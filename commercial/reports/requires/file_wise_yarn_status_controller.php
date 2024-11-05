<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id=$_SESSION['logic_erp']['user_id'];
require_once('../../../includes/common.php');
require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.yarns.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_lc_year")
{
	$sql="select distinct(lc_year) as lc_sc_year from com_export_lc where beneficiary_name=$data and status_active=1 and is_deleted=0 union select distinct(sc_year) as lc_sc_year from com_sales_contract where beneficiary_name=$data and status_active=1 and is_deleted=0";
	echo create_drop_down( "txt_year", 100,$sql,"lc_sc_year", 1, "-- Select --", 1,"");
	exit();
}
$company_library=return_library_array( "select id, company_name from lib_company",'id','company_name');
$bank_details=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
$buyer_details=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');


if($action=="internal_file_no_search_popup")
{
	echo load_html_head_contents("BTB Liability Coverage Report", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var year_arr=new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
	
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
			}

			if( jQuery.inArray( str[2], year_arr) == -1 ) 
			{
				year_arr.push(str[2]);
			}
			
		
			var id = ''; var year='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			if(year_arr.length<2) 
			{
				var year=year_arr;
			}
			
			$('#internal_file_no').val( id );
			$('#txt_year').val( year );
		}
	
    </script>

</head>

<body>
<div align="center">
	<form name="internal_file_frm" id="internal_file_frm">
		<fieldset style="width:580px;">
            <table width="550" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Enter File No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="internal_file_no" id="internal_file_no" value="" />
                    <input type="hidden" name="txt_year" id="txt_year" value="<? echo $txt_year; ?>" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
							?>
                        </td>                 
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes"  name="txt_internal_file_no" id="txt_internal_file_no" />	
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+'<? echo $lien_bank; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_internal_file_no').value+'**'+document.getElementById('txt_year').value, 'create_file_no_search_list_view', 'search_div', 'file_wise_yarn_status_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="create_file_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	if($data[1]==0) $lien_bank="%%"; else $lien_bank=$data[1];
	if($data[2]==0) $buyer_name="%%"; else $buyer_name=$data[2];
	$internal_file_no="%".trim($data[3])."%";
	$lc_year_cond="";
	$sc_year_cond="";
	if($data[4]>0)
	{
		$lc_year_cond=" and a.lc_year='".$data[4]."'";
		$sc_year_cond=" and b.sc_year='".$data[4]."'";
	}

	if($db_type==0)
	{
		
		$sql="select internal_file_no, lc_year, lien_bank, buyer_name from
		(
			select a.internal_file_no, a.lc_year, a.lien_bank, a.buyer_name from com_export_lc a where a.beneficiary_name='$company_id' and a.buyer_name like '$buyer_name' and a.lien_bank like '$lien_bank' and a.internal_file_no like '$internal_file_no' and a.status_active=1 and a.is_deleted=0 $lc_year_cond group by a.internal_file_no, a.lc_year
		union 
			select b.internal_file_no, b.sc_year, b.lien_bank, b.buyer_name from com_sales_contract b where b.beneficiary_name='$company_id' and b.buyer_name like '$buyer_name' and b.lien_bank like '$lien_bank' and b.internal_file_no like '$internal_file_no' and b.status_active=1 and b.is_deleted=0 $sc_year_cond group by b.internal_file_no, b.sc_year
		)  	 
		com_export_lc group by internal_file_no, lc_year order by internal_file_no ASC";
	}
	else
	{
		$sql="select a.internal_file_no, a.lc_year, max(a.lien_bank) as lien_bank, max(a.buyer_name) as buyer_name from com_export_lc a where a.beneficiary_name='$company_id' and a.buyer_name like '$buyer_name' and a.lien_bank like '$lien_bank' and a.internal_file_no like '$internal_file_no' and a.status_active=1 and a.is_deleted=0 $lc_year_cond group by a.internal_file_no, a.lc_year
			union 
			select b.internal_file_no, b.sc_year, max(b.lien_bank) as lien_bank, max(b.buyer_name) as buyer_name from com_sales_contract b where b.beneficiary_name='$company_id' and b.buyer_name like '$buyer_name' and b.lien_bank like '$lien_bank' and b.internal_file_no like '$internal_file_no' and b.status_active=1 and b.is_deleted=0 $sc_year_cond group by b.internal_file_no, b.sc_year
			order by internal_file_no ASC";
	}
	//echo $sql;
	$arr=array (2=>$buyer_details,3=>$bank_details);
		
	echo create_list_view("tbl_list_search", "File No,Year,Buyer,Lien Bank", "100,100,140","580","230",0, $sql , "js_set_value", "internal_file_no,lc_year", "", 1, "0,0,buyer_name,lien_bank", $arr , "internal_file_no,lc_year,buyer_name,lien_bank", "","",'0,0,0,0','',1) ;
	
   exit(); 
}


//############## test data

/*
$sql = "SELECT account_id, company_id, loan_limit FROM lib_bank_account where account_type=20 and loan_type=0 and status_active=1 and is_deleted=0";
$result = sql_select($sql);

$percent_details = array();
foreach($result as $row)
{
	$percent_details[$row[csf('account_id')]][$row[csf('company_id')]] = $row[csf('loan_limit')];
}

$sql_cost_heads="SELECT company_name, cost_heads, cost_heads_status FROM variable_settings_commercial where variable_list=17 and status_active=1 and is_deleted=0 order by cost_heads";
$result_cost_heads = sql_select($sql_cost_heads);

$cost_heads_fabric_array=array(); $cost_heads_embellish_array=array(); $cost_details=array();
foreach( $result_cost_heads as $row )
{
	if($row[csf('cost_heads_status')]==1)
	{
		if($row[csf('cost_heads')]=="101" || $row[csf('cost_heads')]=="102" || $row[csf('cost_heads')]=="103")
		{
			if (array_key_exists($row[csf('company_name')], $cost_heads_embellish_array)) 
			{
				$cost_heads_embellish_array[$row[csf('company_name')]].=",".substr($row[csf('cost_heads')],-1);
			}
			else
			{
				$cost_heads_embellish_array[$row[csf('company_name')]]=substr($row[csf('cost_heads')],-1);	
			}
		}
		else
		{
			if($row[csf('cost_heads')]!=75 && $row[csf('cost_heads')]!=78)
			{
				if (array_key_exists($row[csf('company_name')], $cost_heads_fabric_array)) 
				{
					$cost_heads_fabric_array[$row[csf('company_name')]].=",".$row[csf('cost_heads')];	
				}
				else
				{
					$cost_heads_fabric_array[$row[csf('company_name')]]=$row[csf('cost_heads')];		
				}
			}
		}
	}
	
	if($row[csf('cost_heads')]=="101" || $row[csf('cost_heads')]=="102" || $row[csf('cost_heads')]=="103")
	{
		$cost_heads=substr($row[csf('cost_heads')],-1);
	}
	else
	{
		$cost_heads=$row[csf('cost_heads')];
	}
	$cost_heads=$row[csf('cost_heads')];
	$cost_details[$row[csf('company_name')]][$cost_heads] = $row[csf('cost_heads_status')];
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


 
$supplier_details=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name'); 
$job_company_library=array(); $ratio_arr=array();
$jobData=sql_select("select job_no, company_name, total_set_qnty from wo_po_details_master");
foreach($jobData as $row)
{
	$job_company_library[$row[csf('job_no')]] = $row[csf('company_name')];
	$ratio_arr[$row[csf('job_no')]] = $row[csf('total_set_qnty')];
}
*/

 
if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_lien_bank=str_replace("'","",$cbo_lien_bank);
	$txt_year=str_replace("'","",$txt_year);
	$txt_internal_file_no=str_replace("'","",$txt_internal_file_no);
	$txt_pi_status=str_replace("'","",$txt_pi_status);
	$sql_lc_cond=$sql_sc_cond="";
	

	
	if($company_name>0) $sql_lc_cond=" and b.beneficiary_name=$company_name";
	//if($company_name>0) $sql_lc_cond=" and b.beneficiary_name=$company_name";
	if($cbo_lien_bank>0) $sql_lc_cond.=" and b.lien_bank=$cbo_lien_bank";
	if($txt_year>0) $sql_lc_cond.=" and b.lc_year='$txt_year'";
	if($txt_internal_file_no!="") $sql_lc_cond.=" and b.internal_file_no in($txt_internal_file_no)";

	if($txt_pi_status==0) $pi_cond="a.approved=$txt_pi_status";
	if($txt_pi_status==1) $pi_cond="a.approved=$txt_pi_status";

/*	if($txt_pi_status==2)
	{
		$one=$txt_pi_status/2;
				$zero=$txt_pi_status%2;
				$pi_cond="a.approved in($one,$zero)";//0 and 1
		}
	if($txt_pi_status==1||$txt_pi_status==0) $pi_cond="a.approved in($txt_pi_status)";*/
	
	if($company_name>0) $sql_sc_cond=" and b.beneficiary_name=$company_name";
	if($cbo_lien_bank>0) $sql_sc_cond.=" and b.lien_bank=$cbo_lien_bank";
	if($txt_year>0) $sql_sc_cond.=" and b.sc_year='$txt_year'";
	if($txt_internal_file_no!="") $sql_sc_cond.=" and b.internal_file_no in($txt_internal_file_no)";
	
	
	 $qry_sqll="select b.internal_file_no, b.id as lc_sc_id, b.lc_year as lc_sc_year, b.export_lc_no as lc_sc_no, b.replacement_lc as converted_type, b.lc_value as lc_sc_val, 1 as type
			from com_export_lc b
			where  b.status_active=1 and b.is_deleted=0 $sql_lc_cond
			
		union all
		
		select b.internal_file_no, b.id as lc_sc_id, b.sc_year as lc_sc_year, b.contract_no as lc_sc_no, b.convertible_to_lc as converted_type, b.contract_value as lc_sc_val, 2 as type
			from com_sales_contract b
			where b.status_active=1 and b.is_deleted=0 $sql_sc_cond";
	$sql=sql_select($qry_sqll);
	$file_data=array();
	foreach($sql as $row)
	{
		if($row[csf("type")]==1) $all_lc_id.=$row[csf("lc_sc_id")].","; else $all_sc_id.=$row[csf("lc_sc_id")].",";
		$file_data[$row[csf("internal_file_no")]]["lc_sc_id"].=$row[csf("lc_sc_id")].",";
		$file_data[$row[csf("internal_file_no")]]["lc_sc_no"].=$row[csf("lc_sc_no")].",";
		$file_data[$row[csf("internal_file_no")]]["lc_sc_year"].=$row[csf("lc_sc_year")].",";
		
		/*if(!in_array($row[csf("internal_file_no")],$file_check))
		{
			if($i>1)
			{
				
				$file_data[$row[csf("internal_file_no")]]["file_value"]=$file_value;
				$sc_value_1_3=$lc_value_1=$balance_1_3_1=$sc_value_2=$lc_value_0_1=$file_value=0;
			}
			$file_check[$row[csf("internal_file_no")]]=$row[csf("internal_file_no")];
			$i++;
			
		}
		
		if($row[csf('type')] == 2 && ($row[csf('converted_type')] ==1 || $row[csf('converted_type')] ==3) ) $sc_value_1_3 += $row[csf('lc_sc_val')];
		if( $row[csf('type')] == 1 && $row[csf('converted_type')] == 1 )$lc_value_1 += $row[csf('lc_sc_val')]; 
		if($row[csf('type')] == 2 && $row[csf('converted_type')] ==2) $sc_value_2 += $row[csf('lc_sc_val')];
		if($row[csf('type')] == 1 &&  $row[csf('converted_type')] == 2  )$lc_value_0_1 += $row[csf('lc_sc_val')];
		
		$balance_1_3_1=$sc_value_1_3-$lc_value_1;
		$file_value=($lc_value_1+ $balance_1_3_1+$sc_value_2+$lc_value_0_1);*/
		
		if($row[csf('type')] == 2)
		{
			$file_data[$row[csf("internal_file_no")]]["file_value"]+= $row[csf('lc_sc_val')];
		}
		else
		{
			if($row[csf('converted_type')] == 2 )
			{
				$file_data[$row[csf("internal_file_no")]]["file_value"]+= $row[csf('lc_sc_val')];
			}
		}
	}
	//print_r($file_data); 
	
	 $order_sql_qry="select b.internal_file_no, b.id as lc_sc_id, b.lc_year as lc_sc_year, b.export_lc_no as lc_sc_no, b.replacement_lc as converted_type, b.lc_value as lc_sc_val, p.wo_po_break_down_id as po_id, p.attached_qnty, p.attached_value, 1 as type
			from com_export_lc_order_info p, com_export_lc b
			where p.com_export_lc_id=b.id and p.status_active=1 and p.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_lc_cond
			
		union all
		
		select b.internal_file_no, b.id as lc_sc_id, b.sc_year as lc_sc_year, b.contract_no as lc_sc_no, b.convertible_to_lc as converted_type, b.contract_value as lc_sc_val, a.wo_po_break_down_id as po_id, a.attached_qnty, a.attached_value, 2 as type
			from com_sales_contract_order_info a, com_sales_contract b
			where a.com_sales_contract_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_sc_cond";
	//echo $order_sql_qry;		
         $order_sql=sql_select($order_sql_qry);
			$po_id_all=array();
	$all_order=array(); 
	foreach($order_sql as $row)
	{
		$po_id_all[]=$row[csf("po_id")];
		$all_order[$row[csf("po_id")]]=$row[csf("po_id")];
		$file_data[$row[csf("internal_file_no")]]["po_id"].=$row[csf("po_id")].",";
		$order_wise_data[$row[csf("internal_file_no")]][$row[csf("po_id")]]+=$row[csf("attached_qnty")];
	}
	$po_arr_cond=array_chunk($po_id_all,1000, true);
	$po_cond_for_in="";
	$ji=0;
	foreach($po_arr_cond as $key=> $value)
	{
		 if($ji==0){
		$po_cond_for_in=implode(",",$value); 
		 }
		 else{
		//$po_cond_for_in.=" or job_no  in(".implode(",",$value).")";
		 }
		 $ji++;
	}
	//echo $po_cond_for_in;
	//print_r($order_wise_data);echo "kaiyum..................."; 
		
	$btb_pi_qnty=return_library_array("select p.com_btb_lc_master_details_id, sum(b.quantity) as btb_qnty from com_pi_item_details b, com_btb_lc_pi p, com_pi_master_details a  where b.pi_id=p.pi_id and a.id=p.pi_id and $pi_cond  group by p.com_btb_lc_master_details_id","com_btb_lc_master_details_id","btb_qnty");
	$btb_pi_wise_qnty_res = sql_select("select p.com_btb_lc_master_details_id, sum(b.quantity) as btb_qnty,sum(b.amount) as btb_amount,p.id,a.id as pi_id
        from com_pi_item_details b, com_btb_lc_pi p, com_pi_master_details a  
        where b.pi_id=p.pi_id and a.id=p.pi_id 
        and b.is_deleted = 0 and b.status_active = 1
        and p.is_deleted = 0 and p.status_active = 1
        and $pi_cond 
        group by p.com_btb_lc_master_details_id, a.id, p.id");
        foreach($btb_pi_wise_qnty_res as $row)
        {
            $btb_pi_wise_qnty[$row[csf("com_btb_lc_master_details_id")]][$row[csf("pi_id")]]["qnty"] +=  $row[csf("btb_qnty")];
            $btb_pi_wise_qnty[$row[csf("com_btb_lc_master_details_id")]][$row[csf("pi_id")]]["amount"] +=  $row[csf("btb_amount")];
        }

        
        
        
        
        /*
	$sql_btb_qry="select a.pi_basis_id,a.item_category_id,a.id as pi_idd,b.internal_file_no, d.id as btb_id, c.current_distribution
				from com_export_lc b, com_btb_export_lc_attachment c, com_btb_lc_master_details d,com_pi_master_details a
				where d.pi_id=a.id and $pi_cond and b.id=c.lc_sc_id and c.import_mst_id=d.id and c.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_lc_cond
		
			union all
		
			select a.pi_basis_id,a.item_category_id,a.id as pi_idd,b.internal_file_no, d.id as btb_id, c.current_distribution
				from com_sales_contract b, com_btb_export_lc_attachment c, com_btb_lc_master_details d ,com_pi_master_details a
				where d.pi_id=a.id and $pi_cond and b.id=c.lc_sc_id and c.import_mst_id=d.id and c.is_lc_sc=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_sc_cond";
	*/
        $sql_btb_qry="select a.pi_basis_id,a.item_category_id,a.id as pi_idd,b.internal_file_no, d.id as btb_id, c.current_distribution
				from com_export_lc b, com_btb_export_lc_attachment c, com_btb_lc_master_details d,com_pi_master_details a,com_btb_lc_pi p
                            where d.id = p.com_btb_lc_master_details_id   and p.pi_id = a.id and $pi_cond and b.id=c.lc_sc_id and c.import_mst_id=d.id and c.is_lc_sc=0 and a.item_category_id = 1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_lc_cond
		
			union all
		
			select a.pi_basis_id,a.item_category_id,a.id as pi_idd,b.internal_file_no, d.id as btb_id, c.current_distribution
				from com_sales_contract b, com_btb_export_lc_attachment c, com_btb_lc_master_details d ,com_pi_master_details a ,com_btb_lc_pi p
                        where d.id = p.com_btb_lc_master_details_id   and p.pi_id = a.id and $pi_cond and b.id=c.lc_sc_id and c.import_mst_id=d.id and c.is_lc_sc=1 and a.item_category_id = 1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_sc_cond";
	
        $sql_btb=sql_select($sql_btb_qry);
	//echo $sql_btb_qry;//die;
	$all_btb_id="";$btb_id_check=array();
	foreach($sql_btb as $row)
	{
                $all_btb_id.=$row[csf("btb_id")].",";
		$file_data[$row[csf("internal_file_no")]]["btb_id"].=$row[csf("btb_id")].",";
		
                $file_data[$row[csf("internal_file_no")]]["pi_idd"].=$row[csf("pi_idd")].",";
		if($btb_id_check[$row[csf("btb_id")]]=="")
                {
                 $file_data[$row[csf("internal_file_no")]]["btb_amt"]+=$row[csf("current_distribution")];   
                }
			$btb_id_check[$row[csf("btb_id")]]=$row[csf("btb_id")];
			$file_data[$row[csf("internal_file_no")]]["btb_qnty"]+=$btb_pi_wise_qnty[$row[csf("btb_id")]][$row[csf("pi_idd")]]['qnty'];
			$file_data[$row[csf("internal_file_no")]]["pi_qty"]+=$btb_pi_wise_qnty[$row[csf("btb_id")]][$row[csf("pi_idd")]]['qnty'];
                        $file_data[$row[csf("internal_file_no")]]["pi_amount"]+=$btb_pi_wise_qnty[$row[csf("btb_id")]][$row[csf("pi_idd")]]['amount'];
			//$file_data[$row[csf("internal_file_no")]]["pi_idd"].=$row[csf("pi_idd")].",";
			$file_data[$row[csf("internal_file_no")]]["pi_basis_id"].=$row[csf("pi_basis_id")].",";
			$file_data[$row[csf("internal_file_no")]]["item_category_id"].=$row[csf("item_category_id")].",";
			$btb_id_data[$row[csf("pi_idd")]]["btb_id"]=$row[csf("btb_id")];
			
	}
	$all_btb_id=implode(",",array_unique(explode(",",chop($all_btb_id,","))));
	//if($all_btb_id=="") $all_btb_id=0;
	unset($btb_id_check);
	//$sql_accep=sql_select("select p.btb_lc_id, sum(p.current_acceptance_value) as current_acceptance_value from com_import_invoice_dtls p,com_pi_master_details a where p.pi_id=a.id and $pi_cond and p.btb_lc_id in($all_btb_id) and p.status_active=1 and p.is_deleted=0 group by btb_lc_id");
        $accep_data=array();$btb_id_check=array();
        
        $sql_accep = sql_select("select b.btb_lc_id,b.current_acceptance_value as current_acceptance_value, e.retire_source,e.edf_paid_date,d.payterm_id,b.id as value_id, 1 as type
        from com_import_invoice_mst e,com_import_invoice_dtls b, com_pi_master_details a, com_btb_lc_master_details d
        where b.pi_id=a.id and e.id = b.import_invoice_id and b.btb_lc_id = d.id
        and $pi_cond and b.btb_lc_id in($all_btb_id) and b.status_active=1 and b.is_deleted=0     
        union all   
        select c.id as btb_lc_id, b.accepted_ammount as current_acceptance_value, null as retire_source,null as edf_paid_date,null as payterm_id,b.id as value_id, 2 as type
        from com_import_payment_mst e, com_import_payment b, com_btb_lc_master_details c, com_import_invoice_dtls d, com_pi_master_details a
        where e.id = b.mst_id and e.lc_id = c.id and e.invoice_id = d.import_invoice_id and d.pi_id = a.id
        and c.payterm_id = 2 and d.btb_lc_id in($all_btb_id) and $pi_cond and b.is_deleted = 0 and b.status_active = 1 
        and c.is_deleted = 0 and c.status_active = 1
        and d.is_deleted = 0 and d.status_active = 1
        and a.is_deleted = 0 and d.status_active = 1");

        
        foreach($sql_accep as $row)
	{
            if($btb_id_check[$row[csf("value_id")]]=="")
            {
                $btb_id_check[$row[csf("btb_lc_id")]]=$row[csf("btb_lc_id")];
                $accep_data[$row[csf("btb_lc_id")]]['accep_amt']+=$row[csf("current_acceptance_value")];
            }
	}
        
/*
$accep_data=array();$btb_id_check=array();
	foreach($sql_accep as $row)
	{
		if($btb_id_check[$row[csf("btb_lc_id")]]=="")
		{
			$btb_id_check[$row[csf("btb_lc_id")]]=$row[csf("btb_lc_id")];
			$accep_data[$row[csf("btb_lc_id")]]['accep_qnty']+=$btb_pi_qnty[$row[csf("btb_lc_id")]];
		}
		$accep_data[$row[csf("btb_lc_id")]]['accep_amt']+=$row[csf("current_acceptance_value")];
	}
        */
	unset($btb_id_check);
	$sql_paid=sql_select("select lc_id, sum(accepted_ammount) as accepted_ammount from com_import_payment  where lc_id in($all_btb_id) and status_active=1 and is_deleted=0 group by lc_id");
	$paid_data=array();
	foreach($sql_paid as $row)
	{
		$paid_data[$row[csf("lc_id")]]+=$row[csf("accepted_ammount")];
	}
	$receive_sql_qry="select a.lc_no, b.order_qnty, b.order_amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=1 and a.receive_basis=1 and a.lc_no>0 and b.transaction_type=1";
	$receive_data=array();
	$receive_sql=sql_select($receive_sql_qry);
	foreach($receive_sql as $row)
	{
		$receive_data[$row[csf("lc_no")]]["order_qnty"]+=$row[csf("order_qnty")];
		$receive_data[$row[csf("lc_no")]]["order_amount"]+=$row[csf("order_amount")];
	}
	$receive_ret_sql_qry="select a.pi_id, b.cons_quantity, b.cons_amount from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=8 and a.pi_id>0 and b.transaction_type=3";
	$receive_ret_data=array();
	$receive_ret_sql=sql_select($receive_ret_sql_qry);
	foreach($receive_ret_sql as $row)
	{
		$btb_id=$btb_id_data[$row[csf("pi_id")]]["btb_id"];
		$receive_ret_data[$btb_id]["order_qnty"]+=$row[csf("cons_quantity")];
		$receive_ret_data[$btb_id]["order_amount"]+=$row[csf("cons_amount")];
		//echo $btb_id;
	}
	//print_r($receive_ret_data);
	$yarn_issue=return_library_array("select po_breakdown_id,
	sum(case when entry_form=3 and trans_type=2  then quantity else 0 end)-sum(case when entry_form=9 and trans_type=4  then quantity else 0 end) as issue_qnty
	 from  order_wise_pro_details where entry_form in(3,9) and trans_type in(2,4) group by po_breakdown_id","po_breakdown_id","issue_qnty");
	$order_plan_cut=return_library_array("select id, plan_cut as plan_cut from wo_po_break_down","id","plan_cut");
	
/*	$condition= new condition();
					 $condition->company_name("=$cbo_company_name");
					 if(str_replace("'","",$cbo_buyer_name)>0){
						  $condition->buyer_name("=$cbo_buyer_name");
					 }
					 if(str_replace("'","",$txt_job_no) !=''){
						  $condition->job_no_prefix_num("=$txt_job_no");
					 }*/
	
	$condition= new condition();
	$condition->company_name("=$company_name");
	
	/*if(str_replace("'","",$txt_internal_file_no)>0){
						  $condition->file_no("=$txt_internal_file_no");
					 }*/
					 
	//if(str_replace("'","",$po_ids_all)!=0){
    $condition->po_id("in($po_cond_for_in)");
 //}
	$condition->init();
	$yarn= new yarn($condition);
	$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
	//echo $yarn->getQuery(); //die;

	$yarn= new yarn($condition);
	$yarn_req_amt_arr=$yarn->getOrderWiseYarnAmountArray();
	//print_r($yarn_req_amt_arr);die;
	//echo $yarn->getQuery(); die;


	//new development
	 $pi_qty_sql="select sum(c.quantity) as pi_quantity,a.internal_file_no from com_pi_item_details c,com_export_lc b,com_pi_master_details a where a.internal_file_no=b.internal_file_no and $pi_cond $sql_lc_cond and a.item_category_id=1 and b.id=c.pi_id group by a.internal_file_no";
	$pi_qty=array();//com_export_lc_order_info p, com_export_lc b, com_pi_master_details a
	foreach($pi_qty_sql as $row)
	{	
	  $pi_qty[$row[csf("internal_file_no")]]['pi_qty']+=$row[csf("pi_quantity")];
	  //$file_data[$row[csf("internal_file_no")]]["pi_qty"].=$row[csf("pi_quantity")].",";
	}
	//print_r($pi_qty);
  	$btb_pi_qnty=return_library_array("select p.com_btb_lc_master_details_id, sum(b.quantity) as btb_qnty from com_pi_item_details b, com_btb_lc_pi p, com_pi_master_details a  where b.pi_id=p.pi_id and a.id=p.pi_id and $pi_cond  group by p.com_btb_lc_master_details_id","com_btb_lc_master_details_id","btb_qnty");
	
	ob_start();
	?>
    <div style="width:1870px;" align="left">
    
    <table width="1850" cellpadding="0" cellspacing="0" id="caption">
        <tr>
           <td align="center" width="100%" colspan="21" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
        </tr> 
        <tr>  
           <td align="center" width="100%" colspan="21" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
        </tr>  
    </table>
    </div>
   <div style="width:2000px;" align="left">
    <table width="1990" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
        <thead>
            <tr>
                <th width="30">Sl</th>
                <th width="60">File No</th>
                <th width="60">Year</th>
                <th width="150">Sales Contract / Export LC No</th>
                <th width="100">Value</th>
                <th width="80">Yarn Req (Kg)</th>
				<th width="80">PI Qty</th>
                <th width="80">Balance Qty</th>
                	<th width="80">PI Value(USD)</th>
                <th width="80">BTB Open (Kg)</th>
                <th width="80">BTB Space (Kg)</th>
                <th width="100">Yarn Req (Amount)</th>
                <th width="100">BTB Open (Amount)</th>
                <th width="100">BTB Space (Amount)</th>
                <th width="80">Receive (Kg)</th>
                <th width="100">Receive (Amount)</th>
                <th width="80">Receive Bal (Kg)</th>
                <th width="100">Receive Bal (Amount)</th>
                <th width="100">Acceptance (Amount)</th>
                <th width="100">Paid Amount</th>
                <th width="100">Bal Payment</th>
                <th width="80">Yarn Issue (kg)</th>
                <th>Yarn Balance</th>
            </tr>
        </thead>
    </table>
    </div>
    <div style="width:2010px; overflow-y: scroll; overflow-x:hidden; max-height:250px;font-size:12px;" id="scroll_body">
    <table width="1990" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
        <tbody>
        
        <?
        
        $i=1;
        foreach($file_data as $file_no=>$val)
        {
            if ($i%2==0)
                $bgcolor="#E9F3FF";
                else
                $bgcolor="#FFFFFF";
				
           
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                <td width="30"><? echo $i; ?></td>
                <td width="60" align="center"><p><? echo $file_no; ?>&nbsp;</p></td>
                <td width="60" align="center"><p><? echo implode(",",array_unique(explode(",",chop($val["lc_sc_year"],",")))); ?>&nbsp;</p></td>
                <td width="150"><p><? echo implode(",",array_unique(explode(",",chop($val["lc_sc_no"],",")))); ?>&nbsp;</p></td>
                <td width="100" align="right"><? echo number_format($val["file_value"],2); $tot_file_value+= $val["file_value"]; ?></td>
                <td width="80" align="right"><p>
				<?
				$all_po_id_arr= array_unique(explode(",",chop($val["po_id"],",")));
				$yarn_required_qnty=$yarn_required_amt=$yarn_issue_qnty=0;
				foreach($all_po_id_arr as $po_id)
				{
					//echo $po_id.'wwwwwwwwwwwwww';
					//$yarn_required_qnty+=($order_wise_data[$file_no][$po_id]*($yarn_req_qty_arr[$po_id]/$order_plan_cut[$po_id]));
					$yarn_required_qnty+=$yarn_req_qty_arr[$po_id];
					//$yarn_required_amt+=($yarn_required_qnty*($yarn_req_amt_arr[$po_id]/$yarn_req_qty_arr[$po_id]));
					$yarn_required_amt+=$yarn_req_amt_arr[$po_id];
					$yarn_issue_qnty+=$yarn_issue[$po_id];
				}
				echo number_format($yarn_required_qnty,2); 
				$tot_yarn_required_qnty+=$yarn_required_qnty;
				$btb_space_qnty=$yarn_required_qnty-$val["btb_qnty"];
				//$btb_space_qnty_new=$val["pi_qty"]-$val["btb_qnty"]; //new dev
				$btb_space_amt=$yarn_required_amt-$val["btb_amt"];
				?>&nbsp;</p></td>
                
                <td width="80" align="right">
               
<!--                <a href="##" onClick="file_wise_pi_popup('action_file_wise_pi_popup','<? echo chop($val["pi_idd"],",");?>','<? echo chop($val["pi_basis_id"],","); ?>','<? echo chop($val["item_category_id"],","); ?>','750px')"><div style="word-wrap:break-word; width:70px"><?  echo  number_format($val["pi_qty"],2); $tot_pi_qty+=$val["pi_qty"];?></div></a>-->
                <a href="##" onClick="file_wise_btb_popup('action_file_wise_btb_popup','<? echo chop($val["pi_idd"],",");?>','<? echo chop($val["item_category_id"],","); ?>','750px','pi')"><div style="word-wrap:break-word; width:70px"><?  echo  number_format($val["pi_qty"],2); $tot_pi_qty+=$val["pi_qty"]; ?></div></a>
				</td>
                <td width="80" align="right"><?  echo number_format($yarn_required_qnty-$val["pi_qty"],2); $tot_blnc_qty+=($yarn_required_qnty-$val["pi_qty"]);?></td>
                
                	 <td width="80" align="right"><?  echo number_format($val["pi_amount"],2);$tot_btb_amt_pi_value += $val["pi_amount"];//echo number_format($val["btb_amt"],2);  //$tot_btb_amt_pi_value+=$val["btb_amt"]; ?></td>
                
                <td width="80" align="right">
                
                <a href="##" onClick="file_wise_btb_popup('action_file_wise_btb_popup','<? echo chop($val["pi_idd"],",");?>','<? echo chop($val["item_category_id"],","); ?>','750px','btb')"><div style="word-wrap:break-word; width:70px"><?  echo number_format($val["btb_qnty"],2); $tot_btb_qnty+=$val["btb_qnty"]; ?></div></a>
                
                </td>
                <td width="80" align="right"><?  echo number_format($btb_space_qnty,2); $tot_btb_space_qnty+=$btb_space_qnty; //btb space ?></td>
                <td width="100" align="right"><?  echo number_format($yarn_required_amt,2); $tot_yarn_required_amt+=$yarn_required_amt; //yarn_req_amount?></td>
                <td width="100" align="right"><?  echo number_format($val["btb_amt"],2); $tot_btb_amt+=$val["btb_amt"]; ?></td>
                <td width="100" align="right"><?  echo number_format($btb_space_amt,2); $tot_btb_space_amt+=$btb_space_amt; ?></td>
                <td width="80" align="right">
				<? 
				$btb_id_arr=array_unique(explode(",",chop($val["btb_id"],",")));
				$rcv_qnty=$rcv_ret_qnty=$rcv_ret_amt=$rcv_amt=$accep_qnty=$accep_amt=$paid_amt=0;
				foreach($btb_id_arr as $btb_id)
				{
					$rcv_qnty+=$receive_data[$btb_id]["order_qnty"];
					$rcv_amt+=$receive_data[$btb_id]["order_amount"];
					//$accep_qnty+=$accep_data[$btb_id]['accep_qnty'];
					$accep_amt+=$accep_data[$btb_id]['accep_amt'];
					$paid_amt+=$paid_data[$btb_id];
					$rcv_ret_qnty+=$receive_ret_data[$btb_id]["order_qnty"];
					$rcv_ret_amt+=$receive_ret_data[$btb_id]["order_amount"];
					
				}
				echo number_format($rcv_qnty-$rcv_ret_qnty,2);
				$tot_rcv_qnty+=$rcv_qnty-$rcv_ret_qnty;
				$rcv_bal_qnty=$val["btb_qnty"]-($rcv_qnty-$rcv_ret_qnty);
				$rcv_bal_amt=$val["btb_amt"]-$rcv_amt-$rcv_ret_amt;
				$paid_bal=$accep_amt-$paid_amt;
				$yarn_bal=$rcv_qnty-($yarn_issue_qnty+$rcv_ret_qnty);
				?></td>
                <td width="100" align="right"><?  echo number_format($rcv_amt-$rcv_ret_amt,2); $tot_rcv_amt+=$rcv_amt-$rcv_ret_amt; ?></td>
                <td width="80" align="right"><?  echo number_format($rcv_bal_qnty,2); $tot_rcv_bal_qnty+=$rcv_bal_qnty; ?></td>
                <td width="100" align="right"><?  echo number_format($rcv_bal_amt,2); $tot_rcv_bal_amt+=$rcv_bal_amt; ?></td>
                <td width="100" align="right"><?  echo number_format($accep_amt,2); $tot_accep_amt+=$accep_amt; ?></td>
                <td width="100" align="right"><?  echo number_format($paid_amt,2); $tot_paid_amt+=$paid_amt; ?></td>
                <td width="100" align="right"><?  echo number_format($paid_bal,2); $tot_paid_bal+=$paid_bal; ?></td>
                <td width="80" align="right"><?  echo number_format($yarn_issue_qnty,2); $tot_yarn_issue_qnty+=$yarn_issue_qnty; ?></td>
                <td align="right"><?  echo number_format($yarn_bal,2);  $tot_yarn_bal+=$yarn_bal; ?></td>
            </tr>
            <?
            $i++;
            
        }
        ?>
        </tbody>
    </table>
    </div>
    <table width="1990" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer">
        <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="150" align="right">Total:</th>
                <th width="100" align="right" id="value_tot_file_value"><? echo number_format($tot_file_value,2);?></th>
                <th width="80" align="right" id="value_tot_yarn_required_qnty"><? echo number_format($tot_yarn_required_qnty,2);?></th>
                <th width="80" align="right" id="value_tot_pi_qnty"><? echo number_format($tot_pi_qty,2);?></th>
                <th width="80" align="right" id="value_tot_blnc_qnty"><? echo number_format($tot_blnc_qty,2);?></th>
                <th width="80" align="right" id="value_tot_btb_amt_pi_value"><? echo number_format($tot_btb_amt_pi_value,2);?></th>
                <th width="80" align="right" id="value_tot_btb_qnty"><? echo number_format($tot_btb_qnty,2);?></th>
                <th width="80" align="right" id="value_tot_btb_space_qnty"><? echo number_format($tot_btb_space_qnty,2);?></th>
                <th width="100" align="right" id="value_tot_yarn_required_amt"><? echo number_format($tot_yarn_required_amt,2);?></th>
                <th width="100" align="right" id="value_tot_btb_amt"><? echo number_format($tot_btb_amt,2);?></th>
                <th width="100" align="right" id="value_tot_btb_space_amt"><? echo number_format($tot_btb_space_amt,2);?></th>
                <th width="80" align="right" id="value_tot_rcv_qnty"><? echo number_format($tot_rcv_qnty,2);?></th> 
                <th width="100" align="right" id="value_tot_rcv_amt"><? echo number_format($tot_rcv_amt,2);?></th>
                <th width="80" align="right" id="value_tot_rcv_bal_qnty"><? echo number_format($tot_rcv_bal_qnty,2);?></th>
                <th width="100" align="right" id="value_tot_rcv_bal_amt"><? echo number_format($tot_rcv_bal_amt,2);?></th>
                <th width="100" align="right" id="value_tot_accep_amt"><? echo number_format($tot_accep_amt,2);?></th>
                <th width="100" align="right" id="value_tot_paid_amt"><? echo number_format($tot_paid_amt,2);?></th>
                <th width="100" align="right" id="value_tot_paid_bal"><? echo number_format($tot_paid_bal,2);?></th>
                <th width="80" align="right" id="value_tot_yarn_issue_qnty"><? echo number_format($tot_yarn_issue_qnty,2);?></th>
                <th align="right" id="value_tot_yarn_bal"><? echo number_format($tot_yarn_bal,2);?></th>
            </tr>
        
        </tfoot>
    </table>
 
    <?
	foreach (glob("$user_id*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename==$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows";
	exit();	
}
?>

<?
//---------------------------------------------- Start Pi Details -----------------------------------------------------------------------//

if($action=="action_file_wise_pi_popup")
{
	 
	echo load_html_head_contents("PI details Report Info", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	
	if($item_category_id!=0)
	{
	?>
    <table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_pi_item">
		<?
        if($item_category_id==1)
        {
		?>
        	<thead>
				<? 
                if($pi_basis_id == 1) 
                { 
                ?>
                    <!--<th>&nbsp;</th>-->
                    <th class="must_entry_caption">WO</th>
                <? 
                } 
                ?>
                <th class="must_entry_caption">Color</th>
                <th class="must_entry_caption">Count</th>
                <th class="must_entry_caption">Composition 1st</th>
                <th class="must_entry_caption">Composition 2nd</th>
                <th class="must_entry_caption">Yarn Type</th>
                <th>UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <th></th>
                <? 
                } 
                ?>
            </thead>
            <tbody>
       	<?
            if($pi_basis_id==1)
            {
                $disable="disabled='disabled'";
                $disable_status=1;
            }
            else
            {
                $disable="";
                $disable_status=0;
            }
			
			$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, color_id, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2, yarn_composition_percentage2, yarn_type, uom, quantity, rate, amount from com_pi_item_details where pi_id='$pi_idd' and status_active=1 and is_deleted=0" );

            if($type==1 || count($nameArray)<1)
            {
				$tot_amnt=''; $upcharge=''; $discount=''; $tot_amnt_net=''; $txt_tot_row=0;
            ?>
                <tr class="general" id="row_1">
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                       <!-- <td>
                            <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                        </td>-->
                        <td>
                            <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:100px;"  readonly />			
                            <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                            <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                        </td>
                    <? 
                    } 
                    ?>
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" onFocus="add_auto_complete( 1 )" style="width:<? if( $pi_basis_id== 1 ) echo "80px;"; else echo "100px;"; ?>"  maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                    <?
                        echo create_drop_down( "countName_1", 85, "SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count ASC",'id,yarn_count', 1, '-Select-',0,"",$disable_status); 
                    ?>                         
                    </td>
                    <td>
                        <?
                            if( $pi_basis_id == 1 ) $composition_item1_width = 75; else $composition_item1_width = 85;
                            echo create_drop_down( "yarnCompositionItem1_1",$composition_item1_width, $composition,'', 1, '-Select-',0,"control_composition(1,'comp_one')",$disable_status); 
                        ?>    
                        
                        <input type="text" name="yarnCompositionPercentage1_1" id="yarnCompositionPercentage1_1" class="text_boxes_numeric" value="100" onChange="control_composition(1,'percent_one')" style="width:25px;" disabled/>%
                    </td>
                    <td>
                        <?
							//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
                            if( $pi_basis_id == 1 ) $composition_item2_width = 75; else $composition_item2_width = 85;
                          	echo create_drop_down( "yarnCompositionItem2_1",$composition_item2_width, $composition,'', 1, '-Select-',0,"control_composition(1,'comp_two');",1); 
                        ?>   
                        <input type="text" name="yarnCompositionPercentage2_1" id="yarnCompositionPercentage2_1" class="text_boxes_numeric" value="" style="width:25px;" disabled/>
                    </td>
                    <td>
                        <?
                            if( $pi_basis_id == 1 ) $yarn_type_width = 70; else $yarn_type_width = 80;
                            echo create_drop_down( "yarnType_1",$yarn_type_width,$yarn_type,'', 1,'-Select-',0,"",$disable_status); 
                        ?>    
                    </td>
                    <td>
                        <?
                            if( $pi_basis_id == 1 ) $yarn_uom = 60; else $yarn_uom = 85;
                            echo create_drop_down( "uom_1", $yarn_uom, $unit_of_measurement,'', 0, '',12,'',1,12); 
                        ?>
                         
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" readonly  />
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:45px;" readonly  />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                    <td width="65">
                        <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( 1 )" />
                        <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                    </td> 
                    <?
                    }
                    ?> 
                </tr>
            <?
            }
			else
			{
				$data_array=sql_select("select total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id='$pi_idd'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')]; 
				
				$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count",'id','yarn_count');
				
				$i=1;
				foreach ($nameArray as $row)
				{
				?>
                    <tr class="general" id="row_<? echo $i; ?>">
                        <? 
                        if( $pi_basis_id == 1 ) 
                        { 
                        ?>
                           <!-- <td>
                                <input type="checkbox" name="workOrderChkbox_<? echo $i; ?>" id="workOrderChkbox_<? echo $i; ?>" value="" />
                            </td>-->
                            <td>
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:100px;"  readonly />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                            </td>
                        <? 
                        } 
                        ?>
                        <td>
                            <input type="text" name="colorName_<? echo $i; ?>" id="colorName_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:<? if( $pi_basis_id == 1 ) echo "80px;"; else echo "100px;"; ?>"  maxlength="50" <? echo $disable; ?> value="<? echo $color_library[$row[csf('color_id')]]; ?>"/>
                        </td>
                        <td>
							<?
                                echo create_drop_down( "countName_".$i, 85, $count_arr,'', 1, '-Select-',$row[csf('count_name')],"",$disable_status); 
                            ?>                         
                        </td>
                        <td>
                            <?
                                if( $pi_basis_id == 1 ) $composition_item1_width = 75; else $composition_item1_width = 85;
                                echo create_drop_down( "yarnCompositionItem1_".$i,$composition_item1_width, $composition,'', 1, '-Select-',$row[csf('yarn_composition_item1')],"control_composition($i,'comp_one')",$disable_status); 
                            ?>    
                            <input type="text" name="yarnCompositionPercentage1_<? echo $i; ?>" id="yarnCompositionPercentage1_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('yarn_composition_percentage1')]; ?>" onChange="control_composition(<? echo $i; ?>,'percent_one')" style="width:25px;" disabled/>%
                        </td>
                        <td>
                            <?
                                if( $pi_basis_id == 1 ) $composition_item2_width = 75; else $composition_item2_width = 85;
                                echo create_drop_down( "yarnCompositionItem2_".$i,$composition_item2_width, $composition,'', 1, '-Select-',$row[csf('yarn_composition_item2')],"control_composition($i,'comp_two')",1); 
                            ?>  
                            <input type="text" name="yarnCompositionPercentage2_<? echo $i; ?>" id="yarnCompositionPercentage2_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('yarn_composition_percentage2')]; ?>" onChange="control_composition(<? echo $i; ?>,'percent_two')" style="width:25px;" disabled/>
                        </td>
                        <td>
                            <?
                                if( $pi_basis_id == 1 ) $yarn_type_width = 70; else $yarn_type_width = 80;
                                echo create_drop_down( "yarnType_".$i,$yarn_type_width,$yarn_type,'', 1,'-Select-',$row[csf('yarn_type')],"",$disable_status); 
                            ?>    
                        </td>
                        <td>
                            <?
                                if( $pi_basis_id == 1 ) $yarn_uom = 60; else $yarn_uom = 85;
                                echo create_drop_down( "uom_".$i, $yarn_uom, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1,12); 
                            ?>
                             
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $i; ?>" id="quantity_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" readonly />
                        </td>
                        <td>
                            <input type="text" name="rate_<? echo $i; ?>" id="rate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:45px;" readonly />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $i; ?>" id="amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" readonly/>
                            <input type="hidden" name="updateIdDtls_<? echo $i; ?>" id="updateIdDtls_<? echo $i; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
                        </td>
                        <? 
                        if( $pi_basis_id == 2 ) 
                        { 
                        ?>
                        <td width="65">
                            <input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
                            <input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                        </td> 
                        <?
                        }
                        ?> 
                    </tr>
            	<?		
				$i++;
				}
				$txt_tot_row=$i-1;
			}
			?>
            </tbody>
            <tfoot class="tbl_bottom">
                <tr>
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                       <!-- <td>&nbsp;</td>-->
                        <td>&nbsp;</td>
                    <? 
                    } 
                    ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td>
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $tot_amnt; ?>" style="width:75px;" readonly/>
                    </td>
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                        <td width="65">&nbsp;</td> 
                    <?
                    }
                    ?>
                </tr>
                <tr>
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                       <!-- <td>&nbsp;</td>-->
                        <td>&nbsp;</td>
                    <? 
                    } 
                    ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Upcharge</td>
                    <td align="center">
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $upcharge; ?>" style="width:75px;" readonly onKeyUp="calculate_total_amount(2)"/>
                    </td>
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                        <td width="65">&nbsp;</td> 
                    <?
                    }
                    ?>
                </tr>
                <tr>
                    <? 
                    if( $pi_basis_id == 1 ) 

                    { 
                    ?>
                       <!-- <td>&nbsp;</td>-->
                        <td>&nbsp;</td>
                    <? 
                    } 
                    ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Discount</td>
                    <td align="center">
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $discount; ?>" style="width:75px;" readonly onKeyUp="calculate_total_amount(2)"/>
                    </td>
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                        <td width="65">&nbsp;</td> 
                    <?
                    }
                    ?>
                </tr>
                <tr>
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                       <!-- <td>&nbsp;</td>-->
                        <td>&nbsp;</td>
                    <? 
                    } 
                    ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Net Total</td>
                    <td align="center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $tot_amnt_net; ?>" style="width:75px;" readonly/>
                    </td>
                    <? 
                    if($pi_basis_id == 2 ) 
                    { 
                    ?>
                        <td width="65">&nbsp;</td> 
                    <?
                    }
                    ?>
                </tr>
            </tfoot>
        <?
        }
		else if($item_category_id==2 || $item_category_id==13)
		{
		?>
        	<thead>
				<? 
                if($pi_basis_id == 1) 
                { 
                ?>
                    <th>&nbsp;</th>
                    <th>WO</th>
                <? 
                } 
                ?>
                <th class="must_entry_caption">Construction</th>
                <th>Composition</th>
                <th class="must_entry_caption">Color</th>					
                <th>GSM</th>
                <th class="must_entry_caption">Dia/Width</th>
                <th>UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <th></th>
                <? 
                } 
                ?>
            </thead>
            <tbody>
       		<?
            if($pi_basis_id==1)
            {
                $disable="disabled='disabled'";
                $disable_status=1;
            }
            else
            {
                $disable="";
                $disable_status=0;
            }
			
			$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, determination_id, color_id, fabric_construction, fabric_composition, gsm, dia_width, uom, quantity, rate, amount from com_pi_item_details where pi_id='$pi_idd' and quantity>0 and status_active=1 and is_deleted=0" );

			if($type==1 || count($nameArray)<1)
            {
				$tot_amnt=''; $upcharge=''; $discount=''; $tot_amnt_net=''; $txt_tot_row=0;
            ?>
                <tr class="general" id="row_1">
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                       <!-- <td>
                            <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                        </td>-->
                        <td>
                            <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:100px;"  readonly />			
                            <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                            <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                        </td>
                    <? 
                    } 
                    ?>
                    <td> 
                        <input type="text" name="construction_1" id="construction_1" class="text_boxes" style="width:110px" onDblClick="openmypage_fabricDescription(1)" placeholder="Double Click To Search" readonly <? echo $disable; ?>/> <!--onFocus="add_auto_complete( 1 );"-->
                        <input type="hidden" name="hideDeterminationId_1" id="hideDeterminationId_1" readonly />
                    </td>
                    <td>
                        <input type="text" name="composition_1" id="composition_1" class="text_boxes" value="" style="width:120px" disabled="disabled"/> <!--onFocus="add_auto_complete(1);"-->
                    </td> 
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" onFocus="add_auto_complete( 1 )" value="" style="width:80px" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="gsm_1" id="gsm_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="diawidth_1" id="diawidth_1" class="text_boxes" onFocus="add_auto_complete( 1 )" value="" style="width:70px" <? echo $disable; ?>/>
                    </td>
                     <td>
                        <? 
                            if( $search[0] == 1 ) $yarn_uom = 60; else $yarn_uom = 85;
                            echo create_drop_down( "uom_1", $yarn_uom, $unit_of_measurement,'', 0, '',12,'',1,12);            
                        ?>						 
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" readonly  />
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:45px;" readonly  />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                    <td width="65">
                        <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( 1 )" />
                        <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                    </td> 
                    <?
                    }
                    ?> 
                </tr>
            <?
            }
			else
			{
				$data_array=sql_select("select total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id='$pi_idd'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')]; 
				
				$i=1;
				foreach ($nameArray as $row)
				{
				?>
                    <tr class="general" id="row_<? echo $i; ?>">
                        <? 
                        if( $pi_basis_id == 1 ) 
                        { 
                        ?>
                           <!-- <td>
                                <input type="checkbox" name="workOrderChkbox_<? echo $i; ?>" id="workOrderChkbox_<? echo $i; ?>" value="" />
                            </td>-->
                            <td>
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:100px;"  readonly />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                            </td>
                        <? 
                        } 
                        ?>
                        <td> 
                            <input type="text" name="construction_<? echo $i; ?>" id="construction_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('fabric_construction')]; ?>" style="width:110px" onDblClick="openmypage_fabricDescription(<? echo $i; ?>)" placeholder="Double Click To Search" readonly <? echo $disable; ?>/>
                            <input type="hidden" name="hideDeterminationId_<? echo $i; ?>" id="hideDeterminationId_<? echo $i; ?>" value="<? echo $row[csf('determination_id')]; ?>" readonly />
                        </td>
                        <td>
                            <input type="text" name="composition_<? echo $i; ?>" id="composition_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('fabric_composition')]; ?>" style="width:120px" disabled="disabled"/>
                        </td> 
                        <td>
 							<input type="text" name="colorName_<? echo $i; ?>" id="colorName_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:80px" maxlength="50" <? echo $disable; ?> value="<? echo $color_library[$row[csf('color_id')]]; ?>"/>                        
                        </td>
                        <td>
                            <input type="text" name="gsm_<? echo $i; ?>" id="gsm_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('gsm')]; ?>" style="width:60px"  disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="diawidth_<? echo $i; ?>" id="diawidth_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" value="<? echo $row[csf('dia_width')]; ?>" style="width:70px" <? echo $disable; ?>/>
                        </td>
                        <td>
                            <?
                                if( $pi_basis_id == 1 ) $yarn_uom = 60; else $yarn_uom = 85;
                                echo create_drop_down( "uom_".$i, $yarn_uom, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1,12); 
                            ?>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $i; ?>" id="quantity_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" readonly />
                        </td>
                        <td>
                            <input type="text" name="rate_<? echo $i; ?>" id="rate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:45px;" readonly  />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $i; ?>" id="amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" readonly/>
                            <input type="hidden" name="updateIdDtls_<? echo $i; ?>" id="updateIdDtls_<? echo $i; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
                        </td>
                        <? 
                        if( $pi_basis_id == 2 ) 
                        { 
                        ?>
                        <td width="65">
                            <input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
                            <input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                        </td> 
                        <?
                        }
                        ?> 
                    </tr>
            	<?		
				$i++;
				}
				$txt_tot_row=$i-1;
			}
			?>
        </tbody>	
        <tfoot class="tbl_bottom">
            <tr>
                <? 
                if( $pi_basis_id == 1 ) 
                { 
                ?>
                   <!-- <td>&nbsp;</td>-->
                    <td>&nbsp;</td>
                <? 
                } 
                ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Total</td>
                <td>
                    <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $tot_amnt; ?>" style="width:75px;" readonly/>
                </td>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <td width="65">&nbsp;</td> 
                <?
                }
                ?>
            </tr>
            <tr>
                <? 
                if( $pi_basis_id == 1 ) 
                { 
                ?>
                   <!-- <td>&nbsp;</td>-->
                    <td>&nbsp;</td>
                <? 
                } 
                ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Upcharge</td>
                <td>
                    <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $upcharge; ?>" style="width:75px;" readonly onKeyUp="calculate_total_amount(2)"/>
                </td>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <td width="65">&nbsp;</td> 
                <?
                }
                ?>
            </tr>
            <tr>
                <? 
                if( $pi_basis_id == 1 ) 
                { 
                ?>
                    <!--<td>&nbsp;</td>-->
                    <td>&nbsp;</td>
                <? 
                } 
                ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Discount</td>
                <td>
                    <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $discount; ?>" style="width:75px;" readonly onKeyUp="calculate_total_amount(2)"/>
                </td>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <td width="65">&nbsp;</td> 
                <?
                }
                ?>
            </tr>
            <tr>
                <? 
                if( $pi_basis_id == 1 ) 
                { 
                ?>
                    <!--<td>&nbsp;</td>-->
                    <td>&nbsp;</td>
                <? 
                } 
                ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Net Total</td>
                <td>
                    <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $tot_amnt_net; ?>" style="width:75px;" readonly/>
                </td>
                <? 
                if($pi_basis_id == 2 ) 
                { 
                ?>
                    <td width="65">&nbsp;</td> 
                <?
                }
                ?>
            </tr>
        </tfoot>
        <?     
		}
		else if($item_category_id==3 || $item_category_id==14)
		{
		 ?>
        	<thead>
				<? 
                if($pi_basis_id == 1) 
                { 
                ?>
                    <th>&nbsp;</th>
                    <th>WO</th>
                <? 
                } 
                ?>
                <th>Construction</th>
                <th>Composition</th>
                <th>Color</th>
                <th>Weight</th>
                <th>Width</th>
                <th>UOM</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Amount</th>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <th></th>
                <? 
                } 
                ?>
            </thead>
            <tbody>
       	<?
            if($pi_basis_id==1)
            {
                $disable="disabled='disabled'";
                $disable_status=1;
            }
            else
            {
                $disable="";
                $disable_status=0;
            }
			
			$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, determination_id, color_id, fabric_construction, fabric_composition, weight, dia_width, uom, quantity, rate, amount from com_pi_item_details where pi_id='$pi_idd' and quantity>0 and status_active=1 and is_deleted=0" );

			if($type==1 || count($nameArray)<1)
            {
				$tot_amnt=''; $upcharge=''; $discount=''; $tot_amnt_net=''; $txt_tot_row=0;
            ?>
                <tr class="general" id="row_1">
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                       <!-- <td>
                            <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                        </td>-->
                        <td>
                            <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:100px;"  readonly />			
                            <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                            <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                        </td>
                    <? 
                    } 
                    ?>
                    <td> 
                        <input type="text" name="construction_1" id="construction_1" class="text_boxes" style="width:110px" onDblClick="openmypage_fabricDescription(1)" placeholder="Double Click To Search" readonly <? echo $disable; ?>/>
                        <input type="hidden" name="hideDeterminationId_1" id="hideDeterminationId_1" readonly />
                    </td>
                    <td>
                        <input type="text" name="composition_1" id="composition_1" class="text_boxes" value="" style="width:120px" disabled="disabled"/>
                    </td> 
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" onFocus="add_auto_complete( 1 )" value="" style="width:80px" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="weight_1" id="weight_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="diawidth_1" id="diawidth_1" class="text_boxes" onFocus="add_auto_complete( 1 )" value="" style="width:70px" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <? 
                            if( $search[0] == 1 ) $yarn_uom = 60; else $yarn_uom = 85;
                            echo create_drop_down( "uom_1", $yarn_uom, $unit_of_measurement,'', 0, '',27,'',1,27);            
                        ?>						 
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" readonly />
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:45px;" readonly/>
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                    <td width="65">
                        <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( 1 )" />
                        <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                    </td> 
                    <?
                    }
                    ?> 
                </tr>
            <?
            }
			else
			{
				$data_array=sql_select("select total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id='$pi_idd'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')]; 
				
				$i=1;
				foreach ($nameArray as $row)
				{
				?>
                    <tr class="general" id="row_<? echo $i; ?>">
                        <? 
                        if( $pi_basis_id == 1 ) 
                        { 
                        ?>
                            <!--<td>
                                <input type="checkbox" name="workOrderChkbox_<? echo $i; ?>" id="workOrderChkbox_<? echo $i; ?>" value="" />
                            </td>-->
                            <td>
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:100px;"  readonly />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                            </td>
                        <? 
                        } 
                        ?>
                        <td> 
                            <input type="text" name="construction_<? echo $i; ?>" id="construction_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('fabric_construction')]; ?>" style="width:110px" onDblClick="openmypage_fabricDescription(<? echo $i; ?>)" placeholder="Double Click To Search" readonly <? echo $disable; ?>/>
                            <input type="hidden" name="hideDeterminationId_<? echo $i; ?>" id="hideDeterminationId_<? echo $i; ?>" value="<? echo $row[csf('determination_id')]; ?>" readonly />
                        </td>
                        <td>
                            <input type="text" name="composition_<? echo $i; ?>" id="composition_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('fabric_composition')]; ?>" style="width:120px" disabled="disabled"/>
                        </td> 
                        <td>
 							<input type="text" name="colorName_<? echo $i; ?>" id="colorName_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:80px" maxlength="50" <? echo $disable; ?> value="<? echo $color_library[$row[csf('color_id')]]; ?>"/>                        
                        </td>
                        <td>
                            <input type="text" name="weight_<? echo $i; ?>" id="weight_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('weight')]; ?>" style="width:60px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="diawidth_<? echo $i; ?>" id="diawidth_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" value="<? echo $row[csf('dia_width')]; ?>" style="width:70px" <? echo $disable; ?>/>
                        </td>
                        <td>
                            <?
                                if( $pi_basis_id == 1 ) $yarn_uom = 60; else $yarn_uom = 85;
                                echo create_drop_down( "uom_".$i, $yarn_uom, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1,27); 
                            ?>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $i; ?>" id="quantity_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" readonly />
                        </td>
                        <td>
                            <input type="text" name="rate_<? echo $i; ?>" id="rate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:45px;" readonly  />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $i; ?>" id="amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" readonly/>
                            <input type="hidden" name="updateIdDtls_<? echo $i; ?>" id="updateIdDtls_<? echo $i; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
                        </td>
                        <? 
                        if( $pi_basis_id == 2 ) 
                        { 
                        ?>
                        <td width="65">
                            <input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
                            <input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                        </td> 
                        <?
                        }
                        ?> 
                    </tr>
            	<?		
				$i++;
				}
				$txt_tot_row=$i-1;
			}
			?>
            </tbody>	
			<tfoot class="tbl_bottom">
            <tr>
                <? 
                if( $pi_basis_id == 1 ) 
                { 
                ?>
                   <!-- <td>&nbsp;</td>-->
                    <td>&nbsp;</td>
                <? 
                } 
                ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Total</td>
                <td>
                    <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $tot_amnt; ?>" style="width:75px;" readonly/>
                </td>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <td width="65">&nbsp;</td> 
                <?
                }
                ?>
            </tr>
            <tr>
                <? 
                if( $pi_basis_id == 1 ) 
                { 
                ?>
                   <!-- <td>&nbsp;</td>-->
                    <td>&nbsp;</td>
                <? 
                } 
                ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Upcharge</td>
                <td>
                    <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $upcharge; ?>" style="width:75px;" readonly onKeyUp="calculate_total_amount(2)"/>
                </td>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <td width="65">&nbsp;</td> 
                <?
                }
                ?>
            </tr>
            <tr>
                <? 
                if( $pi_basis_id == 1 ) 
                { 
                ?>
                    <!--<td>&nbsp;</td>-->
                    <td>&nbsp;</td>
                <? 
                } 
                ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Discount</td>
                <td>
                    <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $discount; ?>" style="width:75px;" readonly onKeyUp="calculate_total_amount(2)"/>
                </td>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <td width="65">&nbsp;</td> 
                <?
                }
                ?>
            </tr>
            <tr>
                <? 
                if( $pi_basis_id == 1 ) 
                { 
                ?>
                    <!--<td>&nbsp;</td>-->
                    <td>&nbsp;</td>
                <? 
                } 
                ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Net Total</td>
                <td>
                    <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $tot_amnt_net; ?>" style="width:75px;" readonly/>
                </td>
                <? 
                if($pi_basis_id == 2 ) 
                { 
                ?>
                    <td width="65">&nbsp;</td> 
                <?
                }
                ?>
            </tr>
		</tfoot>
        <?     
		}
		else if($item_category_id==4)
		{
		 ?>
        	<thead>
				<? 
                if($pi_basis_id == 1) 
                { 
                ?>
                    <th>&nbsp;</th>
                    <th>WO</th>
                <? 
                } 
                ?>
                <th class="must_entry_caption">Item Group</th>
                <th class="must_entry_caption">Item Description</th>
                <th>Brand/ Supp. Ref</th>
                <th>Gmts Color</th>
                <th>Gmts Size</th>
                <th class="must_entry_caption">Item Color</th>
                <th>Item Size</th>
                <th>UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <th></th>
                <? 
                } 
                ?>
            </thead>
            <tbody>
       	<?
            if($pi_basis_id==1)
            {
                $disable="disabled='disabled'";
                $disable_status=1;
            }
            else
            {
                $disable="";
                $disable_status=0;
            }
			
			$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, color_id, item_group, item_description, size_id, item_color, item_size, uom, quantity, rate, amount, brand_supplier, booking_without_order from com_pi_item_details where pi_id='$pi_idd' and status_active=1 and is_deleted=0" );

			if($type==1 || count($nameArray)<1)
            {
				$tot_amnt=''; $upcharge=''; $discount=''; $tot_amnt_net=''; $txt_tot_row=0;
            ?>
                <tr class="general" id="row_1">
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                        <!--<td>
                            <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                        </td>-->
                        <td>
                            <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:100px;"  readonly />			
                            <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                            <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                        </td>
                    <? 
                    } 
                    ?>
                    <td> 
						 <?
                            echo create_drop_down( "itemgroupid_1", 110, "SELECT id,item_name FROM lib_item_group WHERE item_category =$item_category_id AND status_active = 1 AND is_deleted = 0 ORDER BY item_name ASC",'id,item_name', 1, '-Select-',0,"get_php_form_data( this.value+'**'+'uom_1', 'get_uom', 'requires/pi_controller' );",$disable_status); 
                         ?>  
                    </td>
                    <td>
                        <input type="text" name="itemdescription_1" id="itemdescription_1" class="text_boxes" value="" style="width:130px" maxlength="200" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="brandSupRef_1" id="brandSupRef_1" class="text_boxes" value="" maxlength="150" style="width:80px" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" onFocus="add_auto_complete( 1 )" value="" style="width:70px" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="sizeName_1" id="sizeName_1" class="text_boxes" value="" onFocus="add_auto_complete( 1 )" style="width:60px;" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="itemColor_1" id="itemColor_1" class="text_boxes" value="" onFocus="add_auto_complete( 1 )" style="width:70px" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="itemSize_1" id="itemSize_1" class="text_boxes" value="" style="width:60px;" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <? 
                            echo create_drop_down( "uom_1", 60, $unit_of_measurement,'', 0, '',0,'',1);            
                        ?>		
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" readonly  />
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:45px;" readonly  />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                        <input type="hidden" name="bookingWithoutOrder_1" id="bookingWithoutOrder_1" readonly/>
                    </td>	
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                    <td width="65">
                        <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( 1 )" />
                        <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                    </td> 
                    <?
                    }
                    ?> 
                </tr>
            <?
            }
			else
			{
				$data_array=sql_select("select total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id='$pi_idd'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')]; 
				
				$i=1;
				foreach ($nameArray as $row)
				{
				?>
                    <tr class="general" id="row_<? echo $i; ?>">
                        <? 
                        if( $pi_basis_id == 1 ) 
                        { 
                        ?>
                           <!-- <td>
                                <input type="checkbox" name="workOrderChkbox_<? echo $i; ?>" id="workOrderChkbox_<? echo $i; ?>" value="" />
                            </td>-->
                            <td>
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:100px;"  readonly />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                            </td>
                        <? 
                        } 
                        ?>
                        <td> 
						 <?
                            echo create_drop_down( "itemgroupid_".$i, 110, "SELECT id,item_name FROM lib_item_group WHERE item_category =$item_category_id AND status_active = 1 AND is_deleted = 0 ORDER BY item_name ASC",'id,item_name', 1, '-Select-',$row[csf('item_group')],"get_php_form_data( this.value+'**'+'uom_$i', 'get_uom', 'requires/pi_controller' );",$disable_status); 
                         ?>  
                    	</td>
                        <td>
                            <input type="text" name="itemdescription_<? echo $i; ?>" id="itemdescription_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('item_description')]; ?>" style="width:130px" maxlength="200" <? echo $disable; ?>/>
                        </td>
                        <td>
                        	<input type="text" name="brandSupRef_<? echo $i; ?>" id="brandSupRef_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('brand_supplier')]; ?>" style="width:80px" maxlength="150" <? echo $disable; ?>/>
                        </td>
                        <td>
 							<input type="text" name="colorName_<? echo $i; ?>" id="colorName_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )"  style="width:70px" maxlength="50" <? echo $disable; ?> value="<? echo $color_library[$row[csf('color_id')]]; ?>"/>                        
                        </td>
                        <td>
                            <input type="text" name="sizeName_<? echo $i; ?>" id="sizeName_<? echo $i; ?>" class="text_boxes" value="<? echo $size_library[$row[csf('size_id')]]; ?>" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:60px;" maxlength="50" <? echo $disable; ?>/>
                        </td>
                        <td>
                            <input type="text" name="itemColor_<? echo $i; ?>" id="itemColor_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" value="<? echo $color_library[$row[csf('item_color')]]; ?>" style="width:70px" maxlength="50" <? echo $disable; ?>/>
                        </td>
                        <td>
                            <input type="text" name="itemSize_<? echo $i; ?>" id="itemSize_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('item_size')]; ?>" style="width:60px;" maxlength="50" <? echo $disable; ?>/>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "uom_".$i, 60, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1); 
                            ?>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $i; ?>" id="quantity_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" readonly />
                        </td>
                        <td>
                            <input type="text" name="rate_<? echo $i; ?>" id="rate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:45px;" readonly  />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $i; ?>" id="amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" readonly/>
                            <input type="hidden" name="updateIdDtls_<? echo $i; ?>" id="updateIdDtls_<? echo $i; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
                            <input type="hidden" name="bookingWithoutOrder_<? echo $i; ?>" id="bookingWithoutOrder_<? echo $i; ?>" value="<? echo $row[csf('booking_without_order')]; ?>"/>
                        </td>
                        <? 
                        if( $pi_basis_id == 2 ) 
                        { 
                        ?>
                        <td width="65">
                            <input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
                            <input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                        </td> 
                        <?
                        }
                        ?> 
                    </tr>
            	<?		
				$i++;
				}
				$txt_tot_row=$i-1;
			}
			?>
            </tbody>	
            <tfoot class="tbl_bottom">
            <tr>
                <? 
                if( $pi_basis_id == 1 ) 
                { 
                ?>
                    <!--<td>&nbsp;</td>-->
                    <td>&nbsp;</td>
                <? 
                } 
                ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Total</td>
                <td>
                    <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $tot_amnt; ?>" style="width:75px;" readonly/>
                </td>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <td width="65">&nbsp;</td> 
                <?
                }
                ?>
            </tr>
            <tr>
                <? 
                if( $pi_basis_id == 1 ) 
                { 
                ?>
                   <!-- <td>&nbsp;</td>-->
                    <td>&nbsp;</td>
                <? 
                } 
                ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Upcharge</td>
                <td>
                    <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $upcharge; ?>" style="width:75px;" readonly onKeyUp="calculate_total_amount(2)"/>
                </td>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <td width="65">&nbsp;</td> 
                <?
                }
                ?>
            </tr>
            <tr>
                <? 
                if( $pi_basis_id == 1 ) 
                { 
                ?>
                   <!-- <td>&nbsp;</td>-->
                    <td>&nbsp;</td>
                <? 
                } 
                ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Discount</td>
                <td>
                    <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $discount; ?>" style="width:75px;" readonly onKeyUp="calculate_total_amount(2)"/>
                </td>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <td width="65">&nbsp;</td> 
                <?
                }
                ?>
            </tr>
            <tr>
                <? 
                if( $pi_basis_id == 1 ) 
                { 
                ?>
                   <!-- <td>&nbsp;</td>-->
                    <td>&nbsp;</td>
                <? 
                } 
                ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Net Total</td>
                <td>
                    <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $tot_amnt_net; ?>" style="width:75px;" readonly/>
                </td>
                <? 
                if($pi_basis_id == 2 ) 
                { 
                ?>
                    <td width="65">&nbsp;</td> 
                <?
                }
                ?>
            </tr>
        </tfoot>
        <?     
		}
		else if($item_category_id==12)
		{
		 ?>
         	<thead>
				<? 
                if($pi_basis_id == 1) 
                { 
                ?>
                    <th>&nbsp;</th>
                    <th>WO</th>
                <? 
                } 
                ?>
                <th class="must_entry_caption">Service Type</th>
                <th class="must_entry_caption">Description</th>
                <th>Gmts Color</th>
                <th>Gmts Size</th>
                <th>Item Color</th>
                <th>Item Size</th>
                <th>UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <th></th>
                <? 
                } 
                ?>
            </thead>
            <tbody>
       	<?
            if($pi_basis_id==1)
            {
                $disable="disabled='disabled'";
                $disable_status=1;
            }
            else
            {
                $disable="";
                $disable_status=0;
            }
			
			$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, color_id, service_type, item_description, size_id, item_color, item_size, uom, quantity, rate, amount from com_pi_item_details where pi_id='$pi_idd' and status_active=1 and is_deleted=0" );

			if($type==1 || count($nameArray)<1)
            {
				$tot_amnt=''; $upcharge=''; $discount=''; $tot_amnt_net=''; $txt_tot_row=0;
            ?>
                <tr class="general" id="row_1">
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                        <!--<td>
                            <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                        </td>-->
                        <td>
                            <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:100px;"  readonly />			
                            <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                            <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                        </td>
                    <? 
                    } 
                    ?>
                    <td> 
                    	<? echo create_drop_down( "servicetype_1", 110, $conversion_cost_head_array,'', 1,'-Select-',0,"",$disable_status); ?> 
                    </td>
                    <td>
                        <input type="text" name="itemdescription_1" id="itemdescription_1" class="text_boxes" value="" style="width:150px" maxlength="200" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" onFocus="add_auto_complete( 1 )" value="" style="width:80px" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="sizeName_1" id="sizeName_1" class="text_boxes" value="" onFocus="add_auto_complete( 1 )" style="width:70px;" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="itemColor_1" id="itemColor_1" class="text_boxes" value="" onFocus="add_auto_complete( 1 )" style="width:80px" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="itemSize_1" id="itemSize_1" class="text_boxes" value="" style="width:70px;" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <? 
                            echo create_drop_down( "uom_1", 60, $unit_of_measurement,'', 0, '',0,'',0);            
                        ?>		
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" readonly />
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:45px;" readonly />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                    <td width="65">
                        <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( 1 )" />
                        <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                    </td> 
                    <?
                    }
                    ?> 
                </tr>
            <?
            }
			else
			{
				$data_array=sql_select("select total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id='$pi_idd'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')]; 
				
				$i=1;
				foreach ($nameArray as $row)
				{
				?>
                    <tr class="general" id="row_<? echo $i; ?>">
                        <? 
                        if( $pi_basis_id == 1 ) 
                        { 
                        ?>
                           <!-- <td>
                                <input type="checkbox" name="workOrderChkbox_<? echo $i; ?>" id="workOrderChkbox_<? echo $i; ?>" value="" />
                            </td>-->
                            <td>
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:100px;"  readonly />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                            </td>
                        <? 
                        } 
                        ?>
                        <td>
							<? echo create_drop_down( "servicetype_".$i, 110, $conversion_cost_head_array,'', 1,'-Select-',$row[csf('service_type')],"",$disable_status); ?>
                        </td>
                        <td>
                            <input type="text" name="itemdescription_<? echo $i; ?>" id="itemdescription_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('item_description')]; ?>" style="width:150px" maxlength="200" <? echo $disable; ?>/>
                        </td>
                        <td>
 							<input type="text" name="colorName_<? echo $i; ?>" id="colorName_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:80px" maxlength="50" <? echo $disable; ?> value="<? echo $color_library[$row[csf('color_id')]]; ?>"/>                        
                        </td>
                        <td>
                            <input type="text" name="sizeName_<? echo $i; ?>" id="sizeName_<? echo $i; ?>" class="text_boxes" value="<? echo $size_library[$row[csf('size_id')]]; ?>" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:70px;" maxlength="50" <? echo $disable; ?>/>
                        </td>
                        <td>
                            <input type="text" name="itemColor_<? echo $i; ?>" id="itemColor_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" value="<? echo $color_library[$row[csf('item_color')]]; ?>" style="width:80px" maxlength="50" <? echo $disable; ?>/>
                        </td>
                        <td>
                            <input type="text" name="itemSize_<? echo $i; ?>" id="itemSize_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('item_size')]; ?>" style="width:70px;" maxlength="50" <? echo $disable; ?>/>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "uom_".$i, 60, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',$disable_status); 
                            ?>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $i; ?>" id="quantity_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" readonly />
                        </td>
                        <td>
                            <input type="text" name="rate_<? echo $i; ?>" id="rate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:45px;" readonly />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $i; ?>" id="amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" readonly/>
                            <input type="hidden" name="updateIdDtls_<? echo $i; ?>" id="updateIdDtls_<? echo $i; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
                        </td>
                        <? 
                        if( $pi_basis_id == 2 ) 
                        { 
                        ?>
                        <td width="65">
                            <input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
                            <input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                        </td> 
                        <?
                        }
                        ?> 
                    </tr>
            	<?		
				$i++;
				}
				$txt_tot_row=$i-1;
			}
			?>
            </tbody>	
            <tfoot class="tbl_bottom">
                <tr>
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                       <!-- <td>&nbsp;</td>-->
                        <td>&nbsp;</td>
                    <? 
                    } 
                    ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td>
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $tot_amnt; ?>" style="width:75px;" readonly/>
                    </td>
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                        <td width="65">&nbsp;</td> 
                    <?
                    }
                    ?>
                </tr>
                <tr>
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                       <!-- <td>&nbsp;</td>-->
                        <td>&nbsp;</td>
                    <? 
                    } 
                    ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Upcharge</td>
                    <td>
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $upcharge; ?>" style="width:75px;" readonly onKeyUp="calculate_total_amount(2)"/>
                    </td>
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                        <td width="65">&nbsp;</td> 
                    <?
                    }
                    ?>
                </tr>
                <tr>
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                        <!--<td>&nbsp;</td>-->
                        <td>&nbsp;</td>
                    <? 
                    } 
                    ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Discount</td>
                    <td>
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $discount; ?>" style="width:75px;" readonly onKeyUp="calculate_total_amount(2)"/>
                    </td>
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                        <td width="65">&nbsp;</td> 
                    <?
                    }
                    ?>
                </tr>
                <tr>
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                       <!-- <td>&nbsp;</td>-->
                        <td>&nbsp;</td>
                    <? 
                    } 
                    ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Net Total</td>
                    <td>
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $tot_amnt_net; ?>" style="width:75px;" readonly/>
                    </td>
                    <? 
                    if($pi_basis_id == 2 ) 
                    { 
                    ?>
                        <td width="65">&nbsp;</td> 
                    <?
                    }
                    ?>
                </tr>
        	</tfoot>
        <?     
		}
		else if($item_category_id==24)
		{
		 ?>
         	<thead>
				<? 
                if($pi_basis_id == 1) 
                { 
                ?>
                    <th>&nbsp;</th>
                    <th>WO</th>
                <? 
                } 
                ?>
                <th class="must_entry_caption">Lot</th>
                <th>Count</th>
                <th>Yarn Description</th>
                <th>Yarn Color</th>
                <th>Color Range</th>
                <th>UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
                <? 
                if($pi_basis_id == 2 ) 
                { 
                ?>
                    <th></th>
                <? 
                } 
                ?>
            </thead>
            <tbody>
       	<?
            if($pi_basis_id==1)
            {
                $disable="disabled='disabled'";
                $disable_status=1;
				$placeholder="";
            }
            else
            {
                $disable="";
                $disable_status=0;
				$placeholder="Doublic Click";
            }
			
			$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count",'id','yarn_count');
			
			$colorIds=array();
			$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, lot_no, yarn_color, count_name, color_range, item_description, item_prod_id, uom, quantity, rate, amount from com_pi_item_details where pi_id='$pi_idd' and status_active=1 and is_deleted=0" );
			

			if($type==1 || count($nameArray)<1)
            {
				$color_array=return_library_array("select id,color_name from lib_color WHERE status_active=1 AND is_deleted=0",'id','color_name');
				$tot_amnt=''; $upcharge=''; $discount=''; $tot_amnt_net=''; $txt_tot_row=0;
            ?>
                <tr class="general" id="row_1">
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                      <!--  <td>
                            <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                        </td>-->
                        <td>
                            <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:115px;"  readonly />			
                            <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                            <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                        </td>
                    <? 
                    } 
                    ?>
                    <td>
                        <input type="text" name="lot_1" id="lot_1" class="text_boxes" value="" style="width:80px" maxlength="200" <? echo $disable; ?> onDblClick="openmypage_item_desc(1);" placeholder="<? echo $placeholder; ?>" readonly/>
                        <input type="hidden" name="itemProdId_1" id="itemProdId_1" readonly value=""/> 
                    </td>
                    <td>
                    	<? echo create_drop_down( "countName_1", 90, $count_arr,'', 1, '-Select-', 0,"",1); ?>
                    </td>
                    <td>
                        <input type="text" name="itemdescription_1" id="itemdescription_1" class="text_boxes" value="" style="width:200px" maxlength="200" disabled/>
                    </td>
                    <td>
                    	<? echo create_drop_down( "yarnColor_1", 110, $color_array,'', 1, '-Select-', 0,"",$disable_status); ?>
                    </td>
                    <td>
                        <? echo create_drop_down( "colorRange_1", 110, $color_range,'', 1, '-Select-', 0,"",$disable_status); ?>
                    </td>
                    <td>
                        <? 
							echo create_drop_down( "uom_1", 60, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1,12); 
                        ?>		
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" readonly />
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:45px;" readonly />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                    <td width="65">
                        <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( 1 )" />
                        <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                    </td> 
                    <?
                    }
                    ?> 
                </tr>
            <?
            }
			else
			{
				foreach ($nameArray as $row)
				{
					$colorIds[$row[csf('yarn_color')]]=$row[csf('yarn_color')];
				}
				
				if(count($colorIds)>0)
				{
					$colorIds=implode(",",$colorIds);
				}
				else $colorIds=0;
				
				$color_array=return_library_array("select id,color_name from lib_color WHERE status_active=1 AND is_deleted=0 and id in($colorIds)",'id','color_name');
				
				$data_array=sql_select("select total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id='$pi_idd'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')]; 
				
				$i=1;
				foreach ($nameArray as $row)
				{
				?>
                    <tr class="general" id="row_<? echo $i; ?>">
                        <? 
                        if( $pi_basis_id == 1 ) 
                        { 
                        ?>
                           <!-- <td>
                                <input type="checkbox" name="workOrderChkbox_<? echo $i; ?>" id="workOrderChkbox_<? echo $i; ?>" value="" />
                            </td>-->
                            <td>
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:115px;"  readonly />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                            </td>
                        <? 
                        } 
                        ?>
                        <td>
                            <input type="text" name="lot_<? echo $i; ?>" id="lot_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('lot_no')];?>" style="width:80px" maxlength="200" <? echo $disable; ?> onDblClick="openmypage_item_desc(<? echo $i; ?>);" placeholder="<? echo $placeholder; ?>" readonly/>
                            <input type="hidden" name="itemProdId_<? echo $i; ?>" id="itemProdId_<? echo $i; ?>" readonly value="<? echo $row[csf('item_prod_id')];?>"/> 
                        </td>
                        <td>
                            <? echo create_drop_down( "countName_".$i, 90, $count_arr,'', 1, '-Select-', $row[csf('count_name')],"",1); ?>
                        </td>
                        <td>
                            <input type="text" name="itemdescription_<? echo $i; ?>" id="itemdescription_<? echo $i; ?>" class="text_boxes" style="width:200px" value="<? echo $row[csf('item_description')];?>" disabled/>
                        </td>
                        <td>
                            <? echo create_drop_down( "yarnColor_".$i, 110, $color_array,'', 1, '-Select-', $row[csf('yarn_color')],"",$disable_status); ?>
                        </td>
                        <td>
                            <? echo create_drop_down( "colorRange_".$i, 110, $color_range,'', 1, '-Select-', $row[csf('color_range')],"",$disable_status); ?>
                        </td>
                        <td>
                            <?
								echo create_drop_down( "uom_".$i, 60, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1,12);
                            ?>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $i; ?>" id="quantity_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" readonly />
                        </td>
                        <td>
                            <input type="text" name="rate_<? echo $i; ?>" id="rate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:45px;" readonly  />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $i; ?>" id="amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" readonly/>
                            <input type="hidden" name="updateIdDtls_<? echo $i; ?>" id="updateIdDtls_<? echo $i; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
                        </td>
                        <? 
                        if( $pi_basis_id == 2 ) 
                        { 
                        ?>
                        <td width="65">
                            <input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
                            <input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                        </td> 
                        <?
                        }
                        ?> 
                    </tr>
            	<?		
				$i++;
				}
				$txt_tot_row=$i-1;
			}
			?>
            </tbody>	
            <tfoot class="tbl_bottom">
                <tr>
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                       <!-- <td>&nbsp;</td>-->
                        <td>&nbsp;</td>
                    <? 
                    } 
                    ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td>
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $tot_amnt; ?>" style="width:75px;" readonly/>
                    </td>
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                        <td width="65">&nbsp;</td> 
                    <?
                    }
                    ?>
                </tr>
                <tr>
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                       <!-- <td>&nbsp;</td>-->
                        <td>&nbsp;</td>
                    <? 
                    } 
                    ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Upcharge</td>
                    <td>
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $upcharge; ?>" style="width:75px;" readonly onKeyUp="calculate_total_amount(2)"/>
                    </td>
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                        <td width="65">&nbsp;</td> 
                    <?
                    }
                    ?>
                </tr>
                <tr>
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                       <!-- <td>&nbsp;</td>-->
                        <td>&nbsp;</td>
                    <? 
                    } 
                    ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Discount</td>
                    <td>
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $discount; ?>" style="width:75px;" readonly onKeyUp="calculate_total_amount(2)"/>
                    </td>
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                        <td width="65">&nbsp;</td> 
                    <?
                    }
                    ?>
                </tr>
                <tr>
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                       <!-- <td>&nbsp;</td>-->
                        <td>&nbsp;</td>
                    <? 
                    } 
                    ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Net Total</td>
                    <td>
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $tot_amnt_net; ?>" style="width:75px;" readonly/>
                    </td>
                    <? 
                    if($pi_basis_id == 2 ) 
                    { 
                    ?>
                        <td width="65">&nbsp;</td> 
                    <?
                    }
                    ?>
                </tr>
        	</tfoot>
        <?     
		}
		else if($item_category_id==25)
		{
		 ?>
         	<thead>
				<? 
                if($pi_basis_id == 1) 
                { 
                ?>
                    <th>&nbsp;</th>
                    <th>WO</th>
                <? 
                } 
                ?>
                <th class="must_entry_caption">Gmts Item</th>
                <th class="must_entry_caption">Embellishment Name</th>
                <th class="must_entry_caption">Embellishment Type</th>
                <th>Gmts Color</th>
                <th>UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
                <? 
                if($pi_basis_id == 2 ) 
                { 
                ?>
                    <th></th>
                <? 
                } 
                ?>
            </thead>
            <tbody>
       	<?
            if($pi_basis_id==1)
            {
                $disable="disabled='disabled'";
                $disable_status=1;
            }
            else
            {
                $disable="";
                $disable_status=0;
            }
			
			$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, color_id, gmts_item_id, embell_name, embell_type, uom, quantity, rate, amount from com_pi_item_details where pi_id='$pi_idd' and status_active=1 and is_deleted=0" );

			if($type==1 || count($nameArray)<1)
            {
				$tot_amnt=''; $upcharge=''; $discount=''; $tot_amnt_net=''; $txt_tot_row=0;
            ?>
                <tr class="general" id="row_1">
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                        <!--<td>
                            <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                        </td>-->
                        <td>
                            <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:100px;"  readonly />			
                            <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                            <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                        </td>
                    <? 
                    } 
                    ?>
                    <td> 
                    	<? echo create_drop_down( "gmtsitem_1", 150, $garments_item,'', 1, '-Select-', 0,"",$disable_status); ?> 
                    </td>
                    <td>
                    	<? echo create_drop_down( "embellname_1", 130, $emblishment_name_array,'', 1, '-Select-', 0,"load_drop_down( 'requires/pi_controller', this.value+'**'+".$disable_status."+'**'+'embelltype_1', 'load_drop_down_embelltype', 'embelltypeTd_1');",$disable_status); ?>
                    </td>
                    <td id="embelltypeTd_1">
                    	<? echo create_drop_down( "embelltype_1", 130, $blank_array,'', 1, '-Select-', 0,"",$disable_status); ?>
                    </td>
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" onFocus="add_auto_complete( 1 )" style="width:90px;" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <? 
							echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 0, '',0,'',1,2);           
                        ?>		
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:81px;" readonly  />
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:50px;" readonly  />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:85px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                    <td width="65">
                        <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( 1 )" />
                        <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                    </td> 
                    <?
                    }
                    ?> 
                </tr>
            <?
            }
			else
			{
				$data_array=sql_select("select total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id='$pi_idd'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')]; 
				
				$i=1;
				foreach ($nameArray as $row)
				{
				?>
                    <tr class="general" id="row_<? echo $i; ?>">
                        <? 
                        if( $pi_basis_id == 1 ) 
                        { 
                        ?>
                           <!-- <td>
                                <input type="checkbox" name="workOrderChkbox_<? echo $i; ?>" id="workOrderChkbox_<? echo $i; ?>" value="" />
                            </td>-->
                            <td>
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:100px;"  readonly />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                            </td>
                        <? 
                        } 
                        ?>
                        <td>
                        	<? echo create_drop_down( "gmtsitem_".$i, 150, $garments_item,'', 1, '-Select-', $row[csf('gmts_item_id')],"",$disable_status); ?>
                        </td>
                        <td>
                            <? echo create_drop_down( "embellname_".$i, 130, $emblishment_name_array,'', 1, '-Select-', $row[csf('embell_name')],"load_drop_down( 'requires/pi_controller', this.value+'**'+".$disable_status."+'**'+'embelltype_$i', 'load_drop_down_embelltype', 'embelltypeTd_$i');",$disable_status); ?>
                        </td>
                        <td id="embelltypeTd_<? echo $i; ?>">
                        	<?
								$emb_arr=array();
								if($row[csf('embell_name')]==1) $emb_arr=$emblishment_print_type;
								else if($row[csf('embell_name')]==2) $emb_arr=$emblishment_embroy_type;
								else if($row[csf('embell_name')]==3) $emb_arr=$emblishment_wash_type;
								else if($row[csf('embell_name')]==4) $emb_arr=$emblishment_spwork_type;
								else $emb_arr=$blank_array;
								 
								echo create_drop_down( "embelltype_".$i, 130, $emb_arr,'', 1, '-Select-', $row[csf('embell_type')],"",$disable_status); 
							?>
                        </td>
                        <td>
                            <input type="text" name="colorName_<? echo $i; ?>" id="colorName_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:90px" maxlength="50" <? echo $disable; ?> value="<? echo $color_library[$row[csf('color_id')]]; ?>"/>
                        </td>
                        <td>
                            <?
								echo create_drop_down( "uom_".$i, 70, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1,2); 
                            ?>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $i; ?>" id="quantity_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:81px;" readonly />
                        </td>
                        <td>
                            <input type="text" name="rate_<? echo $i; ?>" id="rate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:50px;" readonly  />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $i; ?>" id="amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:85px;" readonly/>
                            <input type="hidden" name="updateIdDtls_<? echo $i; ?>" id="updateIdDtls_<? echo $i; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
                        </td>
                        <? 
                        if( $pi_basis_id == 2 ) 
                        { 
                        ?>
                        <td width="65">
                            <input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
                            <input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                        </td> 
                        <?
                        }
                        ?> 
                    </tr>
            	<?		
				$i++;
				}
				$txt_tot_row=$i-1;
			}
			?>
            </tbody>	
            <tfoot class="tbl_bottom">
                <tr>
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                        <!--<td>&nbsp;</td>-->
                        <td>&nbsp;</td>
                    <? 
                    } 
                    ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $tot_amnt; ?>" style="width:85px;" readonly/>
                    </td>
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                        <td width="65">&nbsp;</td> 
                    <?
                    }
                    ?>
                </tr>
                <tr>
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                       <!-- <td>&nbsp;</td>-->
                        <td>&nbsp;</td>
                    <? 
                    } 
                    ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Upcharge</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $upcharge; ?>" style="width:85px;" readonly onKeyUp="calculate_total_amount(2)"/>
                    </td>
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                        <td width="65">&nbsp;</td> 
                    <?
                    }
                    ?>
                </tr>
                <tr>
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                       <!-- <td>&nbsp;</td>-->
                        <td>&nbsp;</td>
                    <? 
                    } 
                    ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Discount</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $discount; ?>" style="width:85px;" readonly onKeyUp="calculate_total_amount(2)"/>
                    </td>
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                        <td width="65">&nbsp;</td> 
                    <?
                    }
                    ?>
                </tr>
                <tr>
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                        <!--<td>&nbsp;</td>-->
                        <td>&nbsp;</td>
                    <? 
                    } 
                    ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $tot_amnt_net; ?>" style="width:85px;" readonly/>
                    </td>
                    <? 
                    if($pi_basis_id == 2 ) 
                    { 
                    ?>
                        <td width="65">&nbsp;</td> 
                    <?
                    }
                    ?>
                </tr>
        	</tfoot>
        <?     
		}
		else
		{
			$item_group_arr=return_library_array( "SELECT id,item_name FROM lib_item_group",'id','item_name');
		 ?>
        	<thead>
				<? 
                if($pi_basis_id == 1) 
                { 
                ?>
                    <th>&nbsp;</th>
                    <th>WO</th>
                <? 
                } 
                ?>
                <th class="must_entry_caption">Item Group</th>
                <th class="must_entry_caption">Item Description</th>
                <th>Item Size</th>
                <th>UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <th></th>
                <? 
                } 
                ?>
            </thead>
            <tbody>
       	<?
            if($pi_basis_id==1)
            {
                $disable="disabled='disabled'";
				$placeholder="";
            }
            else
            {
                $disable="";
				$placeholder="Doublic Click";
            }
			//echo "select id, work_order_no, work_order_id, work_order_dtls_id, item_prod_id, item_group, item_description, item_size, uom, quantity, rate, amount from com_pi_item_details where pi_id='$pi_idd'";
			$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, item_prod_id, item_group, item_description, item_size, uom, quantity, rate, amount from com_pi_item_details where pi_id='$pi_idd' and status_active=1 and is_deleted=0" );

			if($type==1 || count($nameArray)<1)
            {
				$tot_amnt=''; $upcharge=''; $discount=''; $tot_amnt_net=''; $txt_tot_row=0;
            ?>
                <tr class="general" id="row_1">
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                        <!--<td>
                            <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                        </td>-->
                        <td>
                            <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:100px;"  readonly />			
                            <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                            <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                        </td>
                    <? 
                    } 
                    ?>
                    <td> 
						 <?
                            echo create_drop_down( "itemgroupid_1", 130, $item_group_arr,'', 1, '-Select-',0,"get_php_form_data( this.value+'**'+'uom_1', 'get_uom', 'requires/pi_controller' );",1); 
                         ?>  
                    </td>
                    <td>
                        <input type="text" name="itemdescription_1" id="itemdescription_1" class="text_boxes" value="" style="width:200px" maxlength="200" <? echo $disable; ?> onDblClick="openmypage_item_desc(1);" placeholder="<? echo $placeholder; ?>" readonly/>
                        <input type="hidden" name="itemProdId_1" id="itemProdId_1" readonly value=""/> 
                    </td>
                    <td>
                        <input type="text" name="itemSize_1" id="itemSize_1" class="text_boxes" value="" style="width:90px;" maxlength="50" disabled="disabled"/>
                    </td>
                    <td>
                        <? 
                            echo create_drop_down( "uom_1", 80, $unit_of_measurement,'', 0, '',0,'',1);            
                        ?>		
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:90px;" readonly />
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:75px;" readonly  />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:85px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                    <td width="65">
                        <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( 1 )" />
                        <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                    </td> 
                    <?
                    }
                    ?> 
                </tr>
            <?
            }
			else
			{
				$data_array=sql_select("select total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id='$pi_idd'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')]; 
				
				$i=1;
				foreach ($nameArray as $row)
				{
				?>
                    <tr class="general" id="row_<? echo $i; ?>">
                        <? 
                        if( $pi_basis_id == 1 ) 
                        { 
                        ?>
                            <!--<td>
                                <input type="checkbox" name="workOrderChkbox_<? echo $i; ?>" id="workOrderChkbox_<? echo $i; ?>" value="" />
                            </td>-->
                            <td>
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:100px;"  readonly />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                            </td>
                        <? 
                        } 
                        ?>
                        <td> 
						 <?
                            echo create_drop_down( "itemgroupid_".$i, 130, $item_group_arr,'', 1, '-Select-',$row[csf('item_group')],"get_php_form_data( this.value+'**'+'uom_$i', 'get_uom', 'requires/pi_controller' );",1); 
                         ?>  
                    	</td>
                        <td>
                            <input type="text" name="itemdescription_<? echo $i; ?>" id="itemdescription_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('item_description')]; ?>" style="width:200px" maxlength="200" <? echo $disable; ?> onDblClick="openmypage_item_desc(<? echo $i; ?>);" readonly />
                            <input type="hidden" name="itemProdId_<? echo $i; ?>" id="itemProdId_<? echo $i; ?>" readonly value="<? echo $row[csf('item_prod_id')]; ?>"/> 
                        </td>
                        <td>
                            <input type="text" name="itemSize_<? echo $i; ?>" id="itemSize_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('item_size')]; ?>" style="width:90px;" maxlength="50"  disabled="disabled"/>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "uom_".$i, 80, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1); 
                            ?>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $i; ?>" id="quantity_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:90px;" readonly  />
                        </td>
                        <td>
                            <input type="text" name="rate_<? echo $i; ?>" id="rate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:75px;" readonly />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $i; ?>" id="amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:85px;" readonly/>
                            <input type="hidden" name="updateIdDtls_<? echo $i; ?>" id="updateIdDtls_<? echo $i; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
                        </td>
                        <? 
                        if( $pi_basis_id == 2 ) 
                        { 
                        ?>
                        <td width="65">
                            <input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
                            <input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                        </td> 
                        <?
                        }
                        ?> 
                    </tr>
            	<?		
				$i++;
				}
				$txt_tot_row=$i-1;
			}
			?>
            </tbody>	
            <tfoot class="tbl_bottom">
            <tr>
                <? 
                if( $pi_basis_id == 1 ) 
                { 
                ?>
                    <!--<td>&nbsp;</td>-->
                    <td>&nbsp;</td>
                <? 
                } 
                ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Total</td>
                <td style="text-align:center">
                    <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $tot_amnt; ?>" style="width:85px;" readonly/>
                </td>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <td width="65">&nbsp;</td> 
                <?
                }
                ?>
            </tr>
            <tr>
                <? 
                if( $pi_basis_id == 1 ) 
                { 
                ?>
                   <!-- <td>&nbsp;</td>-->
                    <td>&nbsp;</td>
                <? 
                } 
                ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Upcharge</td>
                <td style="text-align:center">
                    <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $upcharge; ?>" style="width:85px;" readonly onKeyUp="calculate_total_amount(2)"/>
                </td>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <td width="65">&nbsp;</td> 
                <?
                }
                ?>
            </tr>
            <tr>
                <? 
                if( $pi_basis_id == 1 ) 
                { 
                ?>
                  <!--  <td>&nbsp;</td>-->
                    <td>&nbsp;</td>
                <? 
                } 
                ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Discount</td>
                <td style="text-align:center">
                    <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $discount; ?>" style="width:85px;" readonly onKeyUp="calculate_total_amount(2)"/>
                </td>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                ?>
                    <td width="65">&nbsp;</td> 
                <?
                }
                ?>
            </tr>
            <tr>
                <? 
                if( $pi_basis_id == 1 ) 
                { 
                ?>
                  <!--  <td>&nbsp;</td>-->
                    <td>&nbsp;</td>
                <? 
                } 
                ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Net Total</td>
                <td style="text-align:center">
                    <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $tot_amnt_net; ?>" style="width:85px;" readonly/>
                </td>
                <? 
                if($pi_basis_id == 2 ) 
                { 
                ?>
                    <td width="65">&nbsp;</td> 
                <?
                }
                ?>
            </tr>
        </tfoot>
        <?     
		}
        ?>
    </table>
    <table width="100%">
        <tr>
            <td class="button_container" colspan="2"></td>
        </tr>
        <tr>
            <td width="15%">
                <?
                if($pi_basis_id == 1) 
                {
                ?>
                    <!--<input form="form_all" type="checkbox" name="check_all" id="check_all" value=""  onclick="fnCheckUnCheckAll(this.checked)"/> Check / Uncheck All-->
                <?
                }
                ?>
                &nbsp;&nbsp;&nbsp;&nbsp;
            </td>
            <td width="80%" align="center"> 
                <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" readonly/> 
                <input type="hidden" name="txt_tot_row" id="txt_tot_row" value="<? echo $txt_tot_row; ?>" readonly/>                      
              
            </td>    
        </tr>				
    </table>
    <?
	}
	exit();
}

//---------------------------------------------- End Pi Details -----------------------------------------------------------------------//


//--------------------------------------------Start BTB Pi Details List----------------------------------------------------------------//
if( $action == 'action_file_wise_btb_popup' ) 
 {	
	
	echo load_html_head_contents("BTB PI details Report Info", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	$pi_mst_id=$pi_idd;
	$cbo_item_category_id=  implode(",",array_filter(array_unique(explode(",",$item_category_id))));//$item_category_id;

	$size_library = return_library_array('SELECT id,size_name FROM lib_size','id','size_name');
	$color_library = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
	?>
    <table class="rpt_table" width="100%" cellspacing="1" rules="all">
    <?
	switch($cbo_item_category_id ) 
		{
			case 1:			//Yarn
			//$sql = "SELECT b.pi_number, a.id, a.color_id, a.count_name, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_composition_item2,a.yarn_composition_percentage2, a.yarn_type,a.uom,a.quantity,a.net_pi_rate as rate, a.net_pi_amount as amount FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id)  AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC";
			$sql = " select c.lc_sc_id,c.is_lc_sc, b.pi_number, a.id, a.color_id, a.count_name, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_composition_item2,
                        a.yarn_composition_percentage2, a.yarn_type,a.uom,a.quantity,a.net_pi_rate as rate, a.net_pi_amount as amount 
                        from com_pi_item_details a, com_pi_master_details b, com_btb_export_lc_attachment c, com_btb_lc_pi d
                        where b.id = a.pi_id and a.pi_id in($pi_mst_id) and c.import_mst_id = d.com_btb_lc_master_details_id
                        and a.status_active = 1 and a.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.pi_id in ($pi_mst_id)
                        group by c.lc_sc_id,c.is_lc_sc, b.pi_number, a.id, a.color_id, a.count_name, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_composition_item2,
                        a.yarn_composition_percentage2, a.yarn_type,a.uom,a.quantity,a.net_pi_rate, a.net_pi_amount
                        order by a.id asc";
                        $data_array=sql_select($sql);	
			 
			$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
                        
			?>
				<thead>
					<tr> 
                                            <? if($pi_btb == "btb"){?>
                                            <th>LC/SC</th>
                                            <? }?>
                    	<th>PI No</th>
						<th>Color</th>
						<th>Count</th>
						<th colspan="4">Composition</th>
						<th>Yarn Type</th>
						<th>UOM</th>
						<th>Quantity</th>
						<th>Rate</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody>
				<?php
                $i = 0;
                foreach($data_array as $row) 
                {
                    $i++;
                    if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
                    else $bgcolor = "#FFFFFF";//get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/pi_controller" );
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <? 
                        $sum_span = "9";
                        if($pi_btb == "btb"){
                            $sum_span = "10";
                        $lc_sc_id = $row[csf("lc_sc_id")];
                        if($row[csf("is_lc_sc")] == 0)
                        {
                            $lc_sc_no = return_field_value("export_lc_no", "com_export_lc", "status_active = 1 and is_deleted = 0 and id = $lc_sc_id");
                        }else{
                            $lc_sc_no = return_field_value("contract_no", "com_sales_contract", "status_active = 1 and is_deleted = 0 and id = $lc_sc_id");
                        } ?>
                        <td><? echo $lc_sc_no;?></td>
                        <? }?>
                        <td width="50"><?php echo $row[csf('pi_number')]; ?></td>
                        <td width="80"><?php echo $color_library[$row[csf('color_id')]]; ?></td>
                        <td width="85"><?php echo $yarn_count[$row[csf('count_name')]]; ?></td>
                        <td width="90"><?php echo $composition[$row[csf('yarn_composition_item1')]]; ?></td>
                        <td width="40" align="right"><?php echo $row[csf('yarn_composition_percentage1')]; ?>%</td>
                        <td width="90"><?php echo $composition[$row[csf('yarn_composition_item2')]]; ?>&nbsp;</td>
                        <td width="40" align="right"><?php if($row[csf('yarn_composition_percentage2')]!=0) echo $row[csf('yarn_composition_percentage2')]."%"; ?>&nbsp;</td>
                        <td width="100">
                        	<?php if( $row[csf('yarn_type')] != 0 ) echo $yarn_type[$row[csf('yarn_type')]]; ?>
                        </td>
                        <td width="60">
                        	<?php if( $row[csf('uom')] != 0 ) echo $unit_of_measurement[$row[csf('uom')]]; ?>
                        </td>
                        <td width="100"  align="right"><?php echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                        <td width="60" align="right"><?php echo $row[csf('rate')]; ?></td>
                        <td width="110" align="right"><?php echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];?></td>
                    </tr>
                <?php 
                } 
                ?>
                <tr class="tbl_bottom"> 
                    <td colspan="<? echo $sum_span;?>">Sum</td> 
                    <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                    <td></td>
                    <td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
                </tr>
             </tbody>
            <?php 				
			 
			break;
			case 2:			//Knit Fabric
			case 13:		//grey fabric Knit Fabric
			case 110:		//Knit Fabric Import
				?>
				<thead>
					<tr> 
                    	<th>PI No</th>
                        <th>Construction</th>
                        <th>Composition</th>
                        <th>Color</th>					
                        <th>GSM</th>
                        <th>Dia/Width</th>
                        <th>UOM</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$data_array=sql_select("SELECT b.pi_number, a.fabric_composition, a.fabric_construction, a.color_id, a.gsm,a.dia_width, a.dia_width, a.uom, a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) and a.quantity>0 AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");
					$i = 0;
                    foreach($data_array as $row) 
					{
						$i++;
						if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
						else $bgcolor = "#FFFFFF";//get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/pi_controller" );
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" height="25">
                            <td><?php echo $row[csf('pi_number')]; ?></td> 
                            <td><?php echo $row[csf('fabric_construction')]; ?></td>
                            <td><?php echo $row[csf('fabric_composition')]; ?></td>
                            <td><?php echo $color_library[$row[csf('color_id')]]; ?></td>
                            <td><?php echo $row[csf('gsm')]; ?></td>
                            <td><?php echo $row[csf('dia_width')]; ?></td>
                            <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td align="right"><?php echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                            <td align="right"><?php echo number_format($row[csf('rate')],2); ?></td>
                            <td align="right"><?php echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
                        </tr>
					<?php 
					}
					?>
                    <tr class="tbl_bottom"> 
                        <td colspan="7">Sum</td> 
                        <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td>&nbsp;</td>
                        <td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
                    </tr>
                </tbody>  
				 <?php
				break;
				case 3:			//Woven Fabric
				case 14:		//Grey Fabric Woven
			?>
			<thead>
				<tr>
                	<th>PI No</th>
					<th>Construction</th>
					<th>Composition</th> 
                    <th>Color</th>
                    <th>Weight</th>
					<th>Width</th>
					<th>UOM</th>
					<th>Quantity</th>
					<th>Rate</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
            <?
				$data_array=sql_select("SELECT b.pi_number, a.color_id, a.fabric_composition, a.fabric_construction, a.dia_width, a.weight, a.uom, a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) and a.quantity>0 and a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");	 
				
				//$color_library = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
				
				$i = 0;
				foreach($data_array as $row) 
				{
					$i++;
					if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
					else $bgcolor = "#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>"  height="25">
						<td><?php echo $row[csf('pi_number')]; ?></td>
						<td><?php echo $row[csf('fabric_construction')]; ?></td>
						<td><?php echo $row[csf('fabric_composition')]; ?></td>
						<td><?php echo $color_library[$row[csf('color_id')]]; ?></td>
						<td><?php echo $row[csf('weight')]; ?></td>
						<td><?php echo $row[csf('dia_width')]; ?></td>
						<td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
						<td align="right"><?php echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
						<td align="right"><?php echo number_format($row[csf('rate')],4); ?></td>
						<td align="right"><?php echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
					</tr>
				<?php 
				} 
				?>
				<tr class="tbl_bottom" height="25">
					<td colspan = "7" align="right">Sum</td> 
					<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
					<td>&nbsp;</td>
					<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
				</tr>
			</tbody>
			<?php
			break;
			case 8:			//Spare Parts
			case 9:			//Machinaries
			case 10:		//Other Capital Items
			case 11:		//Stationaries
			case 33:		//Others
			?>
            <thead>
                <tr>
                    <th>PI No</th>
                    <th>Item Group</th>
                    <th>Item Description</th>
                    <th>UOM</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
				<?php
                 $data_array=sql_select("SELECT b.pi_number, a.item_group, a.item_description, a.uom, a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id)  AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");	 
                 
                $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
                $i = 0;
                foreach($data_array as $row) 
                {
					$i++;
					if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
					else $bgcolor = "#FFFFFF";
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" height="25" >
                        <td><?php echo $row[csf('pi_number')]; ?></td>
                        <td><?php echo $item_group_library[$row[csf('item_group')]]; ?></td>
                        <td><?php echo $row[csf('item_description')]; ?></td>
                        <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td  align="right"><?php echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
                        <td  align="right"><?php echo number_format($row[csf('rate')],4); ?></td>
                        <td  align="right"><?php echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
                    </tr>
                <?php 
                } 
                ?>
                <tr class="tbl_bottom" height="25">
                    <td  colspan = "4" align="right">Sum</td> 
                    <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                    <td></td>
                    <td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
                </tr>
             </tbody>
			<?php
			break;
			case 4:			//Accessories  
				?>
				<thead>
					<tr>
                    	<th>PI No</th>
						<th>Item Group</th>
						<th>Item Description</th>
                        <th>Gmts Color</th>
                        <th>Gmts Size</th>
						<th>UOM</th>
						<th>Quantity</th>
						<th>Rate</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody>
					<?php
					 $data_array=sql_select("SELECT b.pi_number, a.item_group, a.item_description, a.color_id, a.size_id, a.uom, a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id)  AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");	 
					
					$item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
					$i = 0;
                    foreach($data_array as $row) 
					{
						$i++;
						if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
                            <td><?php echo $row[csf('pi_number')]; ?></td>
                            <td><?php echo $item_group_library[$row[csf('item_group')]]; ?></td>
                            <td><?php echo $row[csf('item_description')]; ?></td>
                            <td><?php echo $color_library[$row[csf('color_id')]]; ?></td>
                            <td><?php echo $size_library[$row[csf('size_id')]]; ?></td>
                            <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td align="right"><?php echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
                            <td align="right"><?php echo number_format($row[csf('rate')],4); ?></td>
                            <td align="right"><?php echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
                        </tr>
					<?php 
				    } 
				    ?>
                    <tr class="tbl_bottom" height="25">
                        <td  colspan = "6" align="right">Sum</td> 
                        <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
					</tr>
             </tbody>	
			<?php 
				break;
			case 5:			//Chemicals
			case 6:			//Dyes
			case 7:			//Auxilary Chemicals
			case 15:		
			case 16:		
			case 17:			
			case 18:			
			case 19:			
			case 20:		
			case 21:		
			case 22:		
			case 23:		
				?>
				<thead>
					<tr>
                    	<th>PI No</th>
						<th>Item Group</th>
						<th>Item Description</th>
						<th>UOM</th>
						<th>Quantity</th>
						<th>Rate</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$data_array=sql_select("SELECT b.pi_number ,a.item_group, a.item_description, a.uom, a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");	

					$item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
					 
					$i = 0;
                    foreach($data_array as $row) 
					{
						$i++;
					 	if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td><?php echo $row[csf('pi_number')]; ?></td>
                            <td><?php echo $item_group_library[$row[csf('item_group')]]; ?>&nbsp;</td>
                            <td><?php echo $row[csf('item_description')]; ?>&nbsp;</td>
                            <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td align="right"><?php echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                            <td align="right"><?php echo $row[csf('rate')]; ?></td>
                            <td align="right"><?php echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')]; ?></td>
                        </tr>
					<?php 
					} 
					?>
                    <tr class="tbl_bottom" height="25"> 
                        <td  colspan = "4" align="right">Sum</td> 
                        <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
                    </tr>
               </tbody>
                    
				
				<?php  
				break;
			case 12:		//Services
				?>
				<thead>
					<tr>
                    	<th>PI No</th>
                    	<th>Service Type</th>
						<th>Item Description</th>
						<th>UOM</th>
						<th>Quantity</th>
						<th>Rate</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody>
                <?
				 $data_array=sql_select("SELECT b.pi_number,a.id,a.pi_id,a.item_description,a.uom,a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount, a.service_type,a.status_active FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");	 
				 
				$i = 0;
				foreach($data_array as $row) 
				{
					$i++;
				 	if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
					else $bgcolor = "#FFFFFF";
				?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
                        <td><?php echo $row[csf('pi_number')]; ?></td>
                        <td><?php echo $service_type[$row[csf('service_type')]]; ?></td>
                        <td><?php echo ($row[csf('item_description')]); ?></td>
                        <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td align="right"><?php echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                        <td align="right"><?php echo $row[csf('rate')]; ?></td>
                        <td align="right"><?php echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
                    </tr>
				<?php 
				} 
				?>
				
				<tr class="tbl_bottom">
					<td  colspan = "4" align="right">Sum</td> 
					<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
					<td>&nbsp;</td>
					<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
				</tr>
			</tbody>
			<?php  
			break;
			case 24:		
				?>
				<thead>
					<tr>
                    	<th>PI No</th>
                    	<th>Lot No</th>
                        <th>Count</th>
						<th>Yarn Description</th>
                        <th>Color</th>
                        <th>Color Range</th>
						<th>UOM</th>
						<th>Quantity</th>
						<th>Rate</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody>
                <?
				 $data_array=sql_select("SELECT b.pi_number,a.id,a.pi_id,a.item_description,a.uom,a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount, a.service_type,a.status_active, a.lot_no,a.yarn_color,a.color_range, a.count_name FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");	 
				 $color_library = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
				 $count_library = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
				 $i = 0;
				 foreach($data_array as $row) 
				 {
					$i++;
				 	if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
					else $bgcolor = "#FFFFFF";
				 ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><?php echo $row[csf('pi_number')]; ?></td>
                        <td><?php echo $row[csf('lot_no')]; ?>&nbsp;</td>
                        <td><?php echo $count_library[$row[csf('count_name')]]; ?>&nbsp;</td>
                        <td><?php echo $row[csf('item_description')]; ?></td>
                        <td><?php echo $color_library[$row[csf('yarn_color')]]; ?>&nbsp;</td>
                        <td><?php echo $color_range[$row[csf('color_range')]]; ?>&nbsp;</td>
                        <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td align="right"><?php echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                        <td align="right"><?php echo $row[csf('rate')]; ?></td>
                        <td align="right"><?php echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
                    </tr>
				 <?php 
				 } 
				 ?>
				
				<tr class="tbl_bottom">
					<td colspan = "7" align="right">Sum</td> 
					<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
					<td>&nbsp;</td>
					<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
				</tr>
			</tbody>
			<?php  
			break;
			case 25:		
				?>
				<thead>
					<tr>
                    	<th>PI No</th>
                    	<th>Gmts Item</th>
                        <th>Embellishment Name</th>
						<th>Embellishment Type</th>
                        <th>Gmts Color</th>
						<th>UOM</th>
						<th>Quantity</th>
						<th>Rate</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody>
                <?
				 $data_array=sql_select("SELECT b.pi_number,a.id,a.pi_id,a.item_description,a.uom,a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount, a.service_type,a.status_active, a.embell_name,a.embell_type,a.color_id, a.gmts_item_id FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");	 
				 $color_library = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
				 $i = 0;
				 foreach($data_array as $row) 
				 {
					$i++;
				 	if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
					else $bgcolor = "#FFFFFF";
					
					$emb_arr=array();
					if($row[csf('embell_name')]==1) $emb_arr=$emblishment_print_type;
					else if($row[csf('embell_name')]==2) $emb_arr=$emblishment_embroy_type;
					else if($row[csf('embell_name')]==3) $emb_arr=$emblishment_wash_type;
					else if($row[csf('embell_name')]==4) $emb_arr=$emblishment_spwork_type;
					else $emb_arr=$blank_array;
				 ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" >
                        <td><?php echo $row[csf('pi_number')]; ?></td>
                        <td><?php echo $garments_item[$row[csf('gmts_item_id')]]; ?>&nbsp;</td>
                        <td><?php echo $emblishment_name_array[$row[csf('embell_name')]]; ?>&nbsp;</td>
                        <td><?php echo $emb_arr[$row[csf('embell_type')]]; ?>&nbsp;</td>
                        <td><?php echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</td>
                        <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td align="right"><?php echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                        <td align="right"><?php echo $row[csf('rate')]; ?></td>
                        <td align="right"><?php echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
                    </tr>
				 <?php 
				 } 
				 ?>
				
				<tr class="tbl_bottom">
					<td colspan = "6" align="right">Sum</td> 
					<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
					<td>&nbsp;</td>
					<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
				</tr>
			</tbody>
			<?php  
			break;
		} 
		?>
	 </table>
	<?
	exit();
}

//--------------------------------------------End BTB Pi Details List----------------------------------------------------------------//
?>


<?
if($action=="btb_open_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:810px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:100%">
            <div style="width:810px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
                <br />    	
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="130">BTB No.</th>
                        <th width="90">BTB Date</th>
                        <th width="140">Supplier</th>
                        <th width="140">Item Category</th>
                        <th width="120">BTB Value</th>
                        <th>Shipment Date</th>
                    </thead>
                </table>
           </div>
           <div style="width:810px; overflow-y:scroll; max-height:230px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                <? 
                    $i=1; $btb_lc_id=array();
                    $sql_opened_lc="select c.id, c.lc_number, c.lc_date, c.supplier_id, c.item_category_id, c.lc_value as lc_value2, c.last_shipment_date, sum(b.current_distribution) as lc_value from com_export_lc a, com_btb_export_lc_attachment b, com_btb_lc_master_details c where  a.id=b.lc_sc_id and b.is_lc_sc=0 and b.import_mst_id=c.id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.lc_number, c.lc_date, c.supplier_id, c.item_category_id, c.lc_value, c.last_shipment_date";
                    $result=sql_select($sql_opened_lc);
                    $total_btb_amnt = 0;
                    foreach($result as $row)  
                    {
                        $total_btb_amnt += $row[csf('lc_value')];
                        $btb_lc_id[]=$row[csf('id')];
                        
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="130"><p><? echo $row[csf('lc_number')]; ?></p></td>
                        <td width="90" align="center"><? echo change_date_format($row[csf('lc_date')]); ?></td>
                        <td width="140"><p><? echo $supplier_details[$row[csf('supplier_id')]]; ?></p></td>
                        <td width="140"><? echo $item_category[$row[csf('item_category_id')]];  ?></td>
                        <td width="120" align="right"><? echo number_format($row[csf('lc_value')],2); ?></td>
                        <td align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?></td>
                    </tr>
                    <?
                    $i++;
                    }
                    
                    $sql_opened_sc="select c.id, c.lc_number, c.lc_date, c.supplier_id, c.item_category_id, c.lc_value as lc_value2, c.last_shipment_date, sum(b.current_distribution) as lc_value from com_sales_contract a, com_btb_export_lc_attachment b, com_btb_lc_master_details c where  a.id=b.lc_sc_id and b.is_lc_sc=1 and b.import_mst_id=c.id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.lc_number, c.lc_date, c.supplier_id, c.item_category_id, c.lc_value, c.last_shipment_date";
                    $result=sql_select($sql_opened_sc);
                    foreach($result as $row_sc)  
                    {
                        if(!in_array($row_sc[csf('id')],$btb_lc_id))
                        {
                            $total_btb_amnt += $row_sc[csf('lc_value')];
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                        
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="130"><p><? echo $row_sc[csf('lc_number')]; ?></p></td>
                                <td width="90" align="center"><? echo change_date_format($row_sc[csf('lc_date')]); ?></td>
                                <td width="140"><p><? echo $supplier_details[$row_sc[csf('supplier_id')]]; ?></p></td>
                                <td width="140"><? echo $item_category[$row_sc[csf('item_category_id')]];  ?></td>
                                <td width="120" align="right"><? echo number_format($row_sc[csf('lc_value')],2); ?></td>
                                <td align="center"><? echo change_date_format($row_sc[csf('last_shipment_date')]); ?></td>
                            </tr>
                            <?
                        $i++;
                        }
                    }
                    ?>
                     <tfoot>
                        <tr class="tbl_bottom">
                            <td colspan="5" align="right">Total</td>
                            <td align="right"><? echo number_format($total_btb_amnt,2)?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
	</fieldset>
</div>
<?
exit();
}

if($action=="pc_drawn_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:810px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:100%">
            <div style="width:710px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
                <br />    
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="180">PC No.</th>
                        <th width="100">PC Date</th>
                        <th width="180">LC/SC No.</th>
                        <th>PC Amount (BDT)</th>
                    </thead>
                </table>
            </div>
            <div style="width:710px; overflow-y:scroll; max-height:230px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                <? 
                    $i=1;
					if($db_type==0)
					{
						$sql="(select a.export_lc_no as lc_or_sc, b.loan_number, c.loan_date, d.amount from com_export_lc a, com_pre_export_finance_dtls b, com_pre_export_finance_mst c,  com_pre_export_lc_wise_dtls d where a.id=d.lc_sc_id and d.export_type=1 and d.pre_export_dtls_id=b.id and b.mst_id=c.id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.export_lc_no, b.loan_number)
						union all
						(
							select a.contract_no as lc_or_sc, b.loan_number, c.loan_date, d.amount from com_sales_contract a, com_pre_export_finance_dtls b, com_pre_export_finance_mst c,  com_pre_export_lc_wise_dtls d where a.id=d.lc_sc_id and d.export_type=2 and d.pre_export_dtls_id=b.id and b.mst_id=c.id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.contract_no, b.loan_number
						)
						";
					}
					else
					{
						$sql="(select a.export_lc_no as lc_or_sc, b.loan_number, c.loan_date, d.amount from com_export_lc a, com_pre_export_finance_dtls b, com_pre_export_finance_mst c,  com_pre_export_lc_wise_dtls d where a.id=d.lc_sc_id and d.export_type=1 and d.pre_export_dtls_id=b.id and b.mst_id=c.id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0)
						union all
						(
							select a.contract_no as lc_or_sc, b.loan_number, c.loan_date, d.amount from com_sales_contract a, com_pre_export_finance_dtls b, com_pre_export_finance_mst c,  com_pre_export_lc_wise_dtls d where a.id=d.lc_sc_id and d.export_type=2 and d.pre_export_dtls_id=b.id and b.mst_id=c.id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
						)
						order by lc_or_sc, loan_number";
					}
					//echo $sql;
                    $result=sql_select($sql);
                    $total_pc_drawn_amnt = 0;
                    foreach($result as $row)  
                    {
                        $total_pc_drawn_amnt += $row[csf('amount')];
                        
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="180"><p><? echo $row[csf('loan_number')]; ?></p></td>
                            <td width="100" align="center"><? echo change_date_format($row[csf('loan_date')]); ?>&nbsp;</td>
                            <td width="180"><p><? echo $row[csf('lc_or_sc')]; ?></p></td>
                            <td align="right"><? echo number_format($row[csf('amount')],2); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                     <tfoot>
                        <tr class="tbl_bottom">
                            <td colspan="4" align="right">Total</td>
                            <td align="right"><? echo number_format($total_pc_drawn_amnt,2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
	</fieldset>
</div>
<?
exit();
}

if($action=="gross_bill_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:810px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:100%">
            <div style="width:800px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
                <br />  
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="150">Invoice No.</th>
                        <th width="90">Invoice Date</th>
                        <th width="150">Buyer Name</th>
                        <th width="110">Invoice Qnty</th>
                        <th width="80">Rate</th>
                        <th>Invoice Value</th>
                    </thead>
                </table>
           </div>
           <div style="width:800px; overflow-y:scroll; max-height:280px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                <? 
                    $i=1; $total_value=0;
                    $sql_lc="select b.invoice_no, b.invoice_date, b.buyer_id, c.current_invoice_value, c.current_invoice_qnty, c.current_invoice_rate from com_export_lc a, com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c where a.id=b.lc_sc_id and b.is_lc=1 and b.id=c.mst_id and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and c.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
                    $result=sql_select($sql_lc);
                    foreach($result as $row)  
                    {
                        $total_value += $row[csf('current_invoice_value')];
                        
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="150"><p><? echo $row[csf('invoice_no')]; ?></p></td>
                        <td width="90" align="center"><? echo change_date_format($row[csf('invoice_date')]); ?></td>
                        <td width="150"><p><? echo $buyer_details[$row[csf('buyer_id')]]; ?></p></td>
                        <td width="110" align="right"><? echo number_format($row[csf('current_invoice_qnty')],2); ?></td>
                        <td width="80" align="right"><? echo number_format($row[csf('current_invoice_rate')],2); ?></td>
                        <td align="right"><? echo number_format($row[csf('current_invoice_value')],2); ?></td>
                    </tr>
                    <?
                    $i++;
                    }
                    
                    $sql_sc="select b.invoice_no, b.invoice_date, b.buyer_id, c.current_invoice_value, c.current_invoice_qnty, c.current_invoice_rate from com_sales_contract a, com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c where a.id=b.lc_sc_id and b.is_lc=2 and b.id=c.mst_id and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and c.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
                    $result_sc=sql_select($sql_sc);
                    foreach($result_sc as $row_sc)  
                    {
                        $total_value += $row_sc[csf('current_invoice_value')];
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                    
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="150"><p><? echo $row_sc[csf('invoice_no')]; ?></p></td>
                            <td width="90" align="center"><? echo change_date_format($row_sc[csf('invoice_date')]); ?></td>
                            <td width="150"><p><? echo $buyer_details[$row_sc[csf('buyer_id')]]; ?></p></td>
                            <td width="110" align="right"><? echo number_format($row_sc[csf('current_invoice_qnty')],2); ?></td>
                            <td width="80" align="right"><? echo number_format($row_sc[csf('current_invoice_rate')],2); ?></td>
                            <td align="right"><? echo number_format($row_sc[csf('current_invoice_value')],2); ?></td>
                        </tr>
                        <?
                    $i++;
                    }
                    ?>
                     <tfoot>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"><? echo number_format($total_value,2); ?></th>
                    </tfoot>
                </table>
            </div>
        </div>
    </fieldset>    
</div>
<?
exit();
}

if($action=="net_bill_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:810px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:100%">
            <div style="width:800px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
                <br />  
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="150">Invoice No.</th>
                        <th width="90">Invoice Date</th>
                        <th width="150">Buyer Name</th>
                        <th width="110">Invoice Value</th>
                        <th width="100">Deduct Value</th>
                        <th>Net Invoice Value</th>
                    </thead>
                </table>
           </div>
           <div style="width:800px; overflow-y:scroll; max-height:290px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                <? 
                    $i=1; $total_value=0; $total_deduct_value=0; $total_net_value=0;
                    $sql_lc="select b.invoice_no, b.invoice_date, b.buyer_id, b.invoice_value, b.net_invo_value, (b.discount_ammount+b.bonus_ammount+b.claim_ammount+commission) as deduct_amnt from com_export_lc a, com_export_invoice_ship_mst b where a.id=b.lc_sc_id and b.is_lc=1 and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                    $result=sql_select($sql_lc);
                    foreach($result as $row)  
                    {
                        $total_value += $row[csf('invoice_value')];
						$total_deduct_value += $row[csf('deduct_amnt')];
						$total_net_value += $row[csf('net_invo_value')];
                        
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="150"><p><? echo $row[csf('invoice_no')]; ?></p></td>
                        <td width="90" align="center"><? echo change_date_format($row[csf('invoice_date')]); ?></td>
                        <td width="150"><p><? echo $buyer_details[$row[csf('buyer_id')]]; ?></p></td>
                        <td width="110" align="right"><? echo number_format($row[csf('invoice_value')],2); ?></td>
                        <td width="100" align="right"><? echo number_format($row[csf('deduct_amnt')],2); ?></td>
                        <td align="right"><? echo number_format($row[csf('net_invo_value')],2); ?></td>
                    </tr>
                    <?
                    $i++;
                    }
                    
                    $sql_sc="select b.invoice_no, b.invoice_date, b.buyer_id, b.invoice_value, b.net_invo_value, (b.discount_ammount+b.bonus_ammount+b.claim_ammount+commission) as deduct_amnt from com_sales_contract a, com_export_invoice_ship_mst b where a.id=b.lc_sc_id and b.is_lc=2 and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                    $result_sc=sql_select($sql_sc);
                    foreach($result_sc as $row_sc)  
                    {
                        $total_value += $row_sc[csf('invoice_value')];
						$total_deduct_value += $row_sc[csf('deduct_amnt')];
						$total_net_value += $row_sc[csf('net_invo_value')];
						
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="150"><p><? echo $row_sc[csf('invoice_no')]; ?></p></td>
                            <td width="90" align="center"><? echo change_date_format($row_sc[csf('invoice_date')]); ?></td>
                            <td width="150"><p><? echo $buyer_details[$row_sc[csf('buyer_id')]]; ?></p></td>
                            <td width="110" align="right"><? echo number_format($row_sc[csf('invoice_value')],2); ?></td>
                            <td width="100" align="right"><? echo number_format($row_sc[csf('deduct_amnt')],2); ?></td>
                            <td align="right"><? echo number_format($row_sc[csf('net_invo_value')],2); ?></td>
                        </tr>
                   	<?
                    	$i++;
                    }
                    ?>
                     <tfoot>
                        <th colspan="4" align="right">Total</th>
                        <th align="right"><? echo number_format($total_value,2); ?></th>
                        <th align="right"><? echo number_format($total_deduct_value,2); ?></th>
                        <th align="right"><? echo number_format($total_net_value,2); ?></th>
                    </tfoot>
                </table>
            </div>
        </div>
    </fieldset>    
</div>
<?
exit();
}

if($action=="export_proceed_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:810px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:100%">
            <div style="width:710px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
                <br />  
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="150">Bill / Invoice No.</th>
                        <th width="80">Bill/Invoice</th>
                        <th width="150">LC/SC No.</th>
                        <th width="100">Realization Date</th>
                        <th>Realized Amount</th>
                    </thead>
                </table>
            </div>
            <div style="width:710px; overflow-y:scroll; max-height:230px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                <? 
                    $i=1; $total_realized_amnt = 0;
					if($db_type==0)
					{
						$bill_id_lc=return_field_value("group_concat(distinct(c.id)) as id","com_export_lc a, com_export_doc_submission_invo b, com_export_doc_submission_mst c","a.id=b.lc_sc_id and b.is_lc=1 and c.id=b.doc_submission_mst_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","id");
					   
						$bill_id_sc=return_field_value("group_concat(distinct(c.id)) as id","com_sales_contract a, com_export_doc_submission_invo b, com_export_doc_submission_mst c","a.id=b.lc_sc_id and b.is_lc=2 and c.id=b.doc_submission_mst_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","id");
						
						$invoice_id_sc=return_field_value("group_concat(distinct(b.id)) as id","com_sales_contract a, com_export_invoice_ship_mst b","a.id=b.lc_sc_id and b.is_lc=2 and a.id=b.lc_sc_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.pay_term=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
					}
					else
					{
						$bill_id_lc=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as id","com_export_lc a, com_export_doc_submission_invo b, com_export_doc_submission_mst c","a.id=b.lc_sc_id and b.is_lc=1 and c.id=b.doc_submission_mst_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","id");
					    $bill_id_lc=implode(",",array_unique(explode(",",$bill_id_lc)));
						
						$bill_id_sc=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as id","com_sales_contract a, com_export_doc_submission_invo b, com_export_doc_submission_mst c","a.id=b.lc_sc_id and b.is_lc=2 and c.id=b.doc_submission_mst_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","id");
						$bill_id_sc=implode(",",array_unique(explode(",",$bill_id_sc)));
						
						$invoice_id_sc=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as id","com_sales_contract a, com_export_invoice_ship_mst b","a.id=b.lc_sc_id and b.is_lc=2 and a.id=b.lc_sc_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.pay_term=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
						$invoice_id_sc=implode(",",array_unique(explode(",",$invoice_id_sc)));
					}
                    if($bill_id_lc!="")
                    {
                        $sql_lc="select d.invoice_bill_id, d.received_date, sum(e.document_currency) as document_currency from com_export_proceed_realization d, com_export_proceed_rlzn_dtls e where d.id=e.mst_id and d.invoice_bill_id in($bill_id_lc) and d.is_invoice_bill=1 and e.type=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by d.id, d.invoice_bill_id, d.received_date";
                        $result_lc=sql_select($sql_lc);
                        
                        foreach($result_lc as $row) 
                        {
                            $total_realized_amnt += $row[csf('document_currency')];
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                                
                            $bill_no=return_field_value("bank_ref_no","com_export_doc_submission_mst","id=".$row[csf('invoice_bill_id')]);
                            $lc_no=return_field_value("a.export_lc_no as export_lc_no","com_export_lc a, com_export_doc_submission_invo b","a.id=b.lc_sc_id and b.is_lc=1 and b.doc_submission_mst_id=".$row[csf('invoice_bill_id')],"export_lc_no");
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><p><? echo $bill_no; ?></p></td>
                                <td width="80">Bill</td>
                                <td width="150"><p><? echo $lc_no; ?></p></td>
                                <td width="100" align="center"><? echo change_date_format($row[csf('received_date')]); ?></td>
                                <td align="right"><? echo number_format($row[csf('document_currency')],2); ?></td>
                            </tr>
                            <?
                        $i++;
                        }
                    }
                    
					if($bill_id_sc!="")
                    {
                       $sql_sc="select d.invoice_bill_id, d.received_date, sum(e.document_currency) as document_currency from com_export_proceed_realization d, com_export_proceed_rlzn_dtls e where d.id=e.mst_id and d.invoice_bill_id in($bill_id_sc) and d.is_invoice_bill=1 and e.type=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by d.id, d.invoice_bill_id, d.received_date";
                        $result_sc=sql_select($sql_sc);
                        foreach($result_sc as $row_sc) 
                        {
                            $total_realized_amnt += $row_sc[csf('document_currency')];
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                            
                            $invoice_no=return_field_value("bank_ref_no","com_export_doc_submission_mst","id=".$row_sc[csf('invoice_bill_id')]);	
                            $sc_no=return_field_value("a.contract_no as contract_no","com_sales_contract a, com_export_doc_submission_invo b","a.id=b.lc_sc_id and b.is_lc=2 and b.doc_submission_mst_id=".$row_sc[csf('invoice_bill_id')],"contract_no");
                            
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><p><? echo $invoice_no; ?>&nbsp;</p></td>
                                <td width="80">Bill</td>
                                <td width="150"><p><? echo $sc_no; ?></p></td>
                                <td width="100" align="center"><? echo change_date_format($row_sc[csf('received_date')]); ?>&nbsp;</td>
                                <td align="right"><? echo number_format($row_sc[csf('document_currency')],2); ?></td>
                            </tr>
                            <?
                            $i++;
                        }
                    }
					
					if($invoice_id_sc!="")
                    {
                        $sql_sc="select d.invoice_bill_id, d.received_date, sum(e.document_currency) as document_currency from com_export_proceed_realization d, com_export_proceed_rlzn_dtls e where d.id=e.mst_id and d.invoice_bill_id in($invoice_id_sc) and d.is_invoice_bill=2 and e.type=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by d.id";
                        $result_sc=sql_select($sql_sc);
                        foreach($result_sc as $row_sc) 
                        {
                            $total_realized_amnt += $row_sc[csf('document_currency')];
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                            
                            $bill_no=return_field_value("invoice_no","com_export_invoice_ship_mst","id=".$row_sc[csf('invoice_bill_id')]);	
                            $sc_no=return_field_value("a.contract_no as contract_no","com_sales_contract a, com_export_invoice_ship_mst b","a.id=b.lc_sc_id and b.is_lc=2 and b.id=".$row_sc[csf('invoice_bill_id')],"contract_no");
                            
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><p><? echo $bill_no; ?></p></td>
                                <td width="80">Invoice</td>
                                <td width="150"><p><? echo $sc_no; ?></p></td>
                                <td width="100" align="center"><? echo change_date_format($row_sc[csf('received_date')]); ?></td>
                                <td align="right"><? echo number_format($row_sc[csf('document_currency')],2); ?></td>
                            </tr>
                            <?
                            $i++;
                        }
                    }
                    ?>
                     <tfoot class="tbl_bottom">
                        <td colspan="5" align="right">Total</td>
                        <td align="right"><? echo number_format($total_realized_amnt,2); ?></td>
                     </tfoot>
                </table>
            </div>
        </div>
	</fieldset>
</div>
<?
exit();
}

if($action=="short_realized")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:810px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%;">
        <div id="report_container" align="center" style="width:100%">
            <div style="width:800px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
               	<table style="margin-top:2px" class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                   <thead>
                        <tr>
                            <th colspan="9">Deduction at Export Invoice</th>
                        </tr>
                        <tr>
                            <th width="40">SL</th>
                            <th width="110">LC/SC No.</th>
                            <th width="110">Invoice No.</th>
                            <th width="80">Invoice Date</th>
                            <th width="80">Discount Amount</th>
                            <th width="80">Bonus Amount</th>
                            <th width="80">Claim Amount</th>
                            <th width="80">Commission</th>
                            <th>Total Deduct Amount</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div style="width:800px; overflow-y:scroll; max-height:175px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                	<? 
					$i=1; $tot_deduct_amnt=0;
					$sql="(select a.export_lc_no as sc_lc_no, b.is_lc, b.lc_sc_id, b.invoice_no, b.invoice_date, b.discount_ammount, b.bonus_ammount, b.claim_ammount, b.commission from com_export_lc a, com_export_invoice_ship_mst b where a.id=b.lc_sc_id and b.is_lc=1 and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0)
					union all
					(
						select c.contract_no as sc_lc_no, d.is_lc, d.lc_sc_id, d.invoice_no, d.invoice_date, d.discount_ammount, d.bonus_ammount, d.claim_ammount, d.commission from com_sales_contract c, com_export_invoice_ship_mst d where c.id=d.lc_sc_id and d.is_lc=2 and c.beneficiary_name='$company_name' and c.internal_file_no='$file_no' and c.lien_bank like '$bank_id' and c.sc_year like '$text_year' and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					)
					";
					$result=sql_select($sql);
					foreach($result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							
						$deduct_amnt=$row[csf('discount_ammount')]+$row[csf('bonus_ammount')]+$row[csf('claim_ammount')]+$row[csf('commission')];
						$tot_deduct_amnt+=$deduct_amnt;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="110"><p><? echo $row[csf('sc_lc_no')]; ?></p></td>
							<td width="110"><p><? echo $row[csf('invoice_no')]; ?></p></td>
							<td width="80" align="center"><? echo change_date_format($row[csf('invoice_date')]); ?></td>
							<td width="80" align="right"><? echo number_format($row[csf('discount_ammount')],2); ?></td>
							<td width="80" align="right"><? echo number_format($row[csf('bonus_ammount')],2); ?></td>
							<td width="80" align="right"><? echo number_format($row[csf('claim_ammount')],2); ?></td>
                            <td width="80" align="right"><? echo number_format($row[csf('commission')],2); ?></td>
							<td align="right"><? echo number_format($deduct_amnt,2); ?></td>
						</tr>
						<?
					$i++;	
					}
					?>
                    <tfoot class="tbl_bottom">
                        <td colspan="8" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_deduct_amnt,2); ?></td>
                    </tfoot>
                </table>
            </div>
            <table style="margin-top:2px" class="rpt_table" border="1" rules="all" width="790" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="5">Deduction at Export realization</th>
                    </tr>
                    <tr>
                        <th width="50">SL</th>
                        <th width="200">LC/SC No.</th>
                        <th width="200">Export Bill No</th>
                        <th width="140">Realized Date</th>
                        <th>Total Deduct Amount</th>
                    </tr>
                </thead>
            </table>
            <div style="width:790px;" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="790" cellpadding="0" cellspacing="0">
                <? 
					if($db_type==0)
					{
						$bill_id_lc=return_field_value("group_concat(distinct(c.id)) as id","com_export_lc a, com_export_doc_submission_invo b, com_export_doc_submission_mst c","a.id=b.lc_sc_id and b.is_lc=1 and c.id=b.doc_submission_mst_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","id");
						
						$bill_id_sc=return_field_value("group_concat(distinct(c.id)) as id","com_sales_contract a, com_export_doc_submission_invo b, com_export_doc_submission_mst c","a.id=b.lc_sc_id and b.is_lc=2 and c.id=b.doc_submission_mst_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","id");
						
						$invoice_id_sc=return_field_value("group_concat(distinct(b.id)) as id","com_sales_contract a, com_export_invoice_ship_mst b","a.id=b.lc_sc_id and b.is_lc=2 and a.id=b.lc_sc_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.pay_term=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
					}
					else
					{
						$bill_id_lc=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as id","com_export_lc a, com_export_doc_submission_invo b, com_export_doc_submission_mst c","a.id=b.lc_sc_id and b.is_lc=1 and c.id=b.doc_submission_mst_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","id");
						$bill_id_lc=implode(",",array_unique(explode(",",$bill_id_lc)));
						
						$bill_id_sc=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as id","com_sales_contract a, com_export_doc_submission_invo b, com_export_doc_submission_mst c","a.id=b.lc_sc_id and b.is_lc=2 and c.id=b.doc_submission_mst_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","id");
						$bill_id_sc=implode(",",array_unique(explode(",",$bill_id_sc)));
						
						$invoice_id_sc=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as id","com_sales_contract a, com_export_invoice_ship_mst b","a.id=b.lc_sc_id and b.is_lc=2 and a.id=b.lc_sc_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.pay_term=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
						$invoice_id_sc=implode(",",array_unique(explode(",",$invoice_id_sc)));
					}
					
                    $result=''; $i=1; $total_realized_amnt = 0;
                    if($bill_id_lc!='' && $bill_id_sc!='')
                    {
                        $bill_id_lc=explode(",",$bill_id_lc);
                        $bill_id_sc=explode(",",$bill_id_sc);
                        $result = array_merge( $bill_id_lc, $bill_id_sc);
                        $result =implode(",",$result);	
                    }
                    else if($bill_id_lc!='')
                    {
                        $result=$bill_id_lc;
                    }
                    else if($bill_id_sc!='')
                    {
                        $result=$bill_id_sc;
                    }
                    
					if($result!="")
					{
						$sql="select d.invoice_bill_id, d.received_date, sum(e.document_currency) as document_currency from com_export_proceed_realization d, com_export_proceed_rlzn_dtls e where d.id=e.mst_id and d.invoice_bill_id in($result) and d.is_invoice_bill=1 and e.type=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by d.id, d.invoice_bill_id, d.received_date";
						$result=sql_select($sql);
						foreach($result as $row) 
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							
							$bill_no=return_field_value("bank_ref_no","com_export_doc_submission_mst","id=".$row[csf('invoice_bill_id')],"bank_ref_no");	
							$lc_sc_no=return_field_value("a.contract_no as contract_no","com_sales_contract a, com_export_doc_submission_invo b","a.id=b.lc_sc_id and b.is_lc=2 and b.doc_submission_mst_id=".$row[csf('invoice_bill_id')],"contract_no");
							if($lc_sc_no=="")
							{
								$lc_sc_no=return_field_value("a.export_lc_no as export_lc_no","com_export_lc a, com_export_doc_submission_invo b","a.id=b.lc_sc_id and b.is_lc=1 and b.doc_submission_mst_id=".$row[csf('invoice_bill_id')],"export_lc_no"); 
							}
							 
							$total_ded_amnt_short += $row[csf('document_currency')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trr_<? echo $i; ?>">
								<td width="50"><? echo $i; ?></td>
								<td width="200"><p><? echo $lc_sc_no; ?></p></td>
								<td width="200"><p><? echo $bill_no; ?>&nbsp;</p></td>
								<td width="140" align="center"><? echo change_date_format($row[csf('received_date')]); ?>&nbsp;</td>
								<td align="right"><? echo number_format($row[csf('document_currency')],2); ?></td>
							</tr>
							<?
						$i++;
						}
					}
					
					if($invoice_id_sc!="")
					{
						$sql="select d.invoice_bill_id, d.received_date, sum(e.document_currency) as document_currency from com_export_proceed_realization d, com_export_proceed_rlzn_dtls e where d.id=e.mst_id and d.invoice_bill_id in($invoice_id_sc) and d.is_invoice_bill=2 and e.type=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by d.id, d.invoice_bill_id, d.received_date";
						$result=sql_select($sql);
						foreach($result as $row) 
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							
							$bill_no=return_field_value("invoice_no","com_export_invoice_ship_mst","id=".$row[csf('invoice_bill_id')]);	
							$lc_sc_no=return_field_value("a.contract_no as contract_no","com_sales_contract a, com_export_invoice_ship_mst b","a.id=b.lc_sc_id and b.is_lc=2 and b.id=".$row[csf('invoice_bill_id')],"contract_no");
							if($lc_sc_no=="")
							{
								$lc_sc_no=return_field_value("a.export_lc_no as export_lc_no","com_export_lc a, com_export_invoice_ship_mst b","a.id=b.lc_sc_id and b.is_lc=1 and b.id=".$row[csf('invoice_bill_id')],"export_lc_no"); 
							}
							 
							$total_ded_amnt_short += $row[csf('document_currency')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trr_<? echo $i; ?>">
								<td width="50"><? echo $i; ?></td>
								<td width="150"><p><? echo $lc_sc_no; ?></p></td>
								<td width="150"><p><? echo $bill_no; ?></p></td>
								<td width="100" align="center"><? echo change_date_format($row[csf('received_date')]); ?></td>
								<td align="right"><? echo number_format($row[csf('document_currency')],2); ?></td>
							</tr>
							<?
						$i++;
						}
					}
                    ?>
                    <tfoot class="tbl_bottom">
                    	<tr>
                            <td colspan="4" align="right">Total</td>
                            <td align="right"><? echo number_format($total_ded_amnt_short,2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" align="right">Grand Total</th>
                            <td align="right"><? echo number_format($tot_deduct_amnt+$total_ded_amnt_short,2); ?></td>
                        </tr>
                	</tfoot>
                </table>
            </div>
        </div>
	</fieldset>
</div>
<?
exit();
}


if($action=="dfc_paid_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:810px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:100%">
            <div style="width:700px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
                <br />  
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="150">Bill No.</th>
                        <th width="150">Supplier</th>
                        <th width="110">Bill Value</th>
                        <th width="100">Paid Date</th>
                        <th>Paid Value</th>
                    </thead>
                </table>
           </div>
           <div style="width:700px; overflow-y:scroll; max-height:230px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                <? 
                    $i=1; $btb_lc_id=array(); $total_paid_amnt = 0;
					
                    $sql_paid_lc="select b.import_mst_id as id, c.id as import_inovice_id, c.bank_ref, d.payment_date, d.accepted_ammount as amnt from com_export_lc a, com_btb_export_lc_attachment b, com_import_invoice_mst c,com_import_payment  d where a.id=b.lc_sc_id and b.is_lc_sc=0 and b.import_mst_id=c.btb_lc_id and c.is_lc=1 and c.id=d.invoice_id and d.payment_head=40 and d.adj_source=5 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by b.import_mst_id, d.id, d.payment_date, d.accepted_ammount, c.id, c.bank_ref";
                    $result_lc=sql_select($sql_paid_lc);
                    foreach($result_lc as $row) 
                    {
                        $total_paid_amnt += $row[csf('amnt')];
                        $btb_lc_id[]=$row[csf('id')];
                        
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
							
                        $supplier_id=return_field_value("supplier_id","com_btb_lc_master_details","id=".$row[csf('id')]);
						$bill_amnt=return_field_value("sum(current_acceptance_value) as val","com_import_invoice_dtls","import_invoice_id=".$row[csf('import_inovice_id')],"val");
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="150"><p><? echo $row[csf('bank_ref')]; ?></p></td>
							<td width="150"><p><? echo $supplier_details[$supplier_id]; ?></p></td>
							<td width="110" align="right"><? echo number_format($bill_amnt,2); ?></td>
							<td width="100" align="center"><? echo change_date_format($row[csf('payment_date')]); ?></td>
							<td align="right"><? echo number_format($row[csf('amnt')],2); ?></td>
						</tr>
						<?
                    $i++;
					}
					
                    $sql_paid_sc="select b.import_mst_id as id, c.id as import_inovice_id, c.bank_ref, d.payment_date, d.accepted_ammount as amnt from com_sales_contract a, com_btb_export_lc_attachment b, com_import_invoice_mst c,com_import_payment  d where a.id=b.lc_sc_id and b.is_lc_sc=1 and b.import_mst_id=c.btb_lc_id and c.is_lc=1 and c.id=d.invoice_id and d.payment_head=40 and d.adj_source=5 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by b.import_mst_id, d.id, d.payment_date, d.accepted_ammount, c.id, c.bank_ref";
                    $result_sc=sql_select($sql_paid_sc);
                    foreach($result_sc as $row_sc)
                    {
                        if(!in_array($row_sc[csf('id')],$btb_lc_id))
                        {
                            $total_paid_amnt += $row_sc[csf('amnt')];
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                        
							$supplier_id=return_field_value("supplier_id","com_btb_lc_master_details","id=".$row_sc[csf('id')]);
							$bill_amnt=return_field_value("sum(current_acceptance_value) as val","com_import_invoice_dtls","import_invoice_id=".$row_sc[csf('import_inovice_id')],"val");
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><p><? echo $row_sc[csf('bank_ref')]; ?></p></td>
                                <td width="150"><p><? echo $supplier_details[$supplier_id]; ?></p></td>
                                <td width="110" align="right"><? echo number_format($bill_amnt,2); ?></td>
                                <td width="100" align="center"><? echo change_date_format($row_sc[csf('payment_date')]); ?></td>
                                <td align="right"><? echo number_format($row_sc[csf('amnt')],2); ?></td>
                            </tr>
                            <?
                        $i++;
                        }
                    }
					
					$result='';
					if($db_type==0)
					{
						$btb_lc_id_atSight=return_field_value("group_concat(distinct(c.id)) as btb_lc_id","com_export_lc a, com_btb_export_lc_attachment b, com_btb_lc_master_details c","a.id=b.lc_sc_id and b.is_lc_sc=0 and b.import_mst_id=c.id and c.payterm_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0","btb_lc_id");
						
						$btb_sc_id_atSight=return_field_value("group_concat(distinct(c.id)) as btb_lc_id","com_sales_contract a, com_btb_export_lc_attachment b, com_btb_lc_master_details c","a.id=b.lc_sc_id and b.is_lc_sc=1 and b.import_mst_id=c.id and c.payterm_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0","btb_lc_id");
					}
					else
					{
						$btb_lc_id_atSight=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as btb_lc_id","com_export_lc a, com_btb_export_lc_attachment b, com_btb_lc_master_details c","a.id=b.lc_sc_id and b.is_lc_sc=0 and b.import_mst_id=c.id and c.payterm_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0","btb_lc_id");
						$btb_lc_id_atSight=implode(",",array_unique(explode(",",$btb_lc_id_atSight)));
					
						$btb_sc_id_atSight=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as btb_lc_id","com_sales_contract a, com_btb_export_lc_attachment b, com_btb_lc_master_details c","a.id=b.lc_sc_id and b.is_lc_sc=1 and b.import_mst_id=c.id and c.payterm_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0","btb_lc_id");
						$btb_lc_id_atSight=implode(",",array_unique(explode(",",$btb_lc_id_atSight)));
					}
					
					if($btb_lc_id_atSight!='' && $btb_sc_id_atSight!='')
                    {
                        $btb_lc_id_atSight=explode(",",$btb_lc_id_atSight);
                        $btb_sc_id_atSight=explode(",",$btb_sc_id_atSight);
                        $result = array_merge( $btb_lc_id_atSight, $btb_sc_id_atSight);
                        $result =implode(",",$result);
                    }
                    else if($btb_lc_id_atSight!='')
                    {
                        $result=$btb_lc_id_atSight;
                    }
                    else if($btb_sc_id_atSight!='')
                    {
                        $result=$btb_sc_id_atSight;
                    }
					
					if($result!="")
					{
						$result="'".implode("','",explode(",",$result))."'";
						$paid_sql_adj="select a.btb_lc_id, a.invoice_no, a.invoice_date, sum(b.current_acceptance_value) as paid_val from com_import_invoice_mst a, com_import_invoice_dtls b where a.id=b.import_invoice_id and a.retire_source=5 and a.btb_lc_id in(".$result.") and a.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.btb_lc_id, a.invoice_no, a.invoice_date";
						$paidArrayAdj=sql_select( $paid_sql_adj );
						
						foreach($paidArrayAdj as $row_btb_adj)
						{
							$total_paid_amnt += $row_btb_adj[csf('paid_val')];
							
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
						
							$supplier_id=return_field_value("supplier_id","com_btb_lc_master_details","id=".$row_btb_adj[csf('btb_lc_id')]);
								
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
								<td width="40"><? echo $i; ?></td>
								<td width="150"><p><? echo $row_btb_adj[csf('invoice_no')]; ?></p></td>
								<td width="150"><p><? echo $supplier_details[$supplier_id]; ?></p></td>
								<td width="110" align="right"><? echo number_format($row_btb_adj[csf('paid_val')],2); ?></td>
								<td width="100" align="center"><? echo change_date_format($row_btb_adj[csf('invoice_date')]); ?></td>
								<td align="right"><? echo number_format($row_btb_adj[csf('paid_val')],2); ?></td>
							</tr>
							<?
							$i++;
						}
					}
                    ?>
                     <tfoot class="tbl_bottom">
                        <td colspan="5" align="right">Total</td>
                        <td align="right"><? echo number_format($total_paid_amnt,2); ?></td>
                    </tfoot>
                </table>
            </div>
        </div>
    </fieldset>    
</div>
<?
exit();
}


if($action=="dfc_paid_info_adjust")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:810px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:100%">
            <div style="width:700px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
                <br />  
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="150">Bill No.</th>
                        <th width="150">Supplier</th>
                        <th width="110">Bill Value</th>
                        <th width="100">Paid Date</th>
                        <th>Paid Value</th>
                    </thead>
                </table>
           </div>
           <div style="width:700px; overflow-y:scroll; max-height:230px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                <? 
                    $i=1; $btb_lc_id=array(); $total_paid_amnt = 0;
                    $sql_paid_lc="select b.import_mst_id as id, c.id as import_inovice_id, c.bank_ref, d.payment_date, d.accepted_ammount as amnt from com_export_lc a, com_btb_export_lc_attachment b, com_import_invoice_mst c, com_import_payment d where a.id=b.lc_sc_id and b.is_lc_sc=0 and b.import_mst_id=c.btb_lc_id and c.is_lc=1 and c.id=d.invoice_id and d.payment_head=40 and d.adj_source<>5 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by b.import_mst_id, d.id, d.payment_date, d.accepted_ammount, c.id, c.bank_ref"; 
                    $result_lc=sql_select($sql_paid_lc);
                    foreach($result_lc as $row) 
                    {
                        $total_paid_amnt += $row[csf('amnt')];
                        $btb_lc_id[]=$row[csf('id')];
                        
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
							
                        $supplier_id=return_field_value("supplier_id","com_btb_lc_master_details","id=".$row[csf('id')]);
						$bill_amnt=return_field_value("sum(current_acceptance_value) as val","com_import_invoice_dtls","import_invoice_id=".$row[csf('import_inovice_id')],"val");
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="150"><p><? echo $row[csf('bank_ref')]; ?></p>&nbsp;</td>
							<td width="150"><p><? echo $supplier_details[$supplier_id]; ?>&nbsp;</p></td>
							<td width="110" align="right"><? echo number_format($bill_amnt,2); ?></td>
							<td width="100" align="center"><? echo change_date_format($row[csf('payment_date')]); ?></td>
							<td align="right"><? echo number_format($row[csf('amnt')],2); ?></td>
						</tr>
						<?
                    $i++;
					}
					
                    $sql_paid_sc="select b.import_mst_id as id, c.id as import_inovice_id, c.bank_ref, d.payment_date, d.accepted_ammount as amnt from com_sales_contract a, com_btb_export_lc_attachment b, com_import_invoice_mst c,com_import_payment  d where a.id=b.lc_sc_id and b.is_lc_sc=1 and b.import_mst_id=c.btb_lc_id and c.is_lc=1 and c.id=d.invoice_id and d.payment_head=40 and d.adj_source<>5 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by b.import_mst_id, d.id, d.payment_date, d.accepted_ammount, c.id, c.bank_ref";
                    $result_sc=sql_select($sql_paid_sc);
                    foreach($result_sc as $row_sc)
                    {
                        if(!in_array($row_sc[csf('id')],$btb_lc_id))
                        {
                            $total_paid_amnt += $row_sc[csf('amnt')];
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                        
							$supplier_id=return_field_value("supplier_id","com_btb_lc_master_details","id=".$row_sc[csf('id')]);
							$bill_amnt=return_field_value("sum(current_acceptance_value) as val","com_import_invoice_dtls","import_invoice_id=".$row_sc[csf('import_inovice_id')],"val");
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><p><? echo $row_sc[csf('bank_ref')]; ?>&nbsp;</p></td>
                                <td width="150"><p><? echo $supplier_details[$supplier_id]; ?>&nbsp;</p></td>
                                <td width="110" align="right"><? echo number_format($bill_amnt,2); ?></td>
                                <td width="100" align="center"><? echo change_date_format($row_sc[csf('payment_date')]); ?></td>
                                <td align="right"><? echo number_format($row_sc[csf('amnt')],2); ?></td>
                            </tr>
                            <?
                        $i++;
                        }
                    }
					
					$result='';
					if($db_type==0)
					{
						$btb_lc_id_atSight=return_field_value("group_concat(distinct(c.id)) as btb_lc_id","com_export_lc a, com_btb_export_lc_attachment b, com_btb_lc_master_details c","a.id=b.lc_sc_id and b.is_lc_sc=0 and b.import_mst_id=c.id and c.payterm_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0","btb_lc_id");
						
						$btb_sc_id_atSight=return_field_value("group_concat(distinct(c.id)) as btb_lc_id","com_sales_contract a, com_btb_export_lc_attachment b, com_btb_lc_master_details c","a.id=b.lc_sc_id and b.is_lc_sc=1 and b.import_mst_id=c.id and c.payterm_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0","btb_lc_id");
					}
					else
					{
						$btb_lc_id_atSight=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as btb_lc_id","com_export_lc a, com_btb_export_lc_attachment b, com_btb_lc_master_details c","a.id=b.lc_sc_id and b.is_lc_sc=0 and b.import_mst_id=c.id and c.payterm_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0","btb_lc_id");
						$btb_lc_id_atSight=implode(",",array_unique(explode(",",$btb_lc_id_atSight)));
						
						$btb_sc_id_atSight=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as btb_lc_id","com_sales_contract a, com_btb_export_lc_attachment b, com_btb_lc_master_details c","a.id=b.lc_sc_id and b.is_lc_sc=1 and b.import_mst_id=c.id and c.payterm_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0","btb_lc_id");
						$btb_sc_id_atSight=implode(",",array_unique(explode(",",$btb_sc_id_atSight)));
					}
					
					if($btb_lc_id_atSight!='' && $btb_sc_id_atSight!='')
                    {
                        $btb_lc_id_atSight=explode(",",$btb_lc_id_atSight);
                        $btb_sc_id_atSight=explode(",",$btb_sc_id_atSight);
                        $result = array_merge( $btb_lc_id_atSight, $btb_sc_id_atSight);
                        $result =implode(",",$result);	
                    }
                    else if($btb_lc_id_atSight!='')
                    {
                        $result=$btb_lc_id_atSight;
                    }
                    else if($btb_sc_id_atSight!='')
                    {
                        $result=$btb_sc_id_atSight;
                    }
					
					if($result!="")
					{
						$result="'".implode("','",explode(",",$result))."'";
						$paid_sql_adj="select a.btb_lc_id, a.invoice_no, a.invoice_date, sum(b.current_acceptance_value) as paid_val from com_import_invoice_mst a, com_import_invoice_dtls b where a.id=b.import_invoice_id and a.retire_source!=5 and a.btb_lc_id in($result) and a.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.btb_lc_id, a.invoice_no, a.invoice_date";
						$paidArrayAdj=sql_select( $paid_sql_adj );
						
						foreach($paidArrayAdj as $row_btb_adj)
						{
							$total_paid_amnt += $row_btb_adj[csf('paid_val')];
							
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
						
							$supplier_id=return_field_value("supplier_id","com_btb_lc_master_details","id=".$row_btb_adj[csf('btb_lc_id')]);
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
								<td width="40"><? echo $i; ?></td>
								<td width="150"><p><? echo $row_btb_adj[csf('invoice_no')]; ?>&nbsp;</p></td>
								<td width="150"><p><? echo $supplier_details[$supplier_id]; ?>&nbsp;</p></td>
								<td width="110" align="right"><? echo number_format($row_btb_adj[csf('paid_val')],2); ?></td>
								<td width="100" align="center"><? echo change_date_format($row_btb_adj[csf('invoice_date')]); ?></td>
								<td align="right"><? echo number_format($row_btb_adj[csf('paid_val')],2); ?></td>
							</tr>
							<?
							$i++;
						}
						
					}
                    ?>
                     <tfoot class="tbl_bottom">
                        <td colspan="5" align="right">Total</td>
                        <td align="right"><? echo number_format($total_paid_amnt,2); ?></td>
                    </tfoot>
                </table>
            </div>
        </div>
    </fieldset>    
</div>
<?
exit();
}

disconnect($con);
?>
