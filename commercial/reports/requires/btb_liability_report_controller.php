<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$seource_des_array=array(3=>"Non EPZ",4=>"Non EPZ",5=>"Abroad",6=>"Abroad",11=>"EPZ",12=>"EPZ");


if ($action=="load_drop_down_year")
{
	$sql=sql_select("select lc_year as lc_sc_year from com_export_lc where beneficiary_name='$data' and status_active=1 and is_deleted=0  union all select sc_year as lc_sc_year from com_sales_contract where beneficiary_name='$data' and status_active=1 and is_deleted=0");
	foreach($sql as $row)
	{
		$lc_sc_year[$row[csf("lc_sc_year")]]=$row[csf("lc_sc_year")];
	}
	echo create_drop_down( "hide_year", 100, $lc_sc_year,"", 1, "-- Select --", $selected, "",0 );
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
                    <input type="button" name="show" id="show" onClick="show_list_view(document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<?  echo $company_id; ?>+'_'+<?  echo $lien_bank;?>+'_'+document.getElementById('cbo_year').value,'search_file_info','search_div_file','btb_liability_report_controller','setFilterGrid(\'list_view\',-1)')" class="formbutton" style="width:100px;" value="Show" />
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
	//and lc_category in('03','3','04','4','05','5','06','6','11','12')
	$sql="select max(id) as id, TO_NUMBER(lc_category) as lc_category from com_btb_lc_master_details where importer_id=$company_id and lc_category is not null and lc_category not in('0','40','30') group by TO_NUMBER(lc_category)";
	//echo $sql;
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
                    <td title="<? echo $row[csf("lc_category")]; ?>"><? echo $seource_des; ?></td>
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
	$txt_lc_category=str_replace("'","",$txt_lc_category);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$report_type=str_replace("'","",$report_type);
	$company_arr=return_library_array( "select company_name,id from  lib_company",'id','company_name');
	$bank_arr=return_library_array( "select bank_name,id from lib_bank",'id','bank_name');
	$suplier_name_arr=return_library_array( "select id,supplier_name from  lib_supplier",'id','supplier_name');
	 //echo $hide_year;die;
	 //echo $cbo_company_name.'____'.$cbo_buyer_name.'____'.$cbo_lein_bank.'____'.$txt_file_no; die;lc_year sc_year
	$sql_cond="";
	if($cbo_lein_bank>0) $sql_cond=" and c.issuing_bank_id=$cbo_lein_bank";
	if(trim($txt_file_no)!="") $sql_cond.="  and a.internal_file_no=$txt_file_no";
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
	
	
	if($hide_year>0) $hide_year =$hide_year; else $hide_year="%%";
	$btb_sql="select a.id as lc_sc_id, a.contract_no as lc_sc_no, a.internal_file_no, c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.item_category_id, c.issuing_bank_id, c.supplier_id, sum(c.lc_value) as btb_value, c.lc_category, sum(case when c.payterm_id=1 then c.lc_value else 0 end) as at_site, sum(case when c.payterm_id=2 then c.lc_value else 0 end) as usence, sum(case when c.payterm_id=3 then c.lc_value else 0 end) as case_in_advance, sum(case when c.payterm_id=4 then c.lc_value else 0 end) as open_account, 1 as type
	from com_sales_contract a, com_btb_export_lc_attachment b,  com_btb_lc_master_details c
	where a.id=b.lc_sc_id and b.import_mst_id=c.id and b.is_lc_sc=1 and a.beneficiary_name=$cbo_company_name and a.sc_year like '$hide_year' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $sql_cond and c.ref_closing_status<>1
	group by a.id, a.contract_no, a.internal_file_no, c.id,  c.lc_number, c.lc_date, c.item_category_id, c.issuing_bank_id, c.supplier_id,  c.lc_category 
	union all 
	select a.id as lc_sc_id, a.export_lc_no as lc_sc_no, a.internal_file_no, c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.item_category_id, c.issuing_bank_id, c.supplier_id,  sum(c.lc_value) as btb_value,  c.lc_category, sum(case when c.payterm_id=1 then c.lc_value else 0 end) as at_site, sum(case when c.payterm_id=2 then c.lc_value else 0 end) as usence, sum(case when c.payterm_id=3 then c.lc_value else 0 end) as case_in_advance, sum(case when c.payterm_id=4 then c.lc_value else 0 end) as open_account, 2 as type
	from com_export_lc a, com_btb_export_lc_attachment b,  com_btb_lc_master_details c 
	where a.id=b.lc_sc_id and b.import_mst_id=c.id and b.is_lc_sc=0 and beneficiary_name=$cbo_company_name and a.lc_year like '$hide_year'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $sql_cond and c.ref_closing_status<>1
	group by a.id, a.export_lc_no, a.internal_file_no, c.id,  c.lc_number, c.lc_date, c.item_category_id, c.issuing_bank_id, c.supplier_id,  c.lc_category order by internal_file_no";
	//echo $btb_sql;die;
	$sql_result=sql_select($btb_sql);$btb_data_arr=array();$btb_id_all="";
	foreach($sql_result as $row)
	{
		$btb_id_all.=$row[csf("btb_lc_sc_id")].",";
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]["lc_sc_no"]=$row[csf("lc_sc_no")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]["internal_file_no"]=$row[csf("internal_file_no")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]["btb_lc_number"]=$row[csf("btb_lc_number")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]["btb_lc_date"]=$row[csf("btb_lc_date")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]["item_category_id"]=$row[csf("item_category_id")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]["issuing_bank_id"]=$row[csf("issuing_bank_id")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]["supplier_id"]=$row[csf("supplier_id")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]["lc_category"]=$row[csf("lc_category")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]["btb_value"]=$row[csf("btb_value")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]["at_site"]=$row[csf("at_site")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]["usence"]=$row[csf("usence")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]["case_in_advance"]=$row[csf("case_in_advance")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]["open_account"]=$row[csf("open_account")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]["type"]=$row[csf("type")];
		$btb_data_arr[$row[csf("internal_file_no")]][$row[csf("btb_lc_sc_id")]]["btb_lc_sc_id"]=$row[csf("btb_lc_sc_id")];
	}
	$btb_id_all=chop($btb_id_all," , ");
	$btb_id_all=implode(",",array_unique(explode(",",$btb_id_all)));
	if(empty($btb_id_all)) $btb_id_all=0;
	//abs
	if($db_type==0)
	{
		/*$inv_sql=sql_select("select a.id as btb_lc_id,
		sum(case when a.payterm_id=1 and b.company_acc_date !='0000-00-00' and b.company_acc_date !='' then c.current_acceptance_value else 0 end) as com_at_site,
		sum(case when a.payterm_id=2 and b.company_acc_date !='0000-00-00' and b.company_acc_date !='' then c.current_acceptance_value else 0 end) as com_usence,
		sum(case when a.payterm_id=3 and b.company_acc_date !='0000-00-00' and b.company_acc_date !='' then c.current_acceptance_value else 0 end) as com_case_in_advance,
		sum(case when a.payterm_id=4 and b.company_acc_date !='0000-00-00' and b.company_acc_date !='' then c.current_acceptance_value else 0 end) as com_open_account,
		sum(case when a.payterm_id=1 and b.bank_acc_date !='0000-00-00' and b.bank_acc_date !='' then c.current_acceptance_value else 0 end) as bank_at_site,
		sum(case when a.payterm_id=2 and b.bank_acc_date !='0000-00-00' and b.bank_acc_date !='' then c.current_acceptance_value else 0 end) as bank_usence,
		sum(case when a.payterm_id=3 and b.bank_acc_date !='0000-00-00' and b.bank_acc_date !='' then c.current_acceptance_value else 0 end) as bank_case_in_advance,
		sum(case when a.payterm_id=4 and b.bank_acc_date !='0000-00-00' and b.bank_acc_date !='' then c.current_acceptance_value else 0 end) as bank_open_account
		from  com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
		where a.id=b.btb_lc_id and b.id=c.import_invoice_id  and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 and a.id in($btb_id_all)
		group by a.id");*/
		
		$inv_sql=sql_select("select a.id as btb_lc_id,
		sum(case when a.payterm_id=1 and b.company_acc_date !='0000-00-00' and b.company_acc_date !='' then c.current_acceptance_value else 0 end) as com_at_site,
		sum(case when a.payterm_id=2 and b.company_acc_date !='0000-00-00' and b.company_acc_date !='' then c.current_acceptance_value else 0 end) as com_usence,
		sum(case when a.payterm_id=3 and b.company_acc_date !='0000-00-00' and b.company_acc_date !='' then c.current_acceptance_value else 0 end) as com_case_in_advance,
		sum(case when a.payterm_id=4 and b.company_acc_date !='0000-00-00' and b.company_acc_date !='' then c.current_acceptance_value else 0 end) as com_open_account,
		
		sum(case when a.payterm_id=1 and abs(a.lc_category)<>6 and b.bank_acc_date !='0000-00-00' and b.bank_acc_date !='' then c.current_acceptance_value else 0 end) as bank_at_site,
		sum(case when a.payterm_id=2 and abs(a.lc_category)<>6 and b.bank_acc_date !='0000-00-00' and b.bank_acc_date !='' then c.current_acceptance_value else 0 end) as bank_usence,
		sum(case when a.payterm_id=3 and abs(a.lc_category)<>6 and b.bank_acc_date !='0000-00-00' and b.bank_acc_date !='' then c.current_acceptance_value else 0 end) as bank_case_in_advance,
		sum(case when a.payterm_id=4 and abs(a.lc_category)<>6 and b.bank_acc_date !='0000-00-00' and b.bank_acc_date !='' then c.current_acceptance_value else 0 end) as bank_open_account,
		
		sum(case when a.payterm_id=1 and abs(a.lc_category)=6 and b.bill_date !='0000-00-00' and b.bill_date !='' then c.current_acceptance_value else 0 end) as bank_at_site_bill,
		sum(case when a.payterm_id=2 and abs(a.lc_category)=6 and b.bill_date !='0000-00-00' and b.bill_date !='' then c.current_acceptance_value else 0 end) as bank_usence_bill,
		sum(case when a.payterm_id=3 and abs(a.lc_category)=6 and b.bill_date !='0000-00-00' and b.bill_date !='' then c.current_acceptance_value else 0 end) as bank_case_in_advance_bill,
		sum(case when a.payterm_id=4 and abs(a.lc_category)=6 and b.bill_date !='0000-00-00' and b.bill_date !='' then c.current_acceptance_value else 0 end) as bank_open_account_bill
		from  com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
		where a.id=b.btb_lc_id and b.id=c.import_invoice_id  and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 and a.id in($btb_id_all)
		group by a.id");
			
	}
	else
	{
		$inv_sql=sql_select("select a.id as btb_lc_id,
		sum(case when a.payterm_id=1 and b.company_acc_date is not null then c.current_acceptance_value else 0 end) as com_at_site,
		sum(case when a.payterm_id=2 and b.company_acc_date is not null then c.current_acceptance_value else 0 end) as com_usence,
		sum(case when a.payterm_id=3 and b.company_acc_date is not null then c.current_acceptance_value else 0 end) as com_case_in_advance,
		sum(case when a.payterm_id=4 and b.company_acc_date is not null then c.current_acceptance_value else 0 end) as com_open_account,
		
		sum(case when a.payterm_id=1 and abs(a.lc_category)<>6 and b.bank_acc_date is not null then c.current_acceptance_value else 0 end) as bank_at_site,
		sum(case when a.payterm_id=2 and abs(a.lc_category)<>6 and b.bank_acc_date is not null then c.current_acceptance_value else 0 end) as bank_usence,
		sum(case when a.payterm_id=3 and abs(a.lc_category)<>6 and b.bank_acc_date is not null then c.current_acceptance_value else 0 end) as bank_case_in_advance,
		sum(case when a.payterm_id=4 and abs(a.lc_category)<>6 and b.bank_acc_date is not null then c.current_acceptance_value else 0 end) as bank_open_account,
		
		sum(case when a.payterm_id=1 and abs(a.lc_category)=6 and b.bill_date is not null then c.current_acceptance_value else 0 end) as bank_at_site_bill,
		sum(case when a.payterm_id=2 and abs(a.lc_category)=6 and b.bill_date is not null then c.current_acceptance_value else 0 end) as bank_usence_bill,
		sum(case when a.payterm_id=3 and abs(a.lc_category)=6 and b.bill_date is not null then c.current_acceptance_value else 0 end) as bank_case_in_advance_bill,
		sum(case when a.payterm_id=4 and abs(a.lc_category)=6 and b.bill_date is not null then c.current_acceptance_value else 0 end) as bank_open_account_bill
		
		from  com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
		where a.id=b.btb_lc_id and b.id=c.import_invoice_id  and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 and a.id in($btb_id_all)

		group by a.id");
	}
	
	//echo $inv_sql;die;
	$btb_invoice_data=array();
	foreach($inv_sql as $row)
	{
		$btb_invoice_data[$row[csf("btb_lc_id")]]["com_at_site"]=$row[csf("com_at_site")];
		$btb_invoice_data[$row[csf("btb_lc_id")]]["com_usence"]=$row[csf("com_usence")];
		$btb_invoice_data[$row[csf("btb_lc_id")]]["com_case_in_advance"]=$row[csf("com_case_in_advance")];
		$btb_invoice_data[$row[csf("btb_lc_id")]]["com_open_account"]=$row[csf("com_open_account")];
		
		$btb_invoice_data[$row[csf("btb_lc_id")]]["bank_at_site"]=$row[csf("bank_at_site")]+$row[csf("bank_at_site_bill")];
		$btb_invoice_data[$row[csf("btb_lc_id")]]["bank_usence"]=$row[csf("bank_usence")]+$row[csf("bank_usence_bill")];
		$btb_invoice_data[$row[csf("btb_lc_id")]]["bank_case_in_advance"]=$row[csf("bank_case_in_advance")]+$row[csf("bank_case_in_advance_bill")];
		$btb_invoice_data[$row[csf("btb_lc_id")]]["bank_open_account"]=$row[csf("bank_open_account")]+$row[csf("bank_open_account_bill")];
	}
	
	$pay_sql=sql_select("select a.id as btb_lc_id,
				sum(case when a.payterm_id=1 then b.accepted_ammount else 0 end) as at_site,
				sum(case when a.payterm_id=2 then b.accepted_ammount else 0 end) as usence,
				sum(case when a.payterm_id=3 then b.accepted_ammount else 0 end) as case_in_advance,
				sum(case when a.payterm_id=4 then b.accepted_ammount else 0 end) as open_account 
			from  com_btb_lc_master_details a,com_import_payment  b
			where a.id=b.lc_id and b.is_deleted=0 and b.status_active=1 and a.id in($btb_id_all)
			group by a.id");
	//echo $pay_sql;die;
	$btb_pay_data=array();
	foreach($pay_sql as $row)
	{
		$btb_pay_data[$row[csf("btb_lc_id")]]["at_site"]=$row[csf("at_site")];
		$btb_pay_data[$row[csf("btb_lc_id")]]["usence"]=$row[csf("usence")];
		$btb_pay_data[$row[csf("btb_lc_id")]]["case_in_advance"]=$row[csf("case_in_advance")];
		$btb_pay_data[$row[csf("btb_lc_id")]]["open_account"]=$row[csf("open_account")];
	}


		if($db_type==2)
		{
			$lc_item_category_sql=sql_select("Select  LISTAGG( c.item_category_id, ',') WITHIN GROUP (ORDER BY c.item_category_id) as item_category_id , d.id from  com_btb_lc_master_details d,com_btb_lc_pi b, com_pi_item_details c where d.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and d.status_active=1 and b.status_active=1 and c.status_active=1  $company_id group by d.id");	
		}
		else if($db_type==0)
		{
			$lc_item_category_sql=sql_select("Select  group_concat( c.item_category_id) as item_category_id , d.id from  com_btb_lc_master_details d,com_btb_lc_pi b, com_pi_item_details c where d.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and d.status_active=1 and b.status_active=1 and c.status_active=1 $company_id group by d.id");
		}
		$item_category_data=array();
		foreach($lc_item_category_sql as $row)
		{
			$item_category_data[$row[csf("id")]]['item_category_id']=$row[csf("item_category_id")];
		}
	ob_start();
?>
<div style="width:2568px;" id="scroll_body">
<fieldset style="width:100%">
    <table width="2550" cellpadding="0" cellspacing="0" id="caption" align="left">
        <tr>
            <td align="center" width="100%" colspan="9" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
        </tr> 
        <tr>  
            <td align="center" width="100%" colspan="9" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
        </tr> 
    </table>
    <table width="2550" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
        <thead>
            <tr>
                <th width="30" rowspan="2">Sl</th>
                <th width="70" rowspan="2">File No</th>
                <th width="100" rowspan="2">SC/LC No</th>
                <th width="120" rowspan="2">L/C NO.</th>
                <th width="80" rowspan="2">LC Date</th>
                <th width="120" rowspan="2">Item</th>
                <th width="120" rowspan="2">Bank Name</th>
                <th width="160" rowspan="2">Supplier Name</th>
                <th width="100" rowspan="2">Import Source</th>
                <th width="100" rowspan="2">LC Open Value</th>
                <th colspan="4">Pay term Wise LC Vlue</th>
                <th colspan="4">Company Acceptance</th>
                <th colspan="4">Bank Acceptance</th>
                <th colspan="4">Pay Term Wise Payment</th>
                <th width="80" rowspan="2">Total Pay</th>
                <th width="80" rowspan="2">LC Liablity</th>
                <th rowspan="2">ABP Liablity</th>
            </tr>
            <tr>
                <th width="80">At Sight</th>
                <th width="80">Usance</th>
                <th width="80">Cash In Advance</th>
                <th width="80">Opent Account</th>
                <th width="80">At Sight</th>
                <th width="80">Usance</th>
                <th width="80">Cash In Advance</th>
                <th width="80">Opent Account</th>
                <th width="80">At Sight</th>
                <th width="80">Usance</th>
                <th width="80">Cash In Advance</th>
                <th width="80">Opent Account</th>
                <th width="80">At Sight</th>
                <th width="80">Usance</th>
                <th width="80">Cash In Advance</th>
                <th width="80">Opent Account</th>
            </tr>
        </thead>
    </table>
    <div style="width:2568px; overflow-y:scroll; max-height:350px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
    <table width="2550" rules="all" class="rpt_table" align="left" id="" border="1">
    	<tbody>
        <?
		$i=1;
		foreach($btb_data_arr as $file_no=>$value)
		{
			foreach($value as $btb_id=>$val)
			{
				
					$total_pay=$total_bank_accep=$lc_liability=0;
					$total_pay=$btb_pay_data[$btb_id]["at_site"]+$btb_pay_data[$btb_id]["usence"]+$btb_pay_data[$btb_id]["case_in_advance"]+$btb_pay_data[$btb_id]["open_account"];
					$total_bank_accep=$btb_invoice_data[$btb_id]["bank_at_site"]+$btb_invoice_data[$btb_id]["bank_usence"]+$btb_invoice_data[$btb_id]["bank_case_in_advance"]+$btb_invoice_data[$btb_id]["bank_open_account"];
					
					//$lc_liability=$val["btb_value"]-$total_pay;
					$lc_liability=$val["btb_value"]-$total_bank_accep;
					$apb_liability=$total_bank_accep-$total_pay;
				if($report_type==1 ||($report_type == 2 && $lc_liability>=1))
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30" align="center"><? echo $i;?></td>
						<td width="70" align="center"><p><? echo $val["internal_file_no"];?></p></td>
                        <td width="100" align="center"><p><? echo $val["lc_sc_no"];?></p></td>
						<td width="120"><p><? echo $val["btb_lc_number"];?></p></td>
						<td width="80" align="center"><p><? if($val["btb_lc_date"]!="" && $val["btb_lc_date"]!="0000-00-00") echo change_date_format($val["btb_lc_date"]);?></p></td>
						<td width="120"><p><? 
											$itemCategory="";
											$l=1;
											$cat_id_arr=array_unique(explode(",",$item_category_data[$btb_id]['item_category_id']));
											foreach($cat_id_arr as $cat_id)
											{
												if($l!=1) $itemCategory .=", ";
												$itemCategory .=$item_category[$cat_id];
												$l++;
											}
											echo $itemCategory;
						//echo $item_category[$val["item_category_id"]];?></p></td>
						<td width="120"><p><? echo $bank_arr[$val["issuing_bank_id"]];?></p></td>
						<td width="160"><p><? echo $suplier_name_arr[$val["supplier_id"]];?></p></td>
	                    <td width="100"><p><? echo $seource_des_array[$val["lc_category"]*1];?></p></td>
	                    <td width="100" align="right"><? echo number_format($val["btb_value"],2);?></td>
						<td width="80" align="right"><? echo number_format($val["at_site"],2);?></td>
						<td width="80" align="right"><? echo number_format($val["usence"],2);?></td>
						<td width="80" align="right"><? echo number_format($val["case_in_advance"],2);?></td>
						<td width="80" align="right"><? echo number_format($val["open_account"],2);?></td>
						<td width="80" align="right"><? echo number_format($btb_invoice_data[$btb_id]["com_at_site"],2);?></td>
						<td width="80" align="right"><? echo number_format($btb_invoice_data[$btb_id]["com_usence"],2);?></td>
						<td width="80" align="right"><? echo number_format($btb_invoice_data[$btb_id]["com_case_in_advance"],2);?></td>
						<td width="80" align="right"><? echo number_format($btb_invoice_data[$btb_id]["com_open_account"],2);?></td>
						<td width="80" align="right"><? echo number_format($btb_invoice_data[$btb_id]["bank_at_site"],2);?></td>
						<td width="80" align="right"><? echo number_format($btb_invoice_data[$btb_id]["bank_usence"],2);?></td>
						<td width="80" align="right"><? echo number_format($btb_invoice_data[$btb_id]["bank_case_in_advance"],2);?></td>
						<td width="80" align="right"><? echo number_format($btb_invoice_data[$btb_id]["bank_open_account"],2);?></td>
						<td width="80" align="right"><? echo number_format($btb_pay_data[$btb_id]["at_site"],2);?></td>
						<td width="80" align="right"><? echo number_format($btb_pay_data[$btb_id]["usence"],2);?></td>
						<td width="80" align="right"><? echo number_format($btb_pay_data[$btb_id]["case_in_advance"],2);?></td>
						<td width="80" align="right"><? echo number_format($btb_pay_data[$btb_id]["open_account"],2);?></td>
						<td width="80" align="right"><? echo number_format($total_pay,2);?></td>
						<td width="80" align="right"><? echo number_format($lc_liability,2);?></td>
						<td  align="right"><? echo number_format($apb_liability,2);?></td>
					</tr>
					<?
					$i++;
					$file_lc_open+=$val["btb_value"];
					$file_lc_at_site+=$val["at_site"];
					$file_lc_usence+=$val["usence"];
					$file_lc_advance+=$val["case_in_advance"];
					$file_lc_open_account+=$val["open_account"];
					$file_invoice_com_at_site+=$btb_invoice_data[$btb_id]["com_at_site"];
					$file_invoice_com_usence+=$btb_invoice_data[$btb_id]["com_usence"];
					$file_invoice_com_advance+=$btb_invoice_data[$btb_id]["com_case_in_advance"];
					$file_invoice_com_open_account+=$btb_invoice_data[$btb_id]["com_open_account"];
					$file_invoice_bank_at_site+=$btb_invoice_data[$btb_id]["bank_at_site"];
					$file_invoice_bank_usence+=$btb_invoice_data[$btb_id]["bank_usence"];
					$file_invoice_bank_advance+=$btb_invoice_data[$btb_id]["bank_case_in_advance"];
					$file_invoice_bank_open_account+=$btb_invoice_data[$btb_id]["bank_open_account"];
					$file_pay_at_site+=$btb_pay_data[$btb_id]["at_site"];
					$file_pay_usence+=$btb_pay_data[$btb_id]["usence"];
					$file_pay_advance+=$btb_pay_data[$btb_id]["case_in_advance"];
					$file_pay_open_account+=$btb_pay_data[$btb_id]["open_account"];
					$file_total_pay+=$total_pay;
					$file_lc_liability+=$lc_liability;
					$file_apb_liability+=$apb_liability;
				}
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td width="30"><p></p></td>
                <td width="70"><p></p></td>
                <td width="100"><p></p></td>
                <td width="120"><p></p></td>
                <td width="80" align="center"><p></p></td>
                <td width="120"><p></p></td>
                <td width="120"><p></p></td>
                <td width="160" align="right"><p></p></td>
                <td width="100" align="right"><p>File Total:</p></td>
                <td width="100" align="right"><? echo number_format($file_lc_open,2);?></td>
                <td width="80" align="right"><? echo number_format($file_lc_at_site,2);?></td>
                <td width="80" align="right"><? echo number_format($file_lc_usence,2);?></td>
                <td width="80" align="right"><? echo number_format($file_lc_advance,2);?></td>
                <td width="80" align="right"><? echo number_format($file_lc_open_account,2);?></td>
                <td width="80" align="right"><? echo number_format($file_invoice_com_at_site,2);?></td>
                <td width="80" align="right"><? echo number_format($file_invoice_com_usence,2);?></td>
                <td width="80" align="right"><? echo number_format($file_invoice_com_advance,2);?></td>
                <td width="80" align="right"><? echo number_format($file_invoice_com_open_account,2);?></td>
                <td width="80" align="right"><? echo number_format($file_invoice_bank_at_site,2);?></td>
                <td width="80" align="right"><? echo number_format($file_invoice_bank_usence,2);?></td>
                <td width="80" align="right"><? echo number_format($file_invoice_bank_advance,2);?></td>
                <td width="80" align="right"><? echo number_format($file_invoice_bank_open_account,2);?></td>
                <td width="80" align="right"><? echo number_format($file_pay_at_site,2);?></td>
                <td width="80" align="right"><? echo number_format($file_pay_usence,2);?></td>
                <td width="80" align="right"><? echo number_format($file_pay_advance,2);?></td>
                <td width="80" align="right"><? echo number_format($file_pay_open_account,2);?></td>
                <td width="80" align="right"><? echo number_format($file_total_pay,2);?></td>
                <td width="80" align="right"><? echo number_format($file_lc_liability,2);?></td>
                <td  align="right"><? echo number_format($file_apb_liability,2);?></td>
            </tr>
            <?
			$gt_file_lc_open += $file_lc_open;
			$gt_file_lc_at_site+=$file_lc_at_site;
			$gt_file_lc_usence+=$file_lc_usence;
			$gt_file_lc_advance+=$file_lc_advance;
			$gt_file_lc_open_account+=$file_lc_open_account;
			$gt_file_invoice_com_at_site+=$file_invoice_com_at_site;
			$gt_file_invoice_com_usence+=$file_invoice_com_usence;
			$gt_file_invoice_com_advance+=$file_invoice_com_advance;
			$gt_file_invoice_com_open_account+=$file_invoice_com_open_account;
			$gt_file_invoice_bank_at_site+=$file_invoice_bank_at_site;
			$gt_file_invoice_bank_usence+=$file_invoice_bank_usence;
			$gt_file_invoice_bank_advance+=$file_invoice_bank_advance;
			$gt_file_invoice_bank_open_account+=$file_invoice_bank_open_account;
			$gt_file_pay_at_site+=$file_pay_at_site;
			$gt_file_pay_usence+=$file_pay_usence;
			$gt_file_pay_advance+=$file_pay_advance;
			$gt_file_pay_open_account+=$file_pay_open_account;
			$gt_file_total_pay+=$file_total_pay;
			$gt_file_lc_liability+=$file_lc_liability;
			$gt_file_apb_liability+=$file_apb_liability;
			
			$file_lc_open=$file_lc_at_site=$file_lc_usence=$file_lc_advance=$file_lc_open_account=$file_invoice_com_at_site=$file_invoice_com_usence=$file_invoice_com_advance=$file_invoice_com_open_account=$file_invoice_bank_at_site=$file_invoice_bank_usence=$file_invoice_bank_advance=$file_invoice_bank_open_account=$file_pay_at_site=$file_pay_usence=$file_pay_advance=$file_pay_open_account=$file_total_pay=$file_lc_liability=$file_apb_liability=0;
			
		}
		?>
        </tbody>  			
    </table>
    </div>
    <table width="2550" rules="all" border="1" class="rpt_table" align="left" style="margin-bottom:20px;">
        <tfoot>
            <tr align="right">
                <th width="30"></th>
                <th width="70"></th>
                <th width="100"></th>
                <th width="120"></th>
                <th width="80"></th>
                <th width="120"></th>
                <th width="120"></th>
                <th width="160" align="right"></th>
                <th width="100" align="right">Grand Total:</th>
                <th width="100" align="right"><? echo number_format($gt_file_lc_open,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_lc_at_site,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_lc_usence,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_lc_advance,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_lc_open_account,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_invoice_com_at_site,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_invoice_com_usence,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_invoice_com_advance,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_invoice_com_open_account,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_invoice_bank_at_site,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_invoice_bank_usence,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_invoice_bank_advance,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_invoice_bank_open_account,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_pay_at_site,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_pay_usence,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_pay_advance,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_pay_open_account,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_total_pay,2); ?></th>
                <th width="80" align="right"><? echo number_format($gt_file_lc_liability,2); ?></th>
                <th  align="right"><? echo number_format($gt_file_apb_liability,2); ?></th>
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
