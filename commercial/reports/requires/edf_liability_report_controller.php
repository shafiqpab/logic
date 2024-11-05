<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
if ($action=="load_drop_down_year")
{
	$sql=sql_select("select lc_year as lc_sc_year from com_export_lc where beneficiary_name='$data' and status_active=1 and is_deleted=0  union all select sc_year as lc_sc_year from com_sales_contract where beneficiary_name='$data' and status_active=1 and is_deleted=0");
	foreach($sql as $row)
	{
		$lc_sc_year[$row[csf("lc_sc_year")]]=$row[csf("lc_sc_year")];
	}
	echo create_drop_down( "hide_year", 70, $lc_sc_year,"", 1, "-- Select --", $selected, "",0 );
	exit();
}

if ($action=="file_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, '', $unicode);
	extract($_REQUEST);
	//echo $lien_bank;die;
	?>
	<script>
		function js_set_value(str)
		{
			$("#hide_file_no").val(str);
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
                    <input type="button" name="show" id="show" onClick="show_list_view(document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<?  echo $company_id; ?>+'_'+<?  echo $lien_bank;?>+'_'+document.getElementById('cbo_year').value,'search_file_info','search_div_file','edf_liability_report_controller','setFilterGrid(\'list_view\',-1)')" class="formbutton" style="width:100px;" value="Show" />
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
	$lien_bank_id = $ex_data[3];
	$cbo_year = $ex_data[4];
	//echo $cbo_year; die;
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
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
            <thead>
                <th width="60">Sl NO</th>
                <th width="100">File NO</th>
                <th width="100">Year</th>
                <th width="140">Buyer</th>
                <th>Lein Bank</th>
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
                <tr bgcolor="<? echo $bgcolor; ?>"  onclick="js_set_value('<? echo $row[csf("internal_file_no")];?>,<? echo $row[csf("lc_sc_year")];?>')" id="search<? echo $row[csf("id")]; ?>" style="cursor:pointer">
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
            <input type="hidden" id="hide_file_no" name="hide_file_no"  />
        </table>
    </form>
    <script>setFilterGrid('list_view',-1)</script>
    </div>
     <?
}

if($action=="source_surch")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", '', '', $unicode);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			var onclickString=paramArr=functionParam="";
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				onclickString = $('#tr_' + i).attr('onclick');
				paramArr = onclickString.split("'");
				functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
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
				var str_or = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );
				
				if( jQuery.inArray( str_or, selected_no ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str_or );				
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				num 	= num.substr( 0, num.length - 1 );
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				$('#txt_selected_no').val( num );
		}
		
		function frm_close()
		{
			parent.emailwindow.hide();
		}
    </script>
    <?
	$company_id=str_replace("'","",$company_id);
	//$sql="select max(id) as id, lc_category from com_btb_lc_master_details where importer_id=$company_id group by lc_category";
	$sql="select max(id) as id, TO_NUMBER(lc_category) as lc_category from com_btb_lc_master_details where importer_id=$company_id and lc_category is not null and lc_category not in('0','40','30') group by TO_NUMBER(lc_category)";
	$sql_result=sql_select($sql);
	//echo $sql;die;
	?>
    
    <div style="width:100%">
    <table width="480" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
    	<thead>
        	<tr>
                <th width="50">Sl</th>
                <th>Import Source</th>
            </tr>
        </thead>
    </table>
    <div style="width:500px; max-height:320px; overflow-y:scroll;">
    <table width="480" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="list_view" align="left">
        <tbody>
			<?
            $i=1;
            foreach($sql_result as $row)
            {
                if ($i%2==0)
                $bgcolor="#E9F3FF";
                else
                $bgcolor="#FFFFFF";
                $seource_des=$supply_source[$row[csf("lc_category")]*1];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i."_".$row[csf("lc_category")]."_".$seource_des; ?>')" style="cursor:pointer">
                    <td width="50"  align="center"><? echo $i; ?></td>
                    <td><? echo $seource_des; ?></td>
                </tr>
                <?
                $i++;
            }
            ?>
            <tr>
                <td  style="vertical-align:middle; padding-left:20px;" colspan="2"><input type="checkbox" id="all_check" onClick="check_all_data('all_check')" />Check All
                <input type='hidden' id='txt_selected_id' />
                <input type='hidden' id='txt_selected' />
                <input type='hidden' id='txt_selected_no' />
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    <br>
    <div style="width:100%"><p align="center"><input type="button" id="btn_close" class="formbutton" style="width:100px;" value="Close" onClick="frm_close();" ></p></div>
    </div>
	
    <script language="javascript" type="text/javascript">
	var category_no='<? echo $txt_serial_no;?>';
	var category_id='<? echo $txt_lc_category;?>';
	var category_des='<? echo $import_source;?>';
	var cate_ref="";
	if(category_no!="")
	{
		category_no_arr=category_no.split(",");
		category_id_arr=category_id.split(",");
		category_des_arr=category_des.split(",");
		var str_ref="";
		for(var k=0;k<category_no_arr.length; k++)
		{
			cate_ref=category_no_arr[k]+'_'+category_id_arr[k]+'_'+category_des_arr[k];
			js_set_value(cate_ref);
		}
	}
	</script>
    <?
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_lein_bank=str_replace("'","",$cbo_lein_bank); 
	$txt_file_no=str_replace("'","",$txt_file_no);
	$hide_year=str_replace("'","",$hide_year);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_lc_category=str_replace("'","",$txt_lc_category);
	$txt_date_from_m=str_replace("'","",$txt_date_from_m);
	$txt_date_to_m=str_replace("'","",$txt_date_to_m);
	$txt_date_from_com=str_replace("'","",$txt_date_from_com);
	$txt_date_to_com=str_replace("'","",$txt_date_to_com);
	$txt_date_from_bank=str_replace("'","",$txt_date_from_bank);
	$txt_date_to_bank=str_replace("'","",$txt_date_to_bank);
	$txt_date_from_paid=str_replace("'","",$txt_date_from_paid);
	$txt_date_to_paid=str_replace("'","",$txt_date_to_paid);
	$cbo_pending=str_replace("'","",$cbo_pending);  
	$company_arr=return_library_array( "select company_name,id from  lib_company",'id','company_name');
	$bank_arr=return_library_array( "select bank_name,id from lib_bank",'id','bank_name');
	$suplier_name_arr=return_library_array( "select id,supplier_name from  lib_supplier",'id','supplier_name');
	$sql_cond="";
 	if($cbo_pending!=1)
	{
		if($cbo_pending==2)
		{
			if($db_type==0)
			{
				$sql_cond.="  and d.edf_paid_date!='0000-00-00'";
			}
			else
			{
				$sql_cond.="  and d.edf_paid_date IS NOT NULL";
			}
		}
		else
		{
			if($db_type==0)
			{
				$sql_cond.="  and d.edf_paid_date='0000-00-00'";
			}
			else
			{
				$sql_cond.="  and d.edf_paid_date is null";
			}
			
		}
 	}
	
	 //echo $cbo_company_name.'____'.$cbo_buyer_name.'____'.$cbo_lein_bank.'____'.$txt_file_no; die;lc_year sc_year
	//echo $sql_cond;
	//if($cbo_lein_bank>0) $sql_cond=" and c.issuing_bank_id=$cbo_lein_bank";
	if(str_replace("'","",$cbo_lein_bank)>0) $sql_cond.=" and c.issuing_bank_id=$cbo_lein_bank ";
	if(trim($txt_file_no)!="") $sql_cond.="  and a.internal_file_no=$txt_file_no";
	if(trim($txt_lc_category)!="")
	{
		$txt_lc_category_arr=array_unique(explode(",",$txt_lc_category));
		$all_lc_cat="";
		foreach($txt_lc_category_arr as $cat_id)
		{
			$all_lc_cat.="'".$cat_id."'"." , ";
		}
		$all_lc_cat=chop($all_lc_cat, " , ");
		$sql_cond.="  and TO_NUMBER(c.lc_category) in($all_lc_cat)";
	}
	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$sql_cond.="  and c.lc_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."'";
		}
		else
		{
			$sql_cond.="  and c.lc_date between '".change_date_format($txt_date_from,"","",1)."' and '".change_date_format($txt_date_to,"","",1)."'";
		}
	}
	
	if($txt_date_from_m!="" && $txt_date_to_m!="")
	{
		if($db_type==0)
		{
			$sql_cond.="  and d.maturity_date between '".change_date_format($txt_date_from_m,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to_m,"yyyy-mm-dd")."'";
		}
		else
		{
			$sql_cond.="  and d.maturity_date between '".change_date_format($txt_date_from_m,"","",1)."' and '".change_date_format($txt_date_to_m,"","",1)."'";
		}
	}
	
	if($txt_date_from_com!="" && $txt_date_to_com!="")
	{
		if($db_type==0)
		{
			$sql_cond.="  and d.company_acc_date between '".change_date_format($txt_date_from_com,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to_com,"yyyy-mm-dd")."'";
		}
		else
		{
			$sql_cond.="  and d.company_acc_date between '".change_date_format($txt_date_from_com,"","",1)."' and '".change_date_format($txt_date_to_com,"","",1)."'";
		}
	}

	if($txt_date_from_bank!="" && $txt_date_to_bank!="")
	{
		if($db_type==0)
		{
			$sql_cond.="  and d.bank_acc_date between '".change_date_format($txt_date_from_bank,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to_bank,"yyyy-mm-dd")."'";
		}
		else
		{
			$sql_cond.="  and d.bank_acc_date between '".change_date_format($txt_date_from_bank,"","",1)."' and '".change_date_format($txt_date_to_bank,"","",1)."'";
		}
	}
	
	if($txt_date_from_paid!="" && $txt_date_to_paid!="")
	{
		if($db_type==0)
		{
			$sql_cond.="  and d.edf_paid_date between '".change_date_format($txt_date_from_paid,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to_paid,"yyyy-mm-dd")."'";
		}
		else
		{
			$sql_cond.="  and d.edf_paid_date between '".change_date_format($txt_date_from_paid,"","",1)."' and '".change_date_format($txt_date_to_paid,"","",1)."'";
		}
	}
	
	 $payment_data_array=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amt from com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","invoice_id","paid_amt");//die;
	 $sql_payment_atsite="select invoice_id as INVOICE_ID, max(payment_date) as PAYMENT_DATE, sum(accepted_ammount) as PAID_AMT from com_import_payment_com where status_active=1 and is_deleted=0 group by invoice_id";
	 $sql_payment_atsite_result=sql_select($sql_payment_atsite);
	 $payment_atsite_data=array();
	 foreach($sql_payment_atsite_result as $row)
	 {
		 $payment_atsite_data[$row["INVOICE_ID"]]["PAYMENT_DATE"]=$row["PAYMENT_DATE"];
		 $payment_atsite_data[$row["INVOICE_ID"]]["PAID_AMT"]=$row["PAID_AMT"];
	 }
	 unset($sql_payment_atsite_result);
	 
	 $lc_pi_sql="select a.com_btb_lc_master_details_id, b.item_category_id from com_btb_lc_pi a, com_pi_item_details b where a.pi_id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.com_btb_lc_master_details_id, b.item_category_id";
	 $lc_pi_result=sql_select($lc_pi_sql);
	 $lc_pi_category=array();
	 foreach($lc_pi_result as $row)
	 {
		 $lc_pi_category[$row[csf("com_btb_lc_master_details_id")]].=$row[csf("item_category_id")].",";
	 }
	
	if($hide_year>0) $hide_year =$hide_year; else $hide_year="%%";
	$btb_sql="select a.id as lc_sc_id, a.internal_file_no, c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.item_category_id, c.issuing_bank_id, c.supplier_id, c.lc_value as btb_value, d.id as invoice_id, d.invoice_no, d.invoice_date, d.company_acc_date, d.bank_acc_date, d.edf_tenor, d.maturity_date, e.pi_id, e.current_acceptance_value as edf_loan_value, c.lc_category, d.edf_paid_date, 1 as type 
	from com_sales_contract a, com_btb_export_lc_attachment b,  com_btb_lc_master_details c, com_import_invoice_mst d, com_import_invoice_dtls e
	where a.id=b.lc_sc_id and b.import_mst_id=c.id and c.id=e.btb_lc_id and e.import_invoice_id=d.id  and b.is_lc_sc=1 and a.beneficiary_name=$cbo_company_name and a.sc_year like '$hide_year' and d.retire_source=30 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1  $sql_cond
	union all 
	select a.id as lc_sc_id, a.internal_file_no, c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.item_category_id, c.issuing_bank_id, c.supplier_id, c.lc_value as btb_value, d.id as invoice_id, d.invoice_no, d.invoice_date, d.company_acc_date, d.bank_acc_date, d.edf_tenor, d.maturity_date, e.pi_id, e.current_acceptance_value as edf_loan_value, c.lc_category, d.edf_paid_date, 2 as type
	from com_export_lc a, com_btb_export_lc_attachment b,  com_btb_lc_master_details c, com_import_invoice_mst d, com_import_invoice_dtls e
	where a.id=b.lc_sc_id and b.import_mst_id=c.id and c.id=e.btb_lc_id and e.import_invoice_id=d.id and b.is_lc_sc=0 and beneficiary_name=$cbo_company_name and a.lc_year like '$hide_year' and d.retire_source=30  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1  $sql_cond";
	//echo $btb_sql;//die;
	//echo $btb_sql."<br>";
	$sql_result=sql_select($btb_sql);$btb_data_arr=array();$btb_id_all="";
	$btb_data_count=array();$check_data=array();
	foreach($sql_result as $row)
	{
		$all_internal_file_no.=$row[csf("internal_file_no")].",";
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["internal_file_no"]=$row[csf("internal_file_no")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["btb_lc_number"]=$row[csf("btb_lc_number")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["btb_lc_date"]=$row[csf("btb_lc_date")];
		//$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["item_category_id"]=$row[csf("item_category_id")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["item_category_id"]=chop($lc_pi_category[$row[csf("btb_lc_sc_id")]],",");
		
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["issuing_bank_id"]=$row[csf("issuing_bank_id")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["supplier_id"]=$row[csf("supplier_id")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["btb_value"]=$row[csf("btb_value")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["invoice_no"]=$row[csf("invoice_no")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["invoice_date"]=$row[csf("invoice_date")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["company_acc_date"]=$row[csf("company_acc_date")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["bank_acc_date"]=$row[csf("bank_acc_date")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["edf_tenor"]=$row[csf("edf_tenor")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["maturity_date"]=$row[csf("maturity_date")];
		
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["lc_category"]=$row[csf("lc_category")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["edf_paid_date"]=$row[csf("edf_paid_date")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["type"]=$row[csf("type")];
		//$btb_data_count[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]++;
		
		if($check_data[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]=="")
		{
			$check_data[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]=$row[csf("btb_lc_sc_id")];
			$btb_data_count[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]++;
			//$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["edf_loan_value"]+=$row[csf("edf_loan_value")];
		}
		
		if($check_edf_data[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]][$row[csf("pi_id")]]=="")
		{
			$check_edf_data[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]][$row[csf("pi_id")]]=$row[csf("pi_id")];
			$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]][$row[csf("invoice_id")]]["edf_loan_value"]+=$row[csf("edf_loan_value")];
		}
	}
	//echo $btb_data_count[121][684]."jahid<br>";print_r($btb_data_arr);die;
	//echo $btb_data_count[121][684]."jahid";die;
	
	$all_internal_file_no=chop($all_internal_file_no, " , ");
	$all_internal_file_no=implode(",",array_unique(explode(",",$all_internal_file_no)));
	if($all_internal_file_no=="") $all_internal_file_no=0;
	
	$export_sql=sql_select("select  a.internal_file_no, e.id as rlz_dtls_id, max(case when e.account_head=5 then e.document_currency else 0 end) as margine_value, max(case when e.account_head=6 then e.document_currency else 0 end) as erq_value, 1 as type
	from  com_export_lc a, com_export_doc_submission_invo c, com_export_proceed_realization d, com_export_proceed_rlzn_dtls e
	where a.id=c.lc_sc_id and c.doc_submission_mst_id=d.invoice_bill_id and d.id=e.mst_id and c.is_lc=1 and d.is_invoice_bill=1 and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.internal_file_no in($all_internal_file_no)
	group by  a.internal_file_no, e.id 
	union all
	select a.internal_file_no, e.id as rlz_dtls_id, max(case when e.account_head=5 then e.document_currency else 0 end) as margine_value, max(case when e.account_head=6 then e.document_currency else 0 end) as erq_value, 2 as type
	from  com_sales_contract a, com_export_doc_submission_invo c, com_export_proceed_realization d, com_export_proceed_rlzn_dtls e
	where a.id=c.lc_sc_id and c.doc_submission_mst_id=d.invoice_bill_id and d.id=e.mst_id and c.is_lc=2 and d.is_invoice_bill=1 and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.internal_file_no in($all_internal_file_no)
	group by  a.internal_file_no, e.id ");
	//echo $export_sql;
	$export_data=array();
	foreach($export_sql as $row)
	{
		$export_data[$row[csf("internal_file_no")]]["margine_value"]+=$row[csf("margine_value")];
		$export_data[$row[csf("internal_file_no")]]["erq_value"]+=$row[csf("erq_value")];
	}
	
	ob_start();
?>
<div style="width:1790px;" id="scroll_body">
<fieldset style="width:100%">
    <table width="1770" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
        <caption style="font-size:18px; font-weight:bold; color:#1972C4;">
			<? echo $company_arr[str_replace("'","",$cbo_company_name)]."<br/>".$report_title; ?>
        </caption>
        <thead>
            <tr>
                <th width="30">Sl</th>
                <th width="70">File No</th>
                <th width="120">L/C No</th>
                <th width="70">LC Date</th>
                <th width="110">Item</th>
                <th width="110">Bank Name</th>
                <th width="150">Supplier Name</th>
                <th width="100">Import Source</th>
                <th width="80">LC Open Value</th>
                <th width="100">Invoice No</th>
                <th width="70">Invoice Date</th>
                <th width="80">EDF Loan</th>
                <th width="70">Loan Date</th>
                <th width="60">Tenor</th>
                <th width="70">Maturity Date</th>
                <th width="80">Matured Loan</th>
                <th width="70">Loan Paid Date</th>
                <th width="80" >Loan Paid Amount</th>
                <th width="80" >Loan Liability</th>
                <th width="80" >Margin Built</th>
                <th >ERQ Built</th>
            </tr>
        </thead>
    </table>
    <div style="width:1790px; overflow-y:scroll; max-height:350px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
    <table width="1770" rules="all" class="rpt_table" align="left" id="" border="1">
    	<tbody>
        <?
		$i=$k=1;
		$current_date=strtotime(date('d-m-Y'));
		foreach($btb_data_arr as $file_no=>$value_file)
		{
			foreach($value_file as $btb_id=>$value)
			{
				foreach($value as $invoice_id=>$val)
				{
                    if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					if($val["maturity_date"]!="" && $val["maturity_date"]!="0000-00-00") $maturity_date=strtotime($val["maturity_date"]); else $maturity_date="";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    	<?
						if($btb_check_arr[$file_no][$btb_id]=="")
						{
							$btb_check_arr[$file_no][$btb_id]=$btb_id;
							?>
                            <td width="30" align="center" rowspan="<? echo $btb_data_count[$file_no][$btb_id]; ?>"><? echo $k;?></td>
                            <td width="70" align="center" rowspan="<? echo $btb_data_count[$file_no][$btb_id]; ?>"><p><? echo $val["internal_file_no"];?></p></td>
                            <td width="120" rowspan="<? echo $btb_data_count[$file_no][$btb_id]; ?>"><p><? echo $val["btb_lc_number"];?></p></td>
                            <td width="70" align="center" rowspan="<? echo $btb_data_count[$file_no][$btb_id]; ?>"><p><? if($val["btb_lc_date"]!="" && $val["btb_lc_date"]!="0000-00-00") echo change_date_format($val["btb_lc_date"]);?></p></td>
                            <td width="110" rowspan="<? echo $btb_data_count[$file_no][$btb_id]; ?>"><p>
							<?
							$cat_arr=explode(",",$val["item_category_id"]);
							$item_cat="";
							foreach($cat_arr as $cat_id)
							{
								$item_cat.=$item_category[$cat_id].",";
							}
							echo chop($item_cat,",");
							?></p></td>
                            <td width="110" rowspan="<? echo $btb_data_count[$file_no][$btb_id]; ?>"><p><? echo $bank_arr[$val["issuing_bank_id"]];?></p></td>
                            <td width="150" rowspan="<? echo $btb_data_count[$file_no][$btb_id]; ?>"><p><? echo $suplier_name_arr[$val["supplier_id"]];?></p></td>
                            <td width="100" rowspan="<? echo $btb_data_count[$file_no][$btb_id]; ?>"><p><? echo $supply_source[$val["lc_category"]*1];?></p></td>
                            <td width="80" align="right" rowspan="<? echo $btb_data_count[$file_no][$btb_id]; ?>"><? echo number_format($val["btb_value"],2);?></td>
                            <?
							$k++;
							$btb_tot_lc_open+=$val["btb_value"];
						}
						?>
                        <td width="97" style="padding-left:3px;"><p><? echo $val["invoice_no"];?></p></td>
						<td width="70" align="center"><p><? if($val["invoice_date"]!="" && $val["invoice_date"]!="0000-00-00") echo change_date_format($val["invoice_date"]);?></p></td>
						<td width="80" align="right"><? echo number_format($val["edf_loan_value"],2);?></td>
						<td width="70" align="center"><p><? if($val["bank_acc_date"]!="" && $val["bank_acc_date"]!="0000-00-00") echo change_date_format($val["bank_acc_date"]);?></p></td>
						<td width="60" align="center"><p><? echo $val["edf_tenor"];?></p></td>
						<td width="70" align="center"><p><? if($val["maturity_date"]!="" && $val["maturity_date"]!="0000-00-00") echo change_date_format($val["maturity_date"]);?></p></td>
						<td width="80" align="right">
						<? 
						if($maturity_date!="" && ($val["edf_paid_date"]=="" || $val["edf_paid_date"]=="0000-00-00")) 
						{ 
							if($maturity_date<=$current_date) echo number_format($matured_lone=$val["edf_loan_value"],2); 
							else $matured_lone="";
						}   
						?></td>
						<td width="70" align="right"><p><? if($payment_atsite_data[$invoice_id]["PAYMENT_DATE"]!="" && $payment_atsite_data[$invoice_id]["PAYMENT_DATE"]!="0000-00-00") echo change_date_format($payment_atsite_data[$invoice_id]["PAYMENT_DATE"]);?></p></td>
                        <td width="80" align="right">
						<? 
						/*if(($val["edf_paid_date"]!="" && $val["edf_paid_date"]!="0000-00-00")) 
						{
							echo number_format($val["edf_loan_value"],2);
							$lc_total_payment+=$val["edf_loan_value"];
							$file_total_payment+=$val["edf_loan_value"];
							$grand_total_payment+=$val["edf_loan_value"];
						}
						else
						{
							echo "";
						}*/
						echo number_format($payment_atsite_data[$invoice_id]["PAID_AMT"],2);
						$lc_total_payment+=$payment_atsite_data[$invoice_id]["PAID_AMT"];
						$file_total_payment+=$payment_atsite_data[$invoice_id]["PAID_AMT"];
						$grand_total_payment+=$payment_atsite_data[$invoice_id]["PAID_AMT"];
						
						 
						?></td>
                        <td width="80" align="right">
						<? 
						if(($val["edf_paid_date"]=="" || $val["edf_paid_date"]=="0000-00-00")&&($val["bank_acc_date"]!="")&&($val["bank_acc_date"]!="0000-00-00"))
						{
							echo number_format($edf_loan_val=$val["edf_loan_value"],2);
						}
						
						?></td>
                        <td width="80" align="right"></td>
						<td align="right"></td>
					</tr>
					<?
					$i++;
					
					$btb_tot_edf_loan_value+=$val["edf_loan_value"];
					$btb_matured_lone+=$matured_lone;
					$btb_edf_loan_val+=$edf_loan_val;
					$matured_lone=$edf_loan_val=0;
				}
				?>
                <tr bgcolor="#CCCCCC">
                    <td align="center"></td>
                    <td align="center"></td>
                    <td ></td>
                    <td align="center"></td>
                    <td ></td>
                    <td ></td>
                    <td ></td>
                    <td align="right">LC Total:</td>
                    <td align="right"><? echo number_format($btb_tot_lc_open,2);?></td>
                    <td ></td>
                    <td ></td>
                    <td align="right"><? echo number_format($btb_tot_edf_loan_value,2);?></td>
                    <td ></td>
                    <td ></td>
                    <td ></td>
                    <td align="right"><? echo number_format($btb_matured_lone,2);?></td>
                    <td align="right"></td>
                    <td align="right"><? echo number_format($lc_total_payment,2);?></td>
                    <td align="right"><? echo number_format($btb_edf_loan_val,2);?></td>
                    <td align="right"></td>
                    <td align="right"></td>
                </tr>
                <?
				$file_lc_open+=$btb_tot_lc_open;
				$file_edf_loan_value+=$btb_tot_edf_loan_value;
				$file_matured_lone+=$btb_matured_lone;
				$file_edf_loan_val+=$btb_edf_loan_val;
				$btb_tot_lc_open=$btb_tot_edf_loan_value=$btb_matured_lone=$btb_edf_loan_val=$lc_total_payment=0;
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td align="center"></td>
                <td align="center"></td>
                <td ></td>
                <td align="center"></td>
                <td></td>
                <td></td>
                <td ></td>
                <td align="right">File Total:</td>
                <td align="right"><? echo number_format($file_lc_open,2);?></td>
                <td></td>
                <td></td>
                <td align="right"><? echo number_format($file_edf_loan_value,2);?></td>
                <td></td>
                <td></td>
                <td></td>
                <td  align="right"><? echo number_format($file_matured_lone,2);?></td>
                <td align="right"></td>
                <td align="right"><? echo number_format($file_total_payment,2);?></td>
                <td align="right"><? echo number_format($file_edf_loan_val,2);?></td>
                <td align="right"><? echo number_format($export_data[$file_no]["margine_value"],2);?></td>
                <td align="right"><? echo number_format($export_data[$file_no]["erq_value"],2);?></td>
            </tr>
            <?
			$gt_lc_open+=$file_lc_open;
			$gt_edf_loan_value+=$file_edf_loan_value;
			$gt_margine_value+=$export_data[$file_no]["margine_value"];
			$gt_erq_value+=$export_data[$file_no]["erq_value"];
			$gt_matured_lone+=$file_matured_lone;
			$gt_edf_loan_val+=$file_edf_loan_val;
			$file_lc_open=$file_edf_loan_value=$file_matured_lone=$file_edf_loan_val=$file_total_payment=0;
			
		}
		?>
        </tbody>  			
    </table>
    </div>
    <table width="1770" rules="all" border="1" class="rpt_table" align="left" style="margin-bottom:20px;">
        <tfoot>
            <tr>
                <th width="30"></th>
                <th width="70"></th>
                <th width="120"></th>
                <th width="70"></th>
                <th width="110"></th>
                <th width="110"></th>
                <th width="150" align="right"></th>
                <th width="100" align="right">Grand Total:</th>
                <th width="80" align="right"><? echo number_format($gt_lc_open,2);?></th>
                <th width="97" style="padding-left:3px;"></th>
                <th width="70"></th>
                <th width="80" align="right"><? echo number_format($gt_edf_loan_value,2);?></th>
                <th width="70"></th>
                <th width="60"></th>
                <th width="70"></th>
                <th width="80"  align="right"><? echo number_format($gt_matured_lone,2);?></th>
                <th width="70"></th>
                <th width="80" align="right"><? echo number_format($grand_total_payment,2);?></th>
                <th width="80" align="right"><? echo number_format($gt_edf_loan_val,2);?></th>
                <th width="80"  align="right"><? echo number_format($gt_margine_value,2);?></th>
                <th  align="right"><? echo number_format($gt_erq_value,2);?></th>
            </tr>
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
disconnect($con);
?>
