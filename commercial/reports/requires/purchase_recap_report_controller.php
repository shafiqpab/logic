<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$seource_des_array=array(3=>"Non EPZ",4=>"Non EPZ",5=>"Abroad",6=>"Abroad",11=>"EPZ",12=>"EPZ");
$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');

$paymentDataArray=sql_select("SELECT invoice_id, payment_date, sum(accepted_ammount) as accepted_ammount, payment_date FROM com_import_payment where payment_head=40 and status_active=1 and is_deleted=0 group by invoice_id,payment_date");
$payment_arr = array();
foreach($paymentDataArray as $row)
{
	$payment_arr[$row[csf('invoice_id')]]['amnt'] = $row[csf('accepted_ammount')];
	$payment_arr[$row[csf('invoice_id')]]['date'] = $row[csf('payment_date')];
}


if($action=="pi_no_popup")
{
	echo load_html_head_contents("PI Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
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
			
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				//selected_no.push( str );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					//alert(selected_id);
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				//selected_no.splice( i, 1 ); 
			}

			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				//num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			//num 	= num.substr( 0, num.length - 1 );
			//alert(name);
			$('#hide_pi_id').val( id );
			$('#hide_pi_no').val( name ); 
			//$('#hide_wo_no').val( num );
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter PI No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_pi_id" id="hide_pi_id" value="" />
                    <input type="hidden" name="hide_pi_no" id="hide_pi_no" value="" />
                </thead>
                <tbody>
                	<tr>                                      
                        <td align="center">	
                    	<?
                       		//$search_by_arr=array(1=>"Job No",2=>"Style Ref");
                       		$search_by_arr=array(3=>"PI No");
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_name; ?>'+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $item_category_id; ?>', 'create_pi_no_search_list_view', 'search_div', 'purchase_recap_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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

if($action=="create_pi_no_search_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$search_type,$search_value,$cbo_year,$item_category_id)=explode('**',$data);

	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0){
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}else{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
	}
	if($db_type==0) if($cbo_year!=0) $job_cond=" and year(a.insert_date)='$cbo_year'";
	else if($cbo_year!=0) $job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	$sql_cond =" and a.importer_id=$company";	
	$sql_cond .=" and a.item_category_id=$item_category_id";	
	if($search_type==3 && $search_value!=''){
		$sql_cond .=" and a.pi_number like '%$search_value%'";	
	}


	if($db_type==0) $year_field="YEAR(a.insert_date) as job_year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as job_year";
	else $year_field="";

	$sql_pi="SELECT a.id, a.pi_number
	from com_pi_master_details a
	where a.status_active=1 and a.is_deleted=0 $sql_cond order by a.id desc";

	echo create_list_view("list_view", "PI No, System ID","190,160","400","200",0, $sql_pi , "js_set_value", "id,pi_number", "", 1, "0,0", $arr, "pi_number,id", "","setFilterGrid('list_view',-1)","0","",1) ;

	echo "<input type='hidden' id='hide_pi_id' />";
	echo "<input type='hidden' id='hide_pi_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	//var style_des='<? echo $txt_style_ref;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    <?
	exit();
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$container_status = array(1=>"FCL", 2=>"LCL");
	$container_size = array(1=>"20 ft GP", 2=>"20 ft HQ", 3=>"40 ft GP", 4=>"40 ft HQ");
	$acc_status=str_replace("'","",$cbo_receive_status);
	$receive_status=str_replace("'","",$cbo_receive_status);
	$pi_no_id=str_replace("'","", $pi_no_id);
	// echo $pi_no_id; die;
	$pi_no=str_replace("'","", $pi_no);
	$item_cate=str_replace("'","",$cbo_item_category_id);
	$entry_form=$category_wise_entry_form[$item_cate];
	$var='';
	
	//echo $cbo_item_category_id;die;

	$cbo_source_id = str_replace("'","",$cbo_source_id);
	
	//echo $cbo_source_id;die;
	$strings=$cbo_source_id;
	$stringMulti_1="";
	$stringMulti_2="";
	$string=explode(",",$strings);
	foreach($string as $str)
	{
		if(strlen($str)==1)
		{
			$stringMulti.="0".$str.',';
		}
		else
		{
			if(strlen($str)>1)
			{
				$stringMulti.=$str.',';
			}

		}
	}
	//$stringMultis_1=chop($stringMulti_1,",");
	$addStringMulti=chop($stringMulti,",");
	if($addStringMulti!=""){ $import_source_cond = " and c.lc_category in($addStringMulti)";}else{ $import_source_cond = "";}

	//$conversion_rateArray=sql_select("select conversion_rate from currency_conversion_rate where id=(select max(ID) from currency_conversion_rate where currency=2)");
	
	//echo $type; die;
	if (str_replace("'","",$type)==1)  // Show
	{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process ));

		$item_cate=str_replace("'","",$cbo_item_category_id);
		$cbo_date_type=str_replace("'","",$cbo_date_type);
		if(str_replace("'","",$cbo_issuing_bank)==0) $issuing_bank="%%"; else $issuing_bank=str_replace("'","",$cbo_issuing_bank);
		if(str_replace("'","",$cbo_lc_type_id)==0) $lc_type_id="%%"; else $lc_type_id=str_replace("'","",$cbo_lc_type_id);
		
		
		$rcv_return_sql=sql_select("select a.received_id, b.item_category, a.pi_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount 
		from inv_issue_master a, inv_transaction b
		where a.id=b.mst_id and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and b.company_id=$cbo_company_name and b.item_category=$cbo_item_category_id and a.received_id>0
		group by a.received_id, b.item_category, a.pi_id");
		
		$return_data=array();
		foreach($rcv_return_sql as $row)
		{
			/*$return_data[$row[csf("received_id")]]["cons_quantity"]=$row[csf("cons_quantity")];
			$return_data[$row[csf("received_id")]]["cons_amount"]=$row[csf("cons_amount")];*/
			
			$return_data[$row[csf("pi_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
			$return_data[$row[csf("pi_id")]]["cons_amount"]+=$row[csf("cons_amount")];
		}


		$recvDataArray=sql_select("select a.pi_wo_batch_no, b.exchange_rate, min(b.receive_date) as receive_date, sum(a.order_qnty) as qnty, sum(a.order_amount) as amnt, sum(a.cons_quantity) as cons_quantity, sum(a.cons_amount) as cons_amount
		from inv_transaction a, inv_receive_master b  
		where a.item_category=$cbo_item_category_id and b.id=a.mst_id and a.receive_basis=1 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and  b.receive_basis=1 and b.exchange_rate>0 and a.company_id=$cbo_company_name and a.item_category=$cbo_item_category_id
		group by a.pi_wo_batch_no, b.exchange_rate");

		$receive_arr = array();
		foreach($recvDataArray as $row)
		{
			$receive_arr[$row[csf('pi_wo_batch_no')]]['receive_date'] = $row[csf('receive_date')];
			$receive_arr[$row[csf('pi_wo_batch_no')]]['exchange_rate'] = $row[csf('exchange_rate')];
			$receive_arr[$row[csf('pi_wo_batch_no')]]['qnty'] += $row[csf('qnty')]-$return_data[$row[csf("pi_wo_batch_no")]]["cons_quantity"];
			$receive_arr[$row[csf('pi_wo_batch_no')]]['amnt'] += $row[csf('amnt')]-($return_data[$row[csf("pi_wo_batch_no")]]["cons_amount"]/$row[csf('exchange_rate')]);
		}
		
		/*$AfterRecvPIDataArray=sql_select("select c.id as pi_id,d.exchange_rate, sum(cons_quantity) as qnty, sum(cons_amount) as amnt
        from inv_transaction a, com_pi_item_details b, com_pi_master_details c , inv_receive_master d 
        where a.id=b.work_order_dtls_id and b.pi_id=c.id  and d.id=a.mst_id  and a.receive_basis=2 and c.goods_rcv_status=1  and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and c.entry_form=$entry_form and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  d.receive_basis=2 and d.exchange_rate>0 and d.status_active=1 and d.is_deleted=0 
		group by c.id,d.exchange_rate ");*/
		
		$AfterRecvPIDataArray=sql_select("select d.booking_id as pi_id, d.exchange_rate, min(d.receive_date) as receive_date, sum(a.order_qnty) as qnty, sum(a.order_amount) as amnt, sum(a.cons_quantity) as cons_quantity, sum(a.cons_amount) as cons_amount
        from inv_transaction a, inv_receive_master d 
        where d.id=a.mst_id and a.receive_basis=2 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and d.receive_basis=2 and d.exchange_rate>0 and a.company_id=$cbo_company_name and a.item_category=$cbo_item_category_id and d.status_active=1 and d.is_deleted=0
		group by d.booking_id, d.exchange_rate");

		$AfterRecv_arr = array();
		foreach($AfterRecvPIDataArray as $row)
		{
			$AfterRecv_arr[$row[csf('pi_id')]]['exchange_rate'] = $row[csf('exchange_rate')];
			$AfterRecv_arr[$row[csf('pi_id')]]['receive_date'] = $row[csf('receive_date')];
			$AfterRecv_arr[$row[csf('pi_id')]]['qnty'] += $row[csf('qnty')]-$return_data[$row[csf("pi_id")]]["cons_quantity"];
			$AfterRecv_arr[$row[csf('pi_id')]]['amnt'] += $row[csf('amnt')]-($return_data[$row[csf("pi_id")]]["cons_amount"]/$row[csf('exchange_rate')]);
		}
		
		$lc_sc_sql=sql_select("select a.id, a.export_lc_no as lc_sc_no, a.lc_date as lc_sc_date, b.import_mst_id from com_export_lc a, com_btb_export_lc_attachment b 
		where a.id=b.lc_sc_id and b.is_lc_sc=0 and a.beneficiary_name=$cbo_company_name
			union all
			select a.id, a.contract_no as lc_sc_no, a.contract_date as lc_sc_date, b.import_mst_id from com_sales_contract a, com_btb_export_lc_attachment b where a.id=b.lc_sc_id and b.is_lc_sc=1 and a.beneficiary_name=$cbo_company_name");

		$lc_sc_data = array();
		foreach($lc_sc_sql as $row)
		{
			$lc_sc_data[$row[csf('import_mst_id')]]['lc_sc_no'] = $row[csf('lc_sc_no')];
			$lc_sc_data[$row[csf('import_mst_id')]]['lc_sc_date'] = $row[csf('lc_sc_date')];
		}

		ob_start();
		if($item_cate==5 || $item_cate==6 || $item_cate==7 || $item_cate==22)
		{
			$table_width=5750;
			$colspan=15;
			$group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		}
		else
		{
			$table_width=5650;
			$colspan=14;
		}
		
		?>
	    <div style="width:<? echo $table_width+30; ?>px; margin-left:10px">
	        <fieldset style="width:100%;">
	            <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption">
	                <tr>
	                   <td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
	                </tr>
	                <tr>
	                   <td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
	                </tr>
	            </table>
	            <br />
	            <table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
	                <thead>
	                    <tr>
	                        <th colspan="<? echo $colspan; ?>" >PI Details</th>
	                        <th colspan="13">LC Details</th>
	                        <th colspan="24">Invoice Details</th>
                            <th colspan="4">Payment Details</th>
                            <th colspan="4">Matarials Received Information</th>
	                    </tr>
	                    <tr>
                        	<!--pi info tot collum 13 width 1180--> 
	                        <th width="40">SL</th>
	                        <th width="120">PI No</th>
	                        <th width="80">PI Date</th>
	                        <th width="80">Last Ship Date</th>
	                        <th width="140">Supplier Name</th>
	                        <th width="100">Item Category</th>
	                        <? if($item_cate==5 || $item_cate==6 || $item_cate==7 || $item_cate==22)
	                        {
	                        	?><th width="100">Item Group</th><?
	                        } 
	                        ?>
	                        <th width="100">HS Code</th>
	                        <th width="320">Goods Description</th>
	                        <th width="60">UOM</th>
	                        <th width="80">PI Quantity</th>
	                        <th width="80">Unit Price</th>
	                        <th width="100">PI Value</th>
	                        <th width="80">Currency</th>
                            <th width="100">Indentor Name</th>
                            
                            
                            <!--lc info tot collum 13 width 1120--> 
	                        <th width="80">LC Date</th>
	                        <th width="120">LC No</th>
	                        <th width="170">FRN /Local</th>
	                        <th width="120">Issuing Bank</th>
	                        <th width="80">Pay Term</th>
	                        <th width="50">Tenor</th>
	                        <th width="80">Currency</th>
	                        <th width="100">LC Amount</th>
	                        <th width="80">Shipment Date</th>
	                        <th width="80">Expiry Date</th>
	                        <th width="120">Export L/c No</th>
	                        <th width="80">Export L/c Date</th>
                            <th width="80">ETD Committed</th>
                            
                            <!--acceptance info tot collum 23 width 2080--> 
	                        <th width="80">ETD Actual</th>
	                        <th width="80">ETA Advice</th>
	                        <th width="80">ETA Actual</th>
	                        <th width="120">Port of Loading</th>
	                        <th width="120">Port of Discharge</th>
	                        <th width="80">ETA Date</th>
	                        <th width="120">Invoice No</th>
	                        <th width="80">Invoice Date</th>
	                        <th width="80">Incoterm</th>
	                        <th width="100">Incoterm Place</th>
	                        <th width="100">B/L No</th>
	                        <th width="80">BL Date</th>
                            <th width="100">Mother Vassel</th>
                            <th width="100">Feedar Vassel</th>
                            <th width="100">Continer No</th>
                            <th width="80">Continer Status</th>
	                        <th width="80">Continer Size</th>
	                        <th width="80">Pkg Qty</th>
	                        <th width="80">NN Doc Received Date</th>
	                        <th width="100">Original Doc Received Date</th>
	                        <th width="80">Doc Send to CNF</th>
                            <th width="100">Bill Of Entry No</th>
                            <th width="80">Bill Of Entry Date</th>
                            <th width="80">Release Date</th>
                            
							<!--payment info tot collum 4 width 340--> 
	                        <th width="80">Maturity Date</th>
	                        <th width="80">Maturity Month</th>
	                        <th width="80">Payment Date</th>
							<th width="100">Paid Amount</th>
                            
                            <!--payment info tot collum 4 width 360-->
	                        <th width="80">MRR Date</th>
                            <th width="80">MRR Qnty</th>
	                        <th width="100">MRR Value</th>
	                        <th>Short Value</th>
	                    </tr>
	                </thead>
	            </table>

	            <div style="width:<? echo $table_width+20; ?>px; max-height:300px; overflow-y:scroll" id="scroll_body">
			 		<table width="<? echo $table_width; ?>" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_body">
	                <?
					$str_cond="";
					if(str_replace("'","",$cbo_issuing_bank)>0) $str_cond .=" and d.issuing_bank_id=$cbo_issuing_bank";
					if($cbo_source_id!="")
					{
						$source_id_string="";
						$cbo_source_id_arr=explode(",",$cbo_source_id);
						foreach($cbo_source_id_arr as $sor_id)
						{
							//str_pad(string,length,pad_string,pad_type)
							$source_id_string.="'".str_pad($sor_id,2,0,STR_PAD_LEFT)."',";
						}
						$source_id_string=chop($source_id_string,",");
						$str_cond .=" and d.lc_category in($source_id_string)";
					}
					
					$ref_close_cond="";
					if($receive_status==4) $ref_close_cond=" and b.ref_closing_status<>1  and d.ref_closing_status<>1";
					if($pi_no_id != "")
					{
						$pi_sql_cond = " and b.id in($pi_no_id)";
					}
						
					if($cbo_date_type==5 || $cbo_date_type==6)
					{
						
						if($cbo_date_type==5) //Maturity Date
						{
							if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") $str_cond.=" and f.maturity_date between $txt_date_from and $txt_date_to";
						}
						else if($cbo_date_type==6) //Maturity Insert Date
						{
							if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
							{
								if($db_type==0)
								{
									$str_cond.=" and f.insert_date between '".str_replace("'","",$txt_date_from)."' and '".str_replace("'","",$txt_date_to)." 23:59:59'";
								}
								else if($db_type==2)
								{
									$str_cond.=" and f.insert_date between '".str_replace("'","",$txt_date_from)."' and '".str_replace("'","",$txt_date_to)." 11:59:59 PM'";
								}
							}
						}
						
						//echo $pi_sql_cond."maakak"; die;
						
						if($item_cate==4)
						{
							
							
							$sql="select a.work_order_id, a.id as pi_dtls_id, a.item_category_id, a.item_group, b.id as pi_id, b.supplier_id, b.pi_number, b.pi_date, b.last_shipment_date, b.currency_id, b.intendor_name, b.goods_rcv_status, a.hs_code, a.item_prod_id, a.item_description, a.fabric_composition, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_type, a.count_name, a.uom, a.quantity, a.rate, a.amount, a.net_pi_rate, a.net_pi_amount, c.id as btb_dtls_id, d.id as btb_id, d.lc_value, d.lc_date, d.lc_number, d.issuing_bank_id, d.payterm_id, d.tenor, d.currency_id as btb_currency_id, d.last_shipment_date as btb_shipment_date, d.lc_expiry_date, d.lc_type_id, d.etd_date, d.lc_category, e.id as inv_dtls_id, e.current_acceptance_value as inv_value, f.id as invoice_id, f.invoice_no, f.invoice_date, f.bill_no, f.bill_date, f.doc_to_cnf, f.original_doc_receive_date, f.feeder_vessel, f.mother_vessel, f.eta_date, f.port_of_loading, f.port_of_discharge, f.copy_doc_receive_date, f.bill_of_entry_no, f.bill_of_entry_date, f.pkg_quantity, f.container_no, f.maturity_date, f.inco_term, f.inco_term_place, f.company_acc_date, f.bank_acc_date, f.document_value, f.etd_actual, f.eta_advice, f.eta_actual, f.container_status, f.container_size, f.release_date, f.maturity_date 
							from com_pi_item_details a, com_pi_master_details b, com_btb_lc_pi c, com_btb_lc_master_details d, com_import_invoice_dtls e, com_import_invoice_mst f
							where a.pi_id=b.id and b.id=c.pi_id and c.com_btb_lc_master_details_id=d.id and d.id=e.btb_lc_id and b.id=e.pi_id and e.import_invoice_id=f.id and a.item_category_id=$item_cate and b.importer_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $str_cond $ref_close_cond $pi_sql_cond
							union all
							select a.work_order_id, a.id as pi_dtls_id, a.item_category_id, a.item_group, b.id as pi_id, b.supplier_id, b.pi_number, b.pi_date, b.last_shipment_date, b.currency_id, b.intendor_name, b.goods_rcv_status, a.hs_code, a.item_prod_id, a.item_description, a.fabric_composition, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_type, a.count_name, a.uom, a.quantity, a.rate, a.amount, a.net_pi_rate, a.net_pi_amount, c.id as btb_dtls_id, d.id as btb_id, d.lc_value, d.lc_date, d.lc_number, d.issuing_bank_id, d.payterm_id, d.tenor, d.currency_id as btb_currency_id, d.last_shipment_date as btb_shipment_date, d.lc_expiry_date, d.lc_type_id, d.etd_date, d.lc_category, e.id as inv_dtls_id, e.current_acceptance_value as inv_value, f.id as invoice_id, f.invoice_no, f.invoice_date, f.bill_no, f.bill_date, f.doc_to_cnf, f.original_doc_receive_date, f.feeder_vessel, f.mother_vessel, f.eta_date, f.port_of_loading, f.port_of_discharge, f.copy_doc_receive_date, f.bill_of_entry_no, f.bill_of_entry_date, f.pkg_quantity, f.container_no, f.maturity_date, f.inco_term, f.inco_term_place, f.company_acc_date, f.bank_acc_date, f.document_value, f.etd_actual, f.eta_advice, f.eta_actual, f.container_status, f.container_size, f.release_date, f.maturity_date 
							from wo_non_order_info_dtls p, com_pi_item_details a, com_pi_master_details b, com_btb_lc_pi c, com_btb_lc_master_details d, com_import_invoice_dtls e, com_import_invoice_mst f
							where p.id=a.work_order_dtls_id and a.pi_id=b.id and b.id=c.pi_id and c.com_btb_lc_master_details_id=d.id and d.id=e.btb_lc_id and b.id=e.pi_id and e.import_invoice_id=f.id and p.item_category_id=$item_cate and b.importer_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $str_cond $ref_close_cond $pi_sql_cond";
						}
						else
						{
							$sql="select a.work_order_id, a.id as pi_dtls_id, a.item_category_id, a.item_group, b.id as pi_id, b.supplier_id, b.pi_number, b.pi_date, b.last_shipment_date, b.currency_id, b.intendor_name, b.goods_rcv_status, a.hs_code, a.item_prod_id, a.item_description, a.fabric_composition, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_type, a.count_name, a.uom, a.quantity, a.rate, a.amount, a.net_pi_rate, a.net_pi_amount, c.id as btb_dtls_id, d.id as btb_id, d.lc_value, d.lc_date, d.lc_number, d.issuing_bank_id, d.payterm_id, d.tenor, d.currency_id as btb_currency_id, d.last_shipment_date as btb_shipment_date, d.lc_expiry_date, d.lc_type_id, d.etd_date, d.lc_category, e.id as inv_dtls_id, e.current_acceptance_value as inv_value, f.id as invoice_id, f.invoice_no, f.invoice_date, f.bill_no, f.bill_date, f.doc_to_cnf, f.original_doc_receive_date, f.feeder_vessel, f.mother_vessel, f.eta_date, f.port_of_loading, f.port_of_discharge, f.copy_doc_receive_date, f.bill_of_entry_no, f.bill_of_entry_date, f.pkg_quantity, f.container_no, f.maturity_date, f.inco_term, f.inco_term_place, f.company_acc_date, f.bank_acc_date, f.document_value, f.etd_actual, f.eta_advice, f.eta_actual, f.container_status, f.container_size, f.release_date, f.maturity_date 
							from com_pi_item_details a, com_pi_master_details b, com_btb_lc_pi c, com_btb_lc_master_details d, com_import_invoice_dtls e, com_import_invoice_mst f
							where a.pi_id=b.id and b.id=c.pi_id and c.com_btb_lc_master_details_id=d.id and d.id=e.btb_lc_id and b.id=e.pi_id and e.import_invoice_id=f.id and a.item_category_id=$item_cate and b.importer_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $str_cond $ref_close_cond $pi_sql_cond";
						}
						
					}
					else if($cbo_date_type==3 || $cbo_date_type==4)
					{
						if($cbo_date_type==3) //Maturity Date
						{
							if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") $str_cond.=" and d.lc_date between $txt_date_from and $txt_date_to";
						}
						else if($cbo_date_type==4) //Maturity Insert Date
						{
							if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
							{
								if($db_type==0)
								{
									$str_cond.=" and d.insert_date between '".str_replace("'","",$txt_date_from)."' and '".str_replace("'","",$txt_date_to)." 23:59:59'";
								}
								else if($db_type==2)
								{
									$str_cond.=" and d.insert_date between '".str_replace("'","",$txt_date_from)."' and '".str_replace("'","",$txt_date_to)." 11:59:59 PM'";
								}
							}
						}
						
						if($item_cate==4)
						{
							$sql="select a.work_order_id, a.id as pi_dtls_id, a.item_category_id, a.item_group, b.id as pi_id, b.supplier_id, b.pi_number, b.pi_date, b.last_shipment_date, b.currency_id, b.intendor_name, b.goods_rcv_status, a.hs_code, a.item_prod_id, a.item_description, a.fabric_composition, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_type, a.count_name, a.uom, a.quantity, a.rate, a.amount, a.net_pi_rate, a.net_pi_amount, c.id as btb_dtls_id, d.id as btb_id, d.lc_value, d.lc_date, d.lc_number, d.issuing_bank_id, d.payterm_id, d.tenor, d.currency_id as btb_currency_id, d.last_shipment_date as btb_shipment_date, d.lc_expiry_date, d.lc_type_id, d.etd_date, d.lc_category, e.id as inv_dtls_id, e.current_acceptance_value as inv_value, f.id as invoice_id, f.invoice_no, f.invoice_date, f.bill_no, f.bill_date, f.doc_to_cnf, f.original_doc_receive_date, f.feeder_vessel, f.mother_vessel, f.eta_date, f.port_of_loading, f.port_of_discharge, f.copy_doc_receive_date, f.bill_of_entry_no, f.bill_of_entry_date, f.pkg_quantity, f.container_no, f.maturity_date, f.inco_term, f.inco_term_place, f.company_acc_date, f.bank_acc_date, f.document_value, f.etd_actual, f.eta_advice, f.eta_actual, f.container_status, f.container_size, f.release_date, f.maturity_date 
							from com_pi_item_details a, com_pi_master_details b, com_btb_lc_pi c, com_btb_lc_master_details d 
							left join com_import_invoice_dtls e on d.id=e.btb_lc_id and e.status_active=1 and e.is_deleted=0
							left join com_import_invoice_mst f on e.import_invoice_id=f.id and f.status_active=1 and f.is_deleted=0 
							where a.pi_id=b.id and b.id=c.pi_id and c.com_btb_lc_master_details_id=d.id and a.item_category_id=$item_cate and b.importer_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $str_cond $ref_close_cond $pi_sql_cond
							union all
							select a.work_order_id, a.id as pi_dtls_id, a.item_category_id, a.item_group, b.id as pi_id, b.supplier_id, b.pi_number, b.pi_date, b.last_shipment_date, b.currency_id, b.intendor_name, b.goods_rcv_status, a.hs_code, a.item_prod_id, a.item_description, a.fabric_composition, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_type, a.count_name, a.uom, a.quantity, a.rate, a.amount, a.net_pi_rate, a.net_pi_amount, c.id as btb_dtls_id, d.id as btb_id, d.lc_value, d.lc_date, d.lc_number, d.issuing_bank_id, d.payterm_id, d.tenor, d.currency_id as btb_currency_id, d.last_shipment_date as btb_shipment_date, d.lc_expiry_date, d.lc_type_id, d.etd_date, d.lc_category, e.id as inv_dtls_id, e.current_acceptance_value as inv_value, f.id as invoice_id, f.invoice_no, f.invoice_date, f.bill_no, f.bill_date, f.doc_to_cnf, f.original_doc_receive_date, f.feeder_vessel, f.mother_vessel, f.eta_date, f.port_of_loading, f.port_of_discharge, f.copy_doc_receive_date, f.bill_of_entry_no, f.bill_of_entry_date, f.pkg_quantity, f.container_no, f.maturity_date, f.inco_term, f.inco_term_place, f.company_acc_date, f.bank_acc_date, f.document_value, f.etd_actual, f.eta_advice, f.eta_actual, f.container_status, f.container_size, f.release_date, f.maturity_date 
							from wo_non_order_info_dtls p, com_pi_item_details a, com_pi_master_details b, com_btb_lc_pi c, com_btb_lc_master_details d 
							left join com_import_invoice_dtls e on d.id=e.btb_lc_id and e.status_active=1 and e.is_deleted=0
							left join com_import_invoice_mst f on e.import_invoice_id=f.id and f.status_active=1 and f.is_deleted=0
							where p.id=a.work_order_dtls_id and a.pi_id=b.id and b.id=c.pi_id and c.com_btb_lc_master_details_id=d.id and p.item_category_id=$item_cate and b.importer_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $str_cond $ref_close_cond $pi_sql_cond";
						}
						else
						{
							$sql="select a.work_order_id, a.id as pi_dtls_id, a.item_category_id, a.item_group, b.id as pi_id, b.supplier_id, b.pi_number, b.pi_date, b.last_shipment_date, b.currency_id, b.intendor_name, b.goods_rcv_status, a.hs_code, a.item_prod_id, a.item_description, a.fabric_composition, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_type, a.count_name, a.uom, a.quantity, a.rate, a.amount, a.net_pi_rate, a.net_pi_amount, c.id as btb_dtls_id, d.id as btb_id, d.lc_value, d.lc_date, d.lc_number, d.issuing_bank_id, d.payterm_id, d.tenor, d.currency_id as btb_currency_id, d.last_shipment_date as btb_shipment_date, d.lc_expiry_date, d.lc_type_id, d.etd_date, d.lc_category, e.id as inv_dtls_id, e.current_acceptance_value as inv_value, f.id as invoice_id, f.invoice_no, f.invoice_date, f.bill_no, f.bill_date, f.doc_to_cnf, f.original_doc_receive_date, f.feeder_vessel, f.mother_vessel, f.eta_date, f.port_of_loading, f.port_of_discharge, f.copy_doc_receive_date, f.bill_of_entry_no, f.bill_of_entry_date, f.pkg_quantity, f.container_no, f.maturity_date, f.inco_term, f.inco_term_place, f.company_acc_date, f.bank_acc_date, f.document_value, f.etd_actual, f.eta_advice, f.eta_actual, f.container_status, f.container_size, f.release_date, f.maturity_date 
							from com_pi_item_details a, com_pi_master_details b, com_btb_lc_pi c, com_btb_lc_master_details d 
							left join com_import_invoice_dtls e on d.id=e.btb_lc_id and e.status_active=1 and e.is_deleted=0
							left join com_import_invoice_mst f on e.import_invoice_id=f.id and f.status_active=1 and f.is_deleted=0 
							where a.pi_id=b.id and b.id=c.pi_id and c.com_btb_lc_master_details_id=d.id and a.item_category_id=$item_cate and b.importer_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $str_cond $ref_close_cond $pi_sql_cond";
						}
					}
					else
					{
						if($cbo_date_type==1) //Maturity Date
						{
							if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") $str_cond.=" and b.pi_date between $txt_date_from and $txt_date_to";
						}
						else if($cbo_date_type==2) //Maturity Insert Date
						{
							if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
							{
								if($db_type==0)
								{
									$str_cond.=" and b.insert_date between '".str_replace("'","",$txt_date_from)."' and '".str_replace("'","",$txt_date_to)." 23:59:59'";
								}
								else if($db_type==2)
								{
									$str_cond.=" and b.insert_date between '".str_replace("'","",$txt_date_from)."' and '".str_replace("'","",$txt_date_to)." 11:59:59 PM'";
								}
							}
						}
						
						$ref_close_pi="";
						if($receive_status==4) $ref_close_pi=" and b.ref_closing_status<>1";
						$ref_close_btb="";
						if($receive_status==4) $ref_close_btb=" and d.ref_closing_status<>1";
						
						if($item_cate==4)
						{
							$sql="select a.work_order_id, a.id as pi_dtls_id, a.item_category_id, a.item_group, b.id as pi_id, b.supplier_id, b.pi_number, b.pi_date, b.last_shipment_date, b.currency_id, b.intendor_name, b.goods_rcv_status, a.hs_code, a.item_prod_id, a.item_description, a.fabric_composition, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_type, a.count_name, a.uom, a.quantity, a.rate, a.amount, a.net_pi_rate, a.net_pi_amount, c.id as btb_dtls_id, d.id as btb_id, d.lc_value, d.lc_date, d.lc_number, d.issuing_bank_id, d.payterm_id, d.tenor, d.currency_id as btb_currency_id, d.last_shipment_date as btb_shipment_date, d.lc_expiry_date, d.lc_type_id, d.etd_date, d.lc_category, e.id as inv_dtls_id, e.current_acceptance_value as inv_value, f.id as invoice_id, f.invoice_no, f.invoice_date, f.bill_no, f.bill_date, f.doc_to_cnf, f.original_doc_receive_date, f.feeder_vessel, f.mother_vessel, f.eta_date, f.port_of_loading, f.port_of_discharge, f.copy_doc_receive_date, f.bill_of_entry_no, f.bill_of_entry_date, f.pkg_quantity, f.container_no, f.maturity_date, f.inco_term, f.inco_term_place, f.company_acc_date, f.bank_acc_date, f.document_value, f.etd_actual, f.eta_advice, f.eta_actual, f.container_status, f.container_size, f.release_date, f.maturity_date 
							from com_pi_item_details a, com_pi_master_details b
							left join com_btb_lc_pi c on b.id=c.pi_id and c.status_active=1 and c.is_deleted=0  
							left join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1 and d.is_deleted=0 $ref_close_btb 
							left join com_import_invoice_dtls e on d.id=e.btb_lc_id and e.status_active=1 and e.is_deleted=0
							left join com_import_invoice_mst f on e.import_invoice_id=f.id and f.status_active=1 and f.is_deleted=0 
							where a.pi_id=b.id and a.item_category_id=$item_cate and b.importer_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond $ref_close_pi $pi_sql_cond
							union all
							select a.work_order_id, a.id as pi_dtls_id, a.item_category_id, a.item_group, b.id as pi_id, b.supplier_id, b.pi_number, b.pi_date, b.last_shipment_date, b.currency_id, b.intendor_name, b.goods_rcv_status, a.hs_code, a.item_prod_id, a.item_description, a.fabric_composition, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_type, a.count_name, a.uom, a.quantity, a.rate, a.amount, a.net_pi_rate, a.net_pi_amount, c.id as btb_dtls_id, d.id as btb_id, d.lc_value, d.lc_date, d.lc_number, d.issuing_bank_id, d.payterm_id, d.tenor, d.currency_id as btb_currency_id, d.last_shipment_date as btb_shipment_date, d.lc_expiry_date, d.lc_type_id, d.etd_date, d.lc_category, e.id as inv_dtls_id, e.current_acceptance_value as inv_value, f.id as invoice_id, f.invoice_no, f.invoice_date, f.bill_no, f.bill_date, f.doc_to_cnf, f.original_doc_receive_date, f.feeder_vessel, f.mother_vessel, f.eta_date, f.port_of_loading, f.port_of_discharge, f.copy_doc_receive_date, f.bill_of_entry_no, f.bill_of_entry_date, f.pkg_quantity, f.container_no, f.maturity_date, f.inco_term, f.inco_term_place, f.company_acc_date, f.bank_acc_date, f.document_value, f.etd_actual, f.eta_advice, f.eta_actual, f.container_status, f.container_size, f.release_date, f.maturity_date 
							from wo_non_order_info_dtls p, com_pi_item_details a, com_pi_master_details b
							left join com_btb_lc_pi c on b.id=c.pi_id and c.status_active=1 and c.is_deleted=0  
							left join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1 and d.is_deleted=0 $ref_close_btb
							left join com_import_invoice_dtls e on d.id=e.btb_lc_id and e.status_active=1 and e.is_deleted=0
							left join com_import_invoice_mst f on e.import_invoice_id=f.id and f.status_active=1 and f.is_deleted=0
							where p.id=a.work_order_dtls_id and a.pi_id=b.id and p.item_category_id=$item_cate and b.importer_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond $ref_close_pi $pi_sql_cond";
						}
						else
						{
							$sql="select a.work_order_id, a.id as pi_dtls_id, a.item_category_id, a.item_group, b.id as pi_id, b.supplier_id, b.pi_number, b.pi_date, b.last_shipment_date, b.currency_id, b.intendor_name, b.goods_rcv_status, a.hs_code, a.item_prod_id, a.item_description, a.fabric_composition, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_type, a.count_name, a.uom, a.quantity, a.rate, a.amount, a.net_pi_rate, a.net_pi_amount, c.id as btb_dtls_id, d.id as btb_id, d.lc_value, d.lc_date, d.lc_number, d.issuing_bank_id, d.payterm_id, d.tenor, d.currency_id as btb_currency_id, d.last_shipment_date as btb_shipment_date, d.lc_expiry_date, d.lc_type_id, d.etd_date, d.lc_category, e.id as inv_dtls_id, e.current_acceptance_value as inv_value, f.id as invoice_id, f.invoice_no, f.invoice_date, f.bill_no, f.bill_date, f.doc_to_cnf, f.original_doc_receive_date, f.feeder_vessel, f.mother_vessel, f.eta_date, f.port_of_loading, f.port_of_discharge, f.copy_doc_receive_date, f.bill_of_entry_no, f.bill_of_entry_date, f.pkg_quantity, f.container_no, f.maturity_date, f.inco_term, f.inco_term_place, f.company_acc_date, f.bank_acc_date, f.document_value, f.etd_actual, f.eta_advice, f.eta_actual, f.container_status, f.container_size, f.release_date, f.maturity_date 
							from com_pi_item_details a, com_pi_master_details b
							left join com_btb_lc_pi c on b.id=c.pi_id and c.status_active=1 and c.is_deleted=0  
							left join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1 and d.is_deleted=0 $ref_close_btb 
							left join com_import_invoice_dtls e on d.id=e.btb_lc_id and e.status_active=1 and e.is_deleted=0
							left join com_import_invoice_mst f on e.import_invoice_id=f.id and f.status_active=1 and f.is_deleted=0 
							where a.pi_id=b.id and a.item_category_id=$item_cate and b.importer_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond $ref_close_pi $pi_sql_cond";
						}
					}
					//echo $pi_sql_cond."maakak"; die;
					
					// echo $sql;die;
					
					$sql_result=sql_select($sql);
					$pi_wise_data_arr=array();
					foreach($sql_result as $row)
					{
						if($pi_id_check[$row[csf("pi_id")]]=="")
						{
							$pi_id_check[$row[csf("pi_id")]]=$row[csf("pi_id")];
							$description="";
						}
						if($row[csf("item_category_id")]==1)
						{
							if($desc_check[$row[csf("pi_id")]][$composition[$row[csf("yarn_composition_item1")]]."*".$row[csf("yarn_composition_percentage1")]."*".$row[csf("yarn_type")]."*".$row[csf("count_name")]]=="")
							{
								$desc_check[$row[csf("pi_id")]][$composition[$row[csf("yarn_composition_item1")]]."*".$row[csf("yarn_composition_percentage1")]."*".$row[csf("yarn_type")]."*".$row[csf("count_name")]]=$row[csf("count_name")];
								$description.=$composition[$composition[$row[csf("yarn_composition_item1")]]]." ".$row[csf("yarn_composition_percentage1")]." ".$yarn_type[$row[csf("yarn_type")]]." ".$count_arr[$row[csf("count_name")]].",";
							}
							
						}
						else if($row[csf("item_category_id")]==2 || $row[csf("item_category_id")]==13)
						{
							if($desc_check[$row[csf("pi_id")]][$row[csf("fabric_composition")]]=="")
							{
								$desc_check[$row[csf("pi_id")]][$row[csf("fabric_composition")]]=$row[csf("fabric_composition")];
								$description.=$row[csf("fabric_composition")].",";
							}
						}
						else
						{
							if($desc_check[$row[csf("pi_id")]][$row[csf("item_description")]]=="")
							{
								$desc_check[$row[csf("pi_id")]][$row[csf("item_description")]]=$row[csf("item_description")];
								$description.=$row[csf("item_description")].",";
							}
						}
						
						
						$pi_wise_data_arr[$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["pi_number"]=$row[csf("pi_number")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["pi_date"]=$row[csf("pi_date")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["supplier_id"]=$row[csf("supplier_id")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["item_category_id"]=$row[csf("item_category_id")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["item_group"]=$row[csf("item_group")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["description"]=$description;
						$pi_wise_data_arr[$row[csf("pi_id")]]["uom"]=$row[csf("uom")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["currency_id"]=$row[csf("currency_id")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["hs_code"].=$row[csf("hs_code")].",";
						$pi_wise_data_arr[$row[csf("pi_id")]]["intendor_name"]=$row[csf("intendor_name")];
						if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
						{
							$pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
							$pi_wise_data_arr[$row[csf("pi_id")]]["quantity"]+=$row[csf("quantity")];
							$pi_wise_data_arr[$row[csf("pi_id")]]["amount"]+=$row[csf("amount")];
							$pi_wise_data_arr[$row[csf("pi_id")]]["net_pi_amount"]+=$row[csf("net_pi_amount")];
						}
						
						$pi_wise_data_arr[$row[csf("pi_id")]]["btb_id"]=$row[csf("btb_id")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["lc_date"]=$row[csf("lc_date")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["lc_number"]=$row[csf("lc_number")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["issuing_bank_id"]=$row[csf("issuing_bank_id")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["payterm_id"]=$row[csf("payterm_id")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["tenor"]=$row[csf("tenor")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["btb_currency_id"]=$row[csf("btb_currency_id")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["btb_shipment_date"]=$row[csf("btb_shipment_date")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["lc_type_id"]=$row[csf("lc_type_id")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["etd_date"]=$row[csf("etd_date")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["lc_category"]=$row[csf("lc_category")];
						
						$pi_wise_data_arr[$row[csf("pi_id")]]["lc_value"]=$row[csf("lc_value")];
						
						/*if($btb_id_check[$row[csf("btb_id")]]=="")
						{
							$btb_id_check[$row[csf("btb_id")]]=$row[csf("btb_id")];
							$pi_data_arr[$row[csf("pi_id")]]["lc_value"]=$row[csf("lc_value")];
						}*/
						
						$pi_wise_data_arr[$row[csf("pi_id")]]["invoice_id"]=$row[csf("invoice_id")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["etd_actual"]=$row[csf("etd_actual")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["eta_advice"]=$row[csf("eta_advice")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["eta_actual"]=$row[csf("eta_actual")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["port_of_loading"]=$row[csf("port_of_loading")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["port_of_discharge"]=$row[csf("port_of_discharge")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["release_date"]=$row[csf("release_date")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["eta_date"]=$row[csf("eta_date")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["invoice_no"]=$row[csf("invoice_no")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["invoice_date"]=$row[csf("invoice_date")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["inco_term"]=$row[csf("inco_term")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["inco_term_place"]=$row[csf("inco_term_place")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["bill_no"]=$row[csf("bill_no")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["bill_date"]=$row[csf("bill_date")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["mother_vessel"]=$row[csf("mother_vessel")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["feeder_vessel"]=$row[csf("feeder_vessel")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["container_no"]=$row[csf("container_no")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["container_status"]=$row[csf("container_status")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["container_size"]=$row[csf("container_size")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["pkg_quantity"]=$row[csf("pkg_quantity")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["doc_to_cnf"]=$row[csf("doc_to_cnf")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["original_doc_receive_date"]=$row[csf("original_doc_receive_date")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["copy_doc_receive_date"]=$row[csf("copy_doc_receive_date")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["bill_of_entry_date"]=$row[csf("bill_of_entry_date")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["release_date"]=$row[csf("release_date")];
						$pi_wise_data_arr[$row[csf("pi_id")]]["maturity_date"]=$row[csf("maturity_date")];

					}
					
					
					$pi_data_arr=array();
					foreach($pi_wise_data_arr as $pi_id=>$row)
					{
						$mrr_qnty=$mrr_value=0;
						if($row[("goods_rcv_status")]==1)
						{
							$receive_date=$AfterRecv_arr[$row[('work_order_id')]]['receive_date'];
							$mrr_qnty=$AfterRecv_arr[$row[('work_order_id')]]['qnty'];
							$mrr_value=$AfterRecv_arr[$row[('work_order_id')]]['amnt'];
						}
						else
						{
							$receive_date=$receive_arr[$row[('pi_id')]]['receive_date'];
							$mrr_qnty=$receive_arr[$row[("pi_id")]]['qnty'];
							$mrr_value=$receive_arr[$row[("pi_id")]]['amnt'];
						}
						
						if($receive_status==1 && ($mrr_qnty=='' || $mrr_qnty==0))
						{
							$pi_data_arr[$row[("pi_id")]]["pi_id"]=$row[("pi_id")];
							$pi_data_arr[$row[("pi_id")]]["pi_number"]=$row[("pi_number")];
							$pi_data_arr[$row[("pi_id")]]["pi_date"]=$row[("pi_date")];
							$pi_data_arr[$row[("pi_id")]]["last_shipment_date"]=$row[("last_shipment_date")];
							$pi_data_arr[$row[("pi_id")]]["supplier_id"]=$row[("supplier_id")];
							$pi_data_arr[$row[("pi_id")]]["item_category_id"]=$row[("item_category_id")];
							$pi_data_arr[$row[("pi_id")]]["item_group"]=$row[("item_group")];
							$pi_data_arr[$row[("pi_id")]]["description"]=$row[("description")];
							$pi_data_arr[$row[("pi_id")]]["uom"]=$row[("uom")];
							$pi_data_arr[$row[("pi_id")]]["currency_id"]=$row[("currency_id")];
							$pi_data_arr[$row[("pi_id")]]["hs_code"].=$row[("hs_code")].",";
							$pi_data_arr[$row[("pi_id")]]["intendor_name"]=$row[("intendor_name")];
							$pi_data_arr[$row[("pi_id")]]["quantity"]+=$row[("quantity")];
							$pi_data_arr[$row[("pi_id")]]["amount"]+=$row[("amount")];
							$pi_data_arr[$row[("pi_id")]]["net_pi_amount"]+=$row[("net_pi_amount")];
							
							$pi_data_arr[$row[("pi_id")]]["btb_id"]=$row[("btb_id")];
							$pi_data_arr[$row[("pi_id")]]["lc_date"]=$row[("lc_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_number"]=$row[("lc_number")];
							$pi_data_arr[$row[("pi_id")]]["issuing_bank_id"]=$row[("issuing_bank_id")];
							$pi_data_arr[$row[("pi_id")]]["payterm_id"]=$row[("payterm_id")];
							$pi_data_arr[$row[("pi_id")]]["tenor"]=$row[("tenor")];
							$pi_data_arr[$row[("pi_id")]]["btb_currency_id"]=$row[("btb_currency_id")];
							$pi_data_arr[$row[("pi_id")]]["btb_shipment_date"]=$row[("btb_shipment_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_expiry_date"]=$row[("lc_expiry_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_type_id"]=$row[("lc_type_id")];
							$pi_data_arr[$row[("pi_id")]]["etd_date"]=$row[("etd_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_category"]=$row[("lc_category")];
							
							$pi_data_arr[$row[("pi_id")]]["lc_value"]=$row[("lc_value")];
							
							$pi_data_arr[$row[("pi_id")]]["invoice_id"]=$row[("invoice_id")];
							$pi_data_arr[$row[("pi_id")]]["etd_actual"]=$row[("etd_actual")];
							$pi_data_arr[$row[("pi_id")]]["eta_advice"]=$row[("eta_advice")];
							$pi_data_arr[$row[("pi_id")]]["eta_actual"]=$row[("eta_actual")];
							$pi_data_arr[$row[("pi_id")]]["port_of_loading"]=$row[("port_of_loading")];
							$pi_data_arr[$row[("pi_id")]]["port_of_discharge"]=$row[("port_of_discharge")];
							$pi_data_arr[$row[("pi_id")]]["release_date"]=$row[("release_date")];
							$pi_data_arr[$row[("pi_id")]]["eta_date"]=$row[("eta_date")];
							$pi_data_arr[$row[("pi_id")]]["invoice_no"]=$row[("invoice_no")];
							$pi_data_arr[$row[("pi_id")]]["invoice_date"]=$row[("invoice_date")];
							$pi_data_arr[$row[("pi_id")]]["inco_term"]=$row[("inco_term")];
							$pi_data_arr[$row[("pi_id")]]["inco_term_place"]=$row[("inco_term_place")];
							$pi_data_arr[$row[("pi_id")]]["bill_no"]=$row[("bill_no")];
							$pi_data_arr[$row[("pi_id")]]["bill_date"]=$row[("bill_date")];
							$pi_data_arr[$row[("pi_id")]]["mother_vessel"]=$row[("mother_vessel")];
							$pi_data_arr[$row[("pi_id")]]["feeder_vessel"]=$row[("feeder_vessel")];
							$pi_data_arr[$row[("pi_id")]]["container_no"]=$row[("container_no")];
							$pi_data_arr[$row[("pi_id")]]["container_status"]=$row[("container_status")];
							$pi_data_arr[$row[("pi_id")]]["container_size"]=$row[("container_size")];
							$pi_data_arr[$row[("pi_id")]]["pkg_quantity"]=$row[("pkg_quantity")];
							$pi_data_arr[$row[("pi_id")]]["doc_to_cnf"]=$row[("doc_to_cnf")];
							$pi_data_arr[$row[("pi_id")]]["original_doc_receive_date"]=$row[("original_doc_receive_date")];
							$pi_data_arr[$row[("pi_id")]]["copy_doc_receive_date"]=$row[("copy_doc_receive_date")];
							$pi_data_arr[$row[("pi_id")]]["bill_of_entry_no"]=$row[("bill_of_entry_no")];
							$pi_data_arr[$row[("pi_id")]]["bill_of_entry_date"]=$row[("bill_of_entry_date")];
							$pi_data_arr[$row[("pi_id")]]["release_date"]=$row[("release_date")];
							$pi_data_arr[$row[("pi_id")]]["maturity_date"]=$row[("maturity_date")];

							$pi_data_arr[$row[("pi_id")]]["receive_date"]=$receive_date;
							$pi_data_arr[$row[("pi_id")]]["mrr_qnty"]=$mrr_qnty;
							$pi_data_arr[$row[("pi_id")]]["mrr_value"]=$mrr_value;
							
						}
						else if($receive_status==2 && (number_format($row[("quantity")],2,'.','') > number_format($mrr_qnty,2,'.','')) && $mrr_qnty>0)
						{
							$pi_data_arr[$row[("pi_id")]]["pi_id"]=$row[("pi_id")];
							$pi_data_arr[$row[("pi_id")]]["pi_number"]=$row[("pi_number")];
							$pi_data_arr[$row[("pi_id")]]["pi_date"]=$row[("pi_date")];
							$pi_data_arr[$row[("pi_id")]]["last_shipment_date"]=$row[("last_shipment_date")];
							$pi_data_arr[$row[("pi_id")]]["supplier_id"]=$row[("supplier_id")];
							$pi_data_arr[$row[("pi_id")]]["item_category_id"]=$row[("item_category_id")];
							$pi_data_arr[$row[("pi_id")]]["item_group"]=$row[("item_group")];
							$pi_data_arr[$row[("pi_id")]]["description"]=$row[("description")];
							$pi_data_arr[$row[("pi_id")]]["uom"]=$row[("uom")];
							$pi_data_arr[$row[("pi_id")]]["currency_id"]=$row[("currency_id")];
							$pi_data_arr[$row[("pi_id")]]["hs_code"].=$row[("hs_code")].",";
							$pi_data_arr[$row[("pi_id")]]["intendor_name"]=$row[("intendor_name")];
							$pi_data_arr[$row[("pi_id")]]["quantity"]+=$row[("quantity")];
							$pi_data_arr[$row[("pi_id")]]["amount"]+=$row[("amount")];
							$pi_data_arr[$row[("pi_id")]]["net_pi_amount"]+=$row[("net_pi_amount")];
							
							$pi_data_arr[$row[("pi_id")]]["btb_id"]=$row[("btb_id")];
							$pi_data_arr[$row[("pi_id")]]["lc_date"]=$row[("lc_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_number"]=$row[("lc_number")];
							$pi_data_arr[$row[("pi_id")]]["issuing_bank_id"]=$row[("issuing_bank_id")];
							$pi_data_arr[$row[("pi_id")]]["payterm_id"]=$row[("payterm_id")];
							$pi_data_arr[$row[("pi_id")]]["tenor"]=$row[("tenor")];
							$pi_data_arr[$row[("pi_id")]]["btb_currency_id"]=$row[("btb_currency_id")];
							$pi_data_arr[$row[("pi_id")]]["btb_shipment_date"]=$row[("btb_shipment_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_expiry_date"]=$row[("lc_expiry_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_type_id"]=$row[("lc_type_id")];
							$pi_data_arr[$row[("pi_id")]]["etd_date"]=$row[("etd_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_category"]=$row[("lc_category")];
							
							$pi_data_arr[$row[("pi_id")]]["lc_value"]=$row[("lc_value")];
							
							$pi_data_arr[$row[("pi_id")]]["invoice_id"]=$row[("invoice_id")];
							$pi_data_arr[$row[("pi_id")]]["etd_actual"]=$row[("etd_actual")];
							$pi_data_arr[$row[("pi_id")]]["eta_advice"]=$row[("eta_advice")];
							$pi_data_arr[$row[("pi_id")]]["eta_actual"]=$row[("eta_actual")];
							$pi_data_arr[$row[("pi_id")]]["port_of_loading"]=$row[("port_of_loading")];
							$pi_data_arr[$row[("pi_id")]]["port_of_discharge"]=$row[("port_of_discharge")];
							$pi_data_arr[$row[("pi_id")]]["release_date"]=$row[("release_date")];
							$pi_data_arr[$row[("pi_id")]]["eta_date"]=$row[("eta_date")];
							$pi_data_arr[$row[("pi_id")]]["invoice_no"]=$row[("invoice_no")];
							$pi_data_arr[$row[("pi_id")]]["invoice_date"]=$row[("invoice_date")];
							$pi_data_arr[$row[("pi_id")]]["inco_term"]=$row[("inco_term")];
							$pi_data_arr[$row[("pi_id")]]["inco_term_place"]=$row[("inco_term_place")];
							$pi_data_arr[$row[("pi_id")]]["bill_no"]=$row[("bill_no")];
							$pi_data_arr[$row[("pi_id")]]["bill_date"]=$row[("bill_date")];
							$pi_data_arr[$row[("pi_id")]]["mother_vessel"]=$row[("mother_vessel")];
							$pi_data_arr[$row[("pi_id")]]["feeder_vessel"]=$row[("feeder_vessel")];
							$pi_data_arr[$row[("pi_id")]]["container_no"]=$row[("container_no")];
							$pi_data_arr[$row[("pi_id")]]["container_status"]=$row[("container_status")];
							$pi_data_arr[$row[("pi_id")]]["container_size"]=$row[("container_size")];
							$pi_data_arr[$row[("pi_id")]]["pkg_quantity"]=$row[("pkg_quantity")];
							$pi_data_arr[$row[("pi_id")]]["doc_to_cnf"]=$row[("doc_to_cnf")];
							$pi_data_arr[$row[("pi_id")]]["original_doc_receive_date"]=$row[("original_doc_receive_date")];
							$pi_data_arr[$row[("pi_id")]]["copy_doc_receive_date"]=$row[("copy_doc_receive_date")];
							$pi_data_arr[$row[("pi_id")]]["bill_of_entry_no"]=$row[("bill_of_entry_no")];
							$pi_data_arr[$row[("pi_id")]]["bill_of_entry_date"]=$row[("bill_of_entry_date")];
							$pi_data_arr[$row[("pi_id")]]["release_date"]=$row[("release_date")];
							$pi_data_arr[$row[("pi_id")]]["maturity_date"]=$row[("maturity_date")];

							$pi_data_arr[$row[("pi_id")]]["receive_date"]=$receive_date;
							$pi_data_arr[$row[("pi_id")]]["mrr_qnty"]=$mrr_qnty;
							$pi_data_arr[$row[("pi_id")]]["mrr_value"]=$mrr_value;
						}
						else if($receive_status==3 && (number_format($row[("quantity")],2,'.','') <= number_format($mrr_qnty,2,'.','')))
						{
							$pi_data_arr[$row[("pi_id")]]["pi_id"]=$row[("pi_id")];
							$pi_data_arr[$row[("pi_id")]]["pi_number"]=$row[("pi_number")];
							$pi_data_arr[$row[("pi_id")]]["pi_date"]=$row[("pi_date")];
							$pi_data_arr[$row[("pi_id")]]["last_shipment_date"]=$row[("last_shipment_date")];
							$pi_data_arr[$row[("pi_id")]]["supplier_id"]=$row[("supplier_id")];
							$pi_data_arr[$row[("pi_id")]]["item_category_id"]=$row[("item_category_id")];
							$pi_data_arr[$row[("pi_id")]]["item_group"]=$row[("item_group")];
							$pi_data_arr[$row[("pi_id")]]["description"]=$row[("description")];
							$pi_data_arr[$row[("pi_id")]]["uom"]=$row[("uom")];
							$pi_data_arr[$row[("pi_id")]]["currency_id"]=$row[("currency_id")];
							$pi_data_arr[$row[("pi_id")]]["hs_code"].=$row[("hs_code")].",";
							$pi_data_arr[$row[("pi_id")]]["intendor_name"]=$row[("intendor_name")];
							$pi_data_arr[$row[("pi_id")]]["quantity"]+=$row[("quantity")];
							$pi_data_arr[$row[("pi_id")]]["amount"]+=$row[("amount")];
							$pi_data_arr[$row[("pi_id")]]["net_pi_amount"]+=$row[("net_pi_amount")];
							
							$pi_data_arr[$row[("pi_id")]]["btb_id"]=$row[("btb_id")];
							$pi_data_arr[$row[("pi_id")]]["lc_date"]=$row[("lc_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_number"]=$row[("lc_number")];
							$pi_data_arr[$row[("pi_id")]]["issuing_bank_id"]=$row[("issuing_bank_id")];
							$pi_data_arr[$row[("pi_id")]]["payterm_id"]=$row[("payterm_id")];
							$pi_data_arr[$row[("pi_id")]]["tenor"]=$row[("tenor")];
							$pi_data_arr[$row[("pi_id")]]["btb_currency_id"]=$row[("btb_currency_id")];
							$pi_data_arr[$row[("pi_id")]]["btb_shipment_date"]=$row[("btb_shipment_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_expiry_date"]=$row[("lc_expiry_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_type_id"]=$row[("lc_type_id")];
							$pi_data_arr[$row[("pi_id")]]["etd_date"]=$row[("etd_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_category"]=$row[("lc_category")];
							
							$pi_data_arr[$row[("pi_id")]]["lc_value"]=$row[("lc_value")];
							
							$pi_data_arr[$row[("pi_id")]]["invoice_id"]=$row[("invoice_id")];
							$pi_data_arr[$row[("pi_id")]]["etd_actual"]=$row[("etd_actual")];
							$pi_data_arr[$row[("pi_id")]]["eta_advice"]=$row[("eta_advice")];
							$pi_data_arr[$row[("pi_id")]]["eta_actual"]=$row[("eta_actual")];
							$pi_data_arr[$row[("pi_id")]]["port_of_loading"]=$row[("port_of_loading")];
							$pi_data_arr[$row[("pi_id")]]["port_of_discharge"]=$row[("port_of_discharge")];
							$pi_data_arr[$row[("pi_id")]]["release_date"]=$row[("release_date")];
							$pi_data_arr[$row[("pi_id")]]["eta_date"]=$row[("eta_date")];
							$pi_data_arr[$row[("pi_id")]]["invoice_no"]=$row[("invoice_no")];
							$pi_data_arr[$row[("pi_id")]]["invoice_date"]=$row[("invoice_date")];
							$pi_data_arr[$row[("pi_id")]]["inco_term"]=$row[("inco_term")];
							$pi_data_arr[$row[("pi_id")]]["inco_term_place"]=$row[("inco_term_place")];
							$pi_data_arr[$row[("pi_id")]]["bill_no"]=$row[("bill_no")];
							$pi_data_arr[$row[("pi_id")]]["bill_date"]=$row[("bill_date")];
							$pi_data_arr[$row[("pi_id")]]["mother_vessel"]=$row[("mother_vessel")];
							$pi_data_arr[$row[("pi_id")]]["feeder_vessel"]=$row[("feeder_vessel")];
							$pi_data_arr[$row[("pi_id")]]["container_no"]=$row[("container_no")];
							$pi_data_arr[$row[("pi_id")]]["container_status"]=$row[("container_status")];
							$pi_data_arr[$row[("pi_id")]]["container_size"]=$row[("container_size")];
							$pi_data_arr[$row[("pi_id")]]["pkg_quantity"]=$row[("pkg_quantity")];
							$pi_data_arr[$row[("pi_id")]]["doc_to_cnf"]=$row[("doc_to_cnf")];
							$pi_data_arr[$row[("pi_id")]]["original_doc_receive_date"]=$row[("original_doc_receive_date")];
							$pi_data_arr[$row[("pi_id")]]["copy_doc_receive_date"]=$row[("copy_doc_receive_date")];
							$pi_data_arr[$row[("pi_id")]]["bill_of_entry_no"]=$row[("bill_of_entry_no")];
							$pi_data_arr[$row[("pi_id")]]["bill_of_entry_date"]=$row[("bill_of_entry_date")];
							$pi_data_arr[$row[("pi_id")]]["release_date"]=$row[("release_date")];
							$pi_data_arr[$row[("pi_id")]]["maturity_date"]=$row[("maturity_date")];

							$pi_data_arr[$row[("pi_id")]]["receive_date"]=$receive_date;
							$pi_data_arr[$row[("pi_id")]]["mrr_qnty"]=$mrr_qnty;
							$pi_data_arr[$row[("pi_id")]]["mrr_value"]=$mrr_value;
						}
						else if($receive_status==4 && (number_format($row[("quantity")],2,'.','') > number_format($mrr_qnty,2,'.','')))
						{
							$pi_data_arr[$row[("pi_id")]]["pi_id"]=$row[("pi_id")];
							$pi_data_arr[$row[("pi_id")]]["pi_number"]=$row[("pi_number")];
							$pi_data_arr[$row[("pi_id")]]["pi_date"]=$row[("pi_date")];
							$pi_data_arr[$row[("pi_id")]]["last_shipment_date"]=$row[("last_shipment_date")];
							$pi_data_arr[$row[("pi_id")]]["supplier_id"]=$row[("supplier_id")];
							$pi_data_arr[$row[("pi_id")]]["item_category_id"]=$row[("item_category_id")];
							$pi_data_arr[$row[("pi_id")]]["item_group"]=$row[("item_group")];
							$pi_data_arr[$row[("pi_id")]]["description"]=$row[("description")];
							$pi_data_arr[$row[("pi_id")]]["uom"]=$row[("uom")];
							$pi_data_arr[$row[("pi_id")]]["currency_id"]=$row[("currency_id")];
							$pi_data_arr[$row[("pi_id")]]["hs_code"].=$row[("hs_code")].",";
							$pi_data_arr[$row[("pi_id")]]["intendor_name"]=$row[("intendor_name")];
							$pi_data_arr[$row[("pi_id")]]["quantity"]+=$row[("quantity")];
							$pi_data_arr[$row[("pi_id")]]["amount"]+=$row[("amount")];
							$pi_data_arr[$row[("pi_id")]]["net_pi_amount"]+=$row[("net_pi_amount")];
							
							$pi_data_arr[$row[("pi_id")]]["btb_id"]=$row[("btb_id")];
							$pi_data_arr[$row[("pi_id")]]["lc_date"]=$row[("lc_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_number"]=$row[("lc_number")];
							$pi_data_arr[$row[("pi_id")]]["issuing_bank_id"]=$row[("issuing_bank_id")];
							$pi_data_arr[$row[("pi_id")]]["payterm_id"]=$row[("payterm_id")];
							$pi_data_arr[$row[("pi_id")]]["tenor"]=$row[("tenor")];
							$pi_data_arr[$row[("pi_id")]]["btb_currency_id"]=$row[("btb_currency_id")];
							$pi_data_arr[$row[("pi_id")]]["btb_shipment_date"]=$row[("btb_shipment_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_expiry_date"]=$row[("lc_expiry_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_type_id"]=$row[("lc_type_id")];
							$pi_data_arr[$row[("pi_id")]]["etd_date"]=$row[("etd_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_category"]=$row[("lc_category")];
							
							$pi_data_arr[$row[("pi_id")]]["lc_value"]=$row[("lc_value")];
							
							$pi_data_arr[$row[("pi_id")]]["invoice_id"]=$row[("invoice_id")];
							$pi_data_arr[$row[("pi_id")]]["etd_actual"]=$row[("etd_actual")];
							$pi_data_arr[$row[("pi_id")]]["eta_advice"]=$row[("eta_advice")];
							$pi_data_arr[$row[("pi_id")]]["eta_actual"]=$row[("eta_actual")];
							$pi_data_arr[$row[("pi_id")]]["port_of_loading"]=$row[("port_of_loading")];
							$pi_data_arr[$row[("pi_id")]]["port_of_discharge"]=$row[("port_of_discharge")];
							$pi_data_arr[$row[("pi_id")]]["release_date"]=$row[("release_date")];
							$pi_data_arr[$row[("pi_id")]]["eta_date"]=$row[("eta_date")];
							$pi_data_arr[$row[("pi_id")]]["invoice_no"]=$row[("invoice_no")];
							$pi_data_arr[$row[("pi_id")]]["invoice_date"]=$row[("invoice_date")];
							$pi_data_arr[$row[("pi_id")]]["inco_term"]=$row[("inco_term")];
							$pi_data_arr[$row[("pi_id")]]["inco_term_place"]=$row[("inco_term_place")];
							$pi_data_arr[$row[("pi_id")]]["bill_no"]=$row[("bill_no")];
							$pi_data_arr[$row[("pi_id")]]["bill_date"]=$row[("bill_date")];
							$pi_data_arr[$row[("pi_id")]]["mother_vessel"]=$row[("mother_vessel")];
							$pi_data_arr[$row[("pi_id")]]["feeder_vessel"]=$row[("feeder_vessel")];
							$pi_data_arr[$row[("pi_id")]]["container_no"]=$row[("container_no")];
							$pi_data_arr[$row[("pi_id")]]["container_status"]=$row[("container_status")];
							$pi_data_arr[$row[("pi_id")]]["container_size"]=$row[("container_size")];
							$pi_data_arr[$row[("pi_id")]]["pkg_quantity"]=$row[("pkg_quantity")];
							$pi_data_arr[$row[("pi_id")]]["doc_to_cnf"]=$row[("doc_to_cnf")];
							$pi_data_arr[$row[("pi_id")]]["original_doc_receive_date"]=$row[("original_doc_receive_date")];
							$pi_data_arr[$row[("pi_id")]]["copy_doc_receive_date"]=$row[("copy_doc_receive_date")];
							$pi_data_arr[$row[("pi_id")]]["bill_of_entry_no"]=$row[("bill_of_entry_no")];
							$pi_data_arr[$row[("pi_id")]]["bill_of_entry_date"]=$row[("bill_of_entry_date")];
							$pi_data_arr[$row[("pi_id")]]["release_date"]=$row[("release_date")];
							$pi_data_arr[$row[("pi_id")]]["maturity_date"]=$row[("maturity_date")];

							$pi_data_arr[$row[("pi_id")]]["receive_date"]=$receive_date;
							$pi_data_arr[$row[("pi_id")]]["mrr_qnty"]=$mrr_qnty;
							$pi_data_arr[$row[("pi_id")]]["mrr_value"]=$mrr_value;
						}
						else if($receive_status==5)
						{
							$pi_data_arr[$row[("pi_id")]]["pi_id"]=$row[("pi_id")];
							$pi_data_arr[$row[("pi_id")]]["pi_number"]=$row[("pi_number")];
							$pi_data_arr[$row[("pi_id")]]["pi_date"]=$row[("pi_date")];
							$pi_data_arr[$row[("pi_id")]]["last_shipment_date"]=$row[("last_shipment_date")];
							$pi_data_arr[$row[("pi_id")]]["supplier_id"]=$row[("supplier_id")];
							$pi_data_arr[$row[("pi_id")]]["item_category_id"]=$row[("item_category_id")];
							$pi_data_arr[$row[("pi_id")]]["item_group"]=$row[("item_group")];
							$pi_data_arr[$row[("pi_id")]]["description"]=$row[("description")];
							$pi_data_arr[$row[("pi_id")]]["uom"]=$row[("uom")];
							$pi_data_arr[$row[("pi_id")]]["currency_id"]=$row[("currency_id")];
							$pi_data_arr[$row[("pi_id")]]["hs_code"].=$row[("hs_code")].",";
							$pi_data_arr[$row[("pi_id")]]["intendor_name"]=$row[("intendor_name")];
							$pi_data_arr[$row[("pi_id")]]["quantity"]+=$row[("quantity")];
							$pi_data_arr[$row[("pi_id")]]["amount"]+=$row[("amount")];
							$pi_data_arr[$row[("pi_id")]]["net_pi_amount"]+=$row[("net_pi_amount")];
							
							$pi_data_arr[$row[("pi_id")]]["btb_id"]=$row[("btb_id")];
							$pi_data_arr[$row[("pi_id")]]["lc_date"]=$row[("lc_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_number"]=$row[("lc_number")];
							$pi_data_arr[$row[("pi_id")]]["issuing_bank_id"]=$row[("issuing_bank_id")];
							$pi_data_arr[$row[("pi_id")]]["payterm_id"]=$row[("payterm_id")];
							$pi_data_arr[$row[("pi_id")]]["tenor"]=$row[("tenor")];
							$pi_data_arr[$row[("pi_id")]]["btb_currency_id"]=$row[("btb_currency_id")];
							$pi_data_arr[$row[("pi_id")]]["btb_shipment_date"]=$row[("btb_shipment_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_expiry_date"]=$row[("lc_expiry_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_type_id"]=$row[("lc_type_id")];
							$pi_data_arr[$row[("pi_id")]]["etd_date"]=$row[("etd_date")];
							$pi_data_arr[$row[("pi_id")]]["lc_category"]=$row[("lc_category")];
							
							$pi_data_arr[$row[("pi_id")]]["lc_value"]=$row[("lc_value")];
							
							$pi_data_arr[$row[("pi_id")]]["invoice_id"]=$row[("invoice_id")];
							$pi_data_arr[$row[("pi_id")]]["etd_actual"]=$row[("etd_actual")];
							$pi_data_arr[$row[("pi_id")]]["eta_advice"]=$row[("eta_advice")];
							$pi_data_arr[$row[("pi_id")]]["eta_actual"]=$row[("eta_actual")];
							$pi_data_arr[$row[("pi_id")]]["port_of_loading"]=$row[("port_of_loading")];
							$pi_data_arr[$row[("pi_id")]]["port_of_discharge"]=$row[("port_of_discharge")];
							$pi_data_arr[$row[("pi_id")]]["release_date"]=$row[("release_date")];
							$pi_data_arr[$row[("pi_id")]]["eta_date"]=$row[("eta_date")];
							$pi_data_arr[$row[("pi_id")]]["invoice_no"]=$row[("invoice_no")];
							$pi_data_arr[$row[("pi_id")]]["invoice_date"]=$row[("invoice_date")];
							$pi_data_arr[$row[("pi_id")]]["inco_term"]=$row[("inco_term")];
							$pi_data_arr[$row[("pi_id")]]["inco_term_place"]=$row[("inco_term_place")];
							$pi_data_arr[$row[("pi_id")]]["bill_no"]=$row[("bill_no")];
							$pi_data_arr[$row[("pi_id")]]["bill_date"]=$row[("bill_date")];
							$pi_data_arr[$row[("pi_id")]]["mother_vessel"]=$row[("mother_vessel")];
							$pi_data_arr[$row[("pi_id")]]["feeder_vessel"]=$row[("feeder_vessel")];
							$pi_data_arr[$row[("pi_id")]]["container_no"]=$row[("container_no")];
							$pi_data_arr[$row[("pi_id")]]["container_status"]=$row[("container_status")];
							$pi_data_arr[$row[("pi_id")]]["container_size"]=$row[("container_size")];
							$pi_data_arr[$row[("pi_id")]]["pkg_quantity"]=$row[("pkg_quantity")];
							$pi_data_arr[$row[("pi_id")]]["doc_to_cnf"]=$row[("doc_to_cnf")];
							$pi_data_arr[$row[("pi_id")]]["original_doc_receive_date"]=$row[("original_doc_receive_date")];
							$pi_data_arr[$row[("pi_id")]]["copy_doc_receive_date"]=$row[("copy_doc_receive_date")];
							$pi_data_arr[$row[("pi_id")]]["bill_of_entry_no"]=$row[("bill_of_entry_no")];
							$pi_data_arr[$row[("pi_id")]]["bill_of_entry_date"]=$row[("bill_of_entry_date")];
							$pi_data_arr[$row[("pi_id")]]["release_date"]=$row[("release_date")];
							$pi_data_arr[$row[("pi_id")]]["maturity_date"]=$row[("maturity_date")];

							$pi_data_arr[$row[("pi_id")]]["receive_date"]=$receive_date;
							$pi_data_arr[$row[("pi_id")]]["mrr_qnty"]=$mrr_qnty;
							$pi_data_arr[$row[("pi_id")]]["mrr_value"]=$mrr_value;
						}
					}
					unset($sql_result);
					
					$sql_payment="select a.id as pay_id, a.invoice_id, a.payment_date, a.accepted_ammount from com_import_payment a, com_import_invoice_dtls b, com_pi_master_details c where a.invoice_id=b.import_invoice_id and b.pi_id=c.id and c.item_category_id=$item_cate and c.importer_id=$cbo_company_name order by a.id";
					$payment_result=sql_select($sql_payment);
					$payment_data=array();
					foreach($payment_result as $row)
					{
						if($pay_check[$row[csf("pay_id")]]=="")
						{
							$pay_check[$row[csf("pay_id")]]=$row[csf("pay_id")];
							$payment_data[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
						}
						$payment_data[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
					}
					unset($payment_result);

					$i=1; $total_pi_qty=0;$total_pi_val=0; $total_short_val=0;$total_mrr_qnty=0;
					foreach ($pi_data_arr as $pi_id => $value)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($z==1)
						{
							$display_font_color="";
							$font_end="";
						}
						else
						{
							$display_font_color="<font style='display:none' color='$bgcolor'>_";
							$font_end="</font>";
						}
						//echo $data[('amount')]."==".$mrr_value_usd."==".$exchange_rate."++";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        
                        	<td width="40" align="center"><? echo $i; ?></td>
	                        <td width="120"><p><? echo $value[('pi_number')]; ?>&nbsp;</p></td>
	                        <td width="80" align="center"><p><? echo change_date_format($value[('pi_date')]); ?>&nbsp;</p></td>
	                        <td width="80" align="center"><p><? if($value[('last_shipment_date')]!="" && $value[('last_shipment_date')]!="0000-00-00") echo change_date_format($value[('last_shipment_date')]); ?>&nbsp;</p></td>
	                        <td width="140"><p><? echo $supplier_arr[$value[('supplier_id')]]; ?>&nbsp;</p></td>
	                        <td width="100"><p><? echo $item_category[$value[('item_category_id')]]; ?>&nbsp;</p></td>
	                        <? if($item_cate==5 || $item_cate==6 || $item_cate==7 || $item_cate==22)
	                        {
	                        	?> <td width="100"><p><? echo $group_arr[$value[('item_group')]]; ?>&nbsp;</p></td><?
	                        } 
	                        ?>
	                        <td width="100" style="word-wrap:break-word;word-break: break-all;"><? echo chop($value[('hs_code')],","); ?>&nbsp;</td>
	                        <td width="320"><p><? echo chop($value[('description')],","); ?>&nbsp;</p></td>
	                        <td width="60" align="center"><p><? echo $unit_of_measurement[$value[('uom')]]; ?>&nbsp;</p></td>
	                        <td width="80" align="right"><? echo number_format($value[('quantity')],2); ?></td>
	                        <td width="80" align="right"><? $unit_price=$value[('net_pi_amount')]/$value[('quantity')]; if($value[('net_pi_amount')]>0 && $value[('quantity')]>0) echo number_format($unit_price,2);?></td>
	                        <td width="100" align="right"><? echo number_format($value[('net_pi_amount')],2); ?></td>
	                        <td width="80" align="center"><p><? echo $currency[$value[('currency_id')]]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $currency[$value[('intendor_name')]]; ?>&nbsp;</p></td>
                            
                            
	                        <td width="80" align="center"><p><? if($value[('lc_date')]!="" && $value[('lc_date')]!="0000-00-00") echo change_date_format($value[('lc_date')]); ?>&nbsp;</p></td>
	                        <td width="120"><p><? echo $value[('lc_number')]; ?>&nbsp;</p></td>
	                        <td width="170" title="<? echo $value[('lc_category')]."=".$value[('lc_category')]*1; ?>"><p><? echo $supply_source[$value[('lc_category')]*1]; ?>&nbsp;</p></td>
	                        <td width="120"><p><? echo $bank_arr[$value[('issuing_bank_id')]]; ?>&nbsp;</p></td>
	                        <td width="80"><p><? echo $pay_term[$value[('payterm_id')]]; ?>&nbsp;</p></td>
	                        <td width="50" align="center"><p><? echo $value[('tenor')]; ?>&nbsp;</p></td>
	                        <td width="80" align="center"><p><? echo $currency[$value[('btb_currency_id')]]; ?>&nbsp;</p></td>
	                        <td width="100" align="right">
							<?
							$lc_value=0; 
							if($value[('btb_id')])
							{
								$lc_value=$value[('lc_value')];
							}
							echo number_format($lc_value,2);
							?></td>
	                        <td width="80" align="center"><p><? if($value[('btb_shipment_date')]!="" && $value[('btb_shipment_date')]!="0000-00-00") echo change_date_format($value[('btb_shipment_date')]); ?>&nbsp;</p></td>
	                        <td width="80" align="center"><p><? if($value[('lc_expiry_date')]!="" && $value[('lc_expiry_date')]!="0000-00-00") echo change_date_format($value[('lc_expiry_date')]); ?>&nbsp;</p></td>
	                        <td width="120"><p><? echo $lc_sc_data[$value[('btb_id')]]['lc_sc_no']; ?>&nbsp;</p></td>
	                        <td width="80" align="center"><p><? if($lc_sc_data[$value[('btb_id')]]['lc_sc_date']!="" && $lc_sc_data[$value[('btb_id')]]['lc_sc_date']!="0000-00-00") echo change_date_format($lc_sc_data[$value[('btb_id')]]['lc_sc_date']); ?>&nbsp;</p></td>
                            <td width="80" align="center"><p><? if($value[('etd_date')]!="" && $value[('etd_date')]!="0000-00-00") echo change_date_format($value[('etd_date')]); ?>&nbsp;</p></td>
                            
	                        <td width="80" align="center"><p><? if($value[('etd_actual')]!="" && $value[('etd_actual')]!="0000-00-00") echo change_date_format($value[('etd_actual')]); ?>&nbsp;</p></td>
	                        <td width="80" align="center"><p><? if($value[('eta_advice')]!="" && $value[('eta_advice')]!="0000-00-00") echo change_date_format($value[('eta_advice')]); ?>&nbsp;</p></td>
	                        <td width="80" align="center"><p><? if($value[('eta_actual')]!="" && $value[('eta_actual')]!="0000-00-00") echo change_date_format($value[('eta_actual')]); ?>&nbsp;</p></td>
	                        <td width="120"><p><? echo $value[('port_of_loading')]; ?>&nbsp;</p></td>
	                        <td width="120"><p><? echo $value[('port_of_discharge')]; ?>&nbsp;</p></td>
	                        <td width="80" align="center"><p><? if($value[('eta_date')]!="" && $value[('eta_date')]!="0000-00-00") echo change_date_format($value[('eta_date')]); ?>&nbsp;</p></td>
	                        <td width="120"><p><? echo $value[('invoice_no')]; ?>&nbsp;</p></td>
	                        <td width="80" align="center"><p><? if($value[('invoice_date')]!="" && $value[('invoice_date')]!="0000-00-00") echo change_date_format($value[('invoice_date')]); ?>&nbsp;</p></td>
	                        <td width="80"><p><? echo $incoterm[$value[('inco_term')]]; ?>&nbsp;</p></td>
	                        <td width="100"><p><? echo $value[('inco_term_place')]; ?>&nbsp;</p></td>
	                        <td width="100"><p><? echo $value[('bill_no')]; ?>&nbsp;</p></td>
	                        <td width="80" align="center"><p><? if($value[('bill_date')]!="" && $value[('bill_date')]!="0000-00-00") echo change_date_format($value[('bill_date')]); ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $value[('mother_vessel')]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $value[('feeder_vessel')]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $value[('container_no')]; ?>&nbsp;</p></td>
                            <td width="80"><p><? echo $container_status[$value[('container_status')]]; ?>&nbsp;</p></td>
	                        <td width="80"><p><? echo $container_size[$value[('container_size')]]; ?>&nbsp;</p></td>
	                        <td width="80" align="right"><p><? echo $value[('pkg_quantity')]; ?>&nbsp;</p></td>
	                        <td width="80" align="center"><p><? if($value[('copy_doc_receive_date')]!="" && $value[('copy_doc_receive_date')]!="0000-00-00") echo change_date_format($value[('copy_doc_receive_date')]); ?>&nbsp;</p></td>
	                        <td width="100" align="center"><p><? if($value[('original_doc_receive_date')]!="" && $value[('original_doc_receive_date')]!="0000-00-00") echo change_date_format($value[('original_doc_receive_date')]); ?>&nbsp;</p></td>
	                        <td width="80" align="center"><p><? if($value[('doc_to_cnf')]!="" && $value[('doc_to_cnf')]!="0000-00-00") echo change_date_format($value[('doc_to_cnf')]); ?>&nbsp;</p></td>
	                        
                            <td width="100"><p><? echo $value[('bill_of_entry_no')]; ?>&nbsp;</p></td>
                            <td width="80" align="center"><p><? if($value[('bill_of_entry_date')]!="" && $value[('bill_of_entry_date')]!="0000-00-00") echo change_date_format($value[('bill_of_entry_date')]); ?>&nbsp;</p></td>
                            <td width="80" align="center"><p><? if($value[('release_date')]!="" && $value[('release_date')]!="0000-00-00") echo change_date_format($value[('release_date')]); ?>&nbsp;</p></td>
                            
	                        <td width="80" align="center"><p><? if($value[('maturity_date')]!="" && $value[('maturity_date')]!="0000-00-00") echo change_date_format($value[('maturity_date')]); ?>&nbsp;</p></td>
	                        <td width="80" align="center"><? if($value[('maturity_date')]!="" && $value[('maturity_date')]!="0000-00-00") echo date("M",strtotime($value[('maturity_date')])); ?></td>
	                        <td width="80" align="center"><p><? if($payment_data[$value[('invoice_id')]]["payment_date"]!="" && $payment_data[$value[('invoice_id')]]["payment_date"]!="0000-00-00") echo change_date_format($payment_data[$value[('invoice_id')]]["payment_date"]); ?>&nbsp;</p></td>
							<td width="100" align="right"><? echo number_format($payment_data[$value[('invoice_id')]]["accepted_ammount"],2); ?></td>
                            
	                        <td width="80" align="center"><p><? if($value[('receive_date')]!="" && $value[('receive_date')]!="0000-00-00") echo change_date_format($value[('receive_date')]); ?>&nbsp;</p></td>
                            <td width="80" align="right" title="<? echo "PI qnty : ".$value[('quantity')]."MRR qnty : ".$value[('mrr_qnty')]; ?>"><? echo number_format($value[('mrr_qnty')],2); ?></td>
	                        <td width="100" align="right"><? echo number_format($value[('mrr_value')],2); ?></td>
	                        <td align="right" title="PI Value - MRR Value"><? $short_value=$value[('net_pi_amount')]-$value[('mrr_value')]; echo number_format($short_value,2);?></td>
						</tr>
						<?
						$tot_pi_qnty+=$value[('quantity')];
						$tot_pi_amt+=$value[('net_pi_amount')];
						//$tot_btb_amt+=$lc_value;
						$tot_paid_amt+=$payment_data[$value[('invoice_id')]]["accepted_ammount"];
						$tot_mrr_qnty+=$value[('mrr_qnty')];
						$tot_mrr_value+=$value[('mrr_value')];
						$tot_short_val+=$short_value;
						$short_value=0;
						$i++;
						
					}
					?>
                </table>
                </div>
                <table width="<? echo $table_width; ?>" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
	                <tfoot>
	                    <th width="40">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="140">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <? if($item_cate==5 || $item_cate==6 || $item_cate==7 || $item_cate==22)
                        {
                        	?><th width="100">&nbsp;</th><?
                        }
                        ?>
                        <th width="100">&nbsp;</th>
                        <th width="320">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="80" align="right" id="value_tot_pi_qnty"><? echo number_format($tot_pi_qnty,2); ?></th>
                        <th width="80">&nbsp;</th>
                        <th width="100" align="right" id="value_tot_pi_amt"><? echo number_format($tot_pi_amt,2); ?></th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="170">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100" align="right" id="value_tot_btb_amt"><?// echo number_format($tot_btb_amt,2); ?></th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100" align="right" id="value_tot_paid_amt"><? echo number_format($tot_paid_amt,2); ?></th>
                        <th width="80">&nbsp;</th>
                        <th width="80" align="right" id="value_tot_mrr_qnty"><? echo number_format($tot_mrr_qnty,2); ?></th>
                        <th width="100" align="right" id="value_tot_mrr_value"><? echo number_format($tot_mrr_value,2); ?></th>
                        <th align="right" id="value_tot_short_val"><? echo number_format($tot_short_val,2); ?></th>
	                </tfoot>
			</table>
	    </fieldset>
	    </div>
		<?
	}
	else if (str_replace("'","",$type)==2)  // Recap 1
	{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process ));

		$item_cate=str_replace("'","",$cbo_item_category_id);
		$cbo_date_type=str_replace("'","",$cbo_date_type);
		if(str_replace("'","",$cbo_issuing_bank)==0) $issuing_bank="%%"; else $issuing_bank=str_replace("'","",$cbo_issuing_bank);
		if(str_replace("'","",$cbo_lc_type_id)==0) $lc_type_id="%%"; else $lc_type_id=str_replace("'","",$cbo_lc_type_id);
		$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');

		if ($item_cate==11) $item_categoryids="4,11";
		else $item_categoryids=$item_cate;
		$sql_receive="select b.booking_id as booking_id,b.exchange_rate, sum(a.cons_quantity) as qnty, sum(a.cons_amount) as amnt, c.item_description, c.item_category_id, c.detarmination_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type, c.color from inv_transaction a, inv_receive_master b, product_details_master c where b.id=a.mst_id and a.prod_id=c.id and a.receive_basis=2 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and c.status_active=1 and b.receive_basis=2 and b.exchange_rate>0 and a.company_id=$cbo_company_name and a.item_category in($item_categoryids) group by b.booking_id,b.exchange_rate,c.item_description, c.item_category_id, c.detarmination_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type, c.color";
		//echo $sql_receive;
		$recvDataArray=sql_select($sql_receive);
		$receive_arr = array();
		
		foreach($recvDataArray as $row)
		{
			if ($row[csf('item_category_id')]==1) $item_description=$row[csf("yarn_count_id")]."*".$row[csf("yarn_comp_type1st")]."*".$row[csf("yarn_comp_percent1st")]."*".$row[csf("yarn_type")]."*".$row[csf("color")];
			else if ($row[csf('item_category_id')]==2 || $row[csf('item_category_id')]==3) $item_description=$row[csf('detarmination_id')];
			else $item_description=$row[csf('item_description')];

			$receive_arr[$row[csf('booking_id')]][$item_description]['qnty'] += $row[csf('qnty')];
			$receive_arr[$row[csf('booking_id')]][$item_description]['amnt'] += $row[csf('amnt')];
			$receive_arr[$row[csf('booking_id')]][$item_description]['exchange_rate'] = $row[csf('exchange_rate')];		
			
		}
		//echo '<pre>';print_r($receive_arr);die;

		$sql_after_receive="select d.booking_id as pi_id, d.exchange_rate,  sum(a.order_qnty) as qnty, sum(a.order_amount) as amnt, c.item_description, c.item_category_id, c.detarmination_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type, c.color from inv_transaction a, inv_receive_master d, product_details_master c 
        where d.id=a.mst_id and a.prod_id=c.id and a.receive_basis=1 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and c.status_active=1 and d.receive_basis=1 and d.exchange_rate>0 and a.company_id=$cbo_company_name and a.item_category in($item_categoryids) and d.status_active=1 and d.is_deleted=0 
		group by d.booking_id, d.exchange_rate, c.item_description, c.item_category_id, c.detarmination_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type, c.color";
		
		$AfterRecvDataArray=sql_select($sql_after_receive);
		$AfterRecv_arr = array();
		foreach($AfterRecvDataArray as $row)
		{
			if ($row[csf('item_category_id')]==1) $item_description=$row[csf("yarn_count_id")]."*".$row[csf("yarn_comp_type1st")]."*".$row[csf("yarn_comp_percent1st")]."*".$row[csf("yarn_type")]."*".$row[csf("color")];
			else if ($row[csf('item_category_id')]==2 || $row[csf('item_category_id')]==3) $item_description=$row[csf('detarmination_id')];
			else $item_description=$row[csf('item_description')];

			$AfterRecv_arr[$row[csf('pi_id')]][$item_description]['qnty'] += $row[csf('qnty')];
			$AfterRecv_arr[$row[csf('pi_id')]][$item_description]['amnt'] += $row[csf('amnt')];
			$AfterRecv_arr[$row[csf('pi_id')]][$item_description]['exchange_rate'] = $row[csf('exchange_rate')];						
		}
		
		$data_array=sql_select("select id, item_name, order_uom from lib_item_group");
		foreach($data_array as $row)
		{
			$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
			$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('order_uom')];
		}
		
		$section_arr=return_library_array("select id, section_name from lib_section where status_active=1 and is_deleted=0","id","section_name");


		$lc_sc_sql=sql_select("select id, export_lc_no as lc_sc_no, lc_date as lc_sc_date, 0 as type from com_export_lc where beneficiary_name=$cbo_company_name
			union all
			select id, contract_no as lc_sc_no, contract_date as lc_sc_date, 1 as type from com_sales_contract where beneficiary_name=$cbo_company_name");

		$lc_sc_data = array();
		foreach($lc_sc_sql as $row)
		{
			$lc_sc_data[$row[csf('id')]][$row[csf('type')]]['lc_sc_no'] = $row[csf('lc_sc_no')];
			$lc_sc_data[$row[csf('id')]][$row[csf('type')]]['lc_sc_date'] = $row[csf('lc_sc_date')];
		}

		// Print Link
		$print_report_format=sql_select("select format_id, report_id from lib_report_template where template_name=".$cbo_company_name." and module_id in(5,19) and report_id in(30,61,132) and status_active=1 and is_deleted=0");
		foreach ($print_report_format as $val) {
			if ($val[csf("report_id")]==30) $print_report_format_otherPerOrder=explode(",",$val[csf("format_id")]);
			else if ($val[csf("report_id")]==61) $print_report_format_statinaryPerOrder=explode(",",$val[csf("format_id")]);
			else if ($val[csf("report_id")]==132) $print_report_format_dyesChemicalPerOrder=explode(",",$val[csf("format_id")]);
			//else if ($val[csf("report_id")]==35) $print_report_format_wovenPartialFabBooking=explode(",",$val[csf("format_id")]);
			//else if ($val[csf("report_id")]==129) $print_report_format_issRtn=explode(",",$val[csf("format_id")]);
			
		}
		//echo '<pre>';print_r($print_report_format_dyesChemicalPerOrder);
		//30 => "Others Purchase Order"
		//61 => "Stationary Purchase Order"
		//132=>"Dyes And Chemical Purchase Order"
		//$woven_partial_fab_booking_arr=array(3);
		$dyes_chemical_category_arr=array(5,6,7,23);
		$stationary_purchase_category_arr=array(4,11);
		$others_purchase_category_arr=array(89,51,52,81,49,90,99,55,21,67,93,59,48,64,15,57,106,66,45,47,54,70,50,37,69,68,18,46,60,62,9,16,17,38,92,65,10,33,44,34,35,63,19,22,61,101,97,36,57,8,41,40,91,53,20,94,32,58,39);


		ob_start();
		$table_width=2300;		
		?>
		<style type="text/css">.wrd_brk{word-break: break-all;}</style>
	    <div style="width:<? echo $table_width+30; ?>px; margin-left:10px">
	        <fieldset style="width:100%;">
	            <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption">
	                <tr>
	                   <td align="center" width="100%" colspan="25" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
	                </tr>
	                <tr>
	                   <td align="center" width="100%" colspan="25" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
	                </tr>
	            </table>
	            <br />
	            <table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
	                <thead>
	                    <tr>
	                        <th colspan="17">PI Details</th>
	                        <th colspan="4">PO/MPO Information</th>
	                        <th colspan="5">Matarials Received Information</th>
	                    </tr>
	                    <tr>
	                        <th width="40">SL</th>
	                        <th width="120">PI No</th>
	                        <th width="120">BTB LC No</th>
	                        <th width="80">PI Date</th>
	                        <th width="80">Last Ship Date</th>
                            <th width="110">Office Note No.</th>
                            <th width="100">Section</th>
	                        <th width="140">Supplier Name</th>
	                        <th width="100">Item Category</th>
                            <th width="110">Item Group</th>
	                        <th width="120">Goods Description</th>
                            <th width="70">HS Code</th>
	                        <th width="60">UOM</th>
	                        <th width="80">PI Quantity</th>
	                        <th width="80">Unit Price</th>
	                        <th width="100">PI Value</th>
	                        <th width="80">Currency</th>

	                        <th width="80">PO/MPO No.</th>
	                        <th width="80">PO/MPO Qty.</th>
	                        <th width="80">PO/MPO Value</th>
                            <th width="110">Req No</th>

	                        <th width="80">MRR Qnty</th>
	                        <th width="90">MRR Value</th>
                            <th width="80">Balance Qty</th>
	                        <th >Balance Value</th>
	                    </tr>
	                </thead>
	            </table>

	            <div style="width:<? echo $table_width+20; ?>px; max-height:300px; overflow-y:scroll" id="scroll_body">
			 			<table width="<? echo $table_width; ?>" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_body">
	                <?
					if($cbo_date_type==1) //Pi Date
					{
						$pi_date_cond="";
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") $pi_date_cond=" and a.pi_date between $txt_date_from and $txt_date_to";
					}
					else if($cbo_date_type==2) //Pi Insert Date
					{
						$pi_date_cond="";
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
						{
							if($db_type==0)
							{
								$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
								$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
								$pi_date_cond=" and a.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
							}
							else if($db_type==2)
							{
								$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
								$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
								$pi_date_cond=" and a.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
							}
						}
					}
					else if($cbo_date_type==3) //BTB Date
					{
						$btb_date_cond="";
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") $btb_date_cond=" and a.lc_date between $txt_date_from and $txt_date_to";
					}
					else if($cbo_date_type==4) // BTB Insert Date
					{
						$btb_date_cond="";
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
						{
							if($db_type==0)
							{
								$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
								$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
								$btb_date_cond=" and a.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
							}
							else if($db_type==2)
							{
								$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
								$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
								$btb_date_cond=" and a.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
							}
						}
					}
					else if($cbo_date_type==5) //Maturity Date
					{
						$mat_str_cond="";
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") $mat_str_cond=" and a.maturity_date between $txt_date_from and $txt_date_to";
					}
					else if($cbo_date_type==6) //Maturity Insert Date
					{
						$mat_str_cond="";
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
						{
							if($db_type==0)
							{
								$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
								$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
								$mat_str_cond=" and a.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
							}
							else if($db_type==2)
							{
								$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
								$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
								$mat_str_cond=" and a.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
							}
						}
					}

					$all_pi_ids="";
					if($cbo_date_type==5 || $cbo_date_type==6)
					{
						$pi_id_arr=array();
						$invoice_sql =sql_select("select b.pi_id from com_import_invoice_mst a, com_import_invoice_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $mat_str_cond");
						foreach ($invoice_sql as $value)
						{
							$pi_id_arr[$value[csf("pi_id")]]=$value[csf("pi_id")];
						}
						$all_pi_ids=implode(",", $pi_id_arr);
					}
					
					if($cbo_date_type==3 || $cbo_date_type==4)
					{
						$ref_close_btb="";
						if($receive_status==4) $ref_close_btb=" and a.ref_closing_status<>1";
						if($addStringMulti!=""){ $import_source_cond = " and a.lc_category in($addStringMulti)";}else{ $import_source_cond = "";}
						if($db_type==0)
						{
							if($entry_form == 167)
							{
								if($item_cate>0){$item_cate_cond="and q.item_category_id=$item_cate";}else{$item_cate_cond='';}
								$sql="select a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id, group_concat(b.lc_sc_id) as lc_sc_id , group_concat(b.is_lc_sc) as is_lc_sc
								from com_btb_lc_master_details a left join com_btb_export_lc_attachment b on a.id=b.import_mst_id and b.is_deleted=0 and b.status_active=1
								where a.importer_id=$cbo_company_name and a.pi_entry_form=$entry_form and a.lc_type_id like '$lc_type_id' and a.issuing_bank_id like '$issuing_bank' and a.is_deleted=0 and a.status_active=1 $ref_close_btb $btb_date_cond $import_source_cond  group by a.id
								union all
								select a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id, group_concat(b.lc_sc_id) as lc_sc_id , group_concat(b.is_lc_sc) as is_lc_sc
								from wo_non_order_info_mst p, com_pi_item_details q, com_btb_lc_pi r, com_btb_lc_master_details a left join com_btb_export_lc_attachment b on a.id=b.import_mst_id and b.is_deleted=0 and b.status_active=1
								where p.id=q.work_order_id and q.pi_id=r.pi_id and r.com_btb_lc_master_details_id=a.id and a.importer_id=$cbo_company_name and p.item_category=4 and a.lc_type_id like '$lc_type_id' $item_cate_cond and a.issuing_bank_id like '$issuing_bank'  $ref_close_btb and a.is_deleted=0 and a.status_active=1 $btb_date_cond $import_source_cond  group by a.id";
							}
							elseif($entry_form == 172)
							{
								if($item_cate>0){$item_cate_cond="and c.item_category_id=$item_cate";}else{$item_cate_cond='';}
								$sql="select a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id, group_concat(b.lc_sc_id) as lc_sc_id , group_concat(b.is_lc_sc) as is_lc_sc
								from com_btb_lc_master_details a left join com_btb_export_lc_attachment b on a.id=b.import_mst_id and b.is_deleted=0 and b.status_active=1
								where a.importer_id=$cbo_company_name and a.pi_entry_form=$entry_form and a.lc_type_id like '$lc_type_id' and a.issuing_bank_id like '$issuing_bank' and a.is_deleted=0 and a.status_active=1 $ref_close_btb $btb_date_cond $import_source_cond and a.id not in(Select a.id from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d  where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id and a.status_active=1 and a.is_deleted=0 and $item_cate_cond and  d.item_category=4 group by a.id)  group by a.id";
							}
							else
							{
								$sql="select a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id, group_concat(b.lc_sc_id) as lc_sc_id , group_concat(b.is_lc_sc) as is_lc_sc
								from com_btb_lc_master_details a left join com_btb_export_lc_attachment b on a.id=b.import_mst_id and b.is_deleted=0 and b.status_active=1
								where a.importer_id=$cbo_company_name and a.pi_entry_form=$entry_form $ref_close_btb and a.lc_type_id like '$lc_type_id' and a.issuing_bank_id like '$issuing_bank' and a.is_deleted=0 and a.status_active=1 $btb_date_cond $import_source_cond  group by a.id";
							}


						}
						else if($db_type==2)
						{
							if($entry_form == 167)
							{
								if($item_cate>0){$item_cate_cond="and q.item_category_id=$item_cate";}else{$item_cate_cond='';}
								$sql="select a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id, LISTAGG(CAST(b.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.lc_sc_id) as lc_sc_id , LISTAGG(CAST(b.is_lc_sc AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.is_lc_sc) as is_lc_sc, 1 as type
								from com_btb_lc_master_details a left join com_btb_export_lc_attachment b on a.id=b.import_mst_id and b.is_deleted=0 and b.status_active=1
								where a.importer_id=$cbo_company_name and a.pi_entry_form=$entry_form and a.lc_type_id like '$lc_type_id' and a.issuing_bank_id like '$issuing_bank'  $ref_close_btb and a.is_deleted=0 and a.status_active=1 $btb_date_cond $import_source_cond
								group by a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id
								union all
								select a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id, LISTAGG(CAST(b.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.lc_sc_id) as lc_sc_id , LISTAGG(CAST(b.is_lc_sc AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.is_lc_sc) as is_lc_sc, 2 as type
								from wo_non_order_info_mst p, com_pi_item_details q, com_btb_lc_pi r, com_btb_lc_master_details a left join com_btb_export_lc_attachment b on a.id=b.import_mst_id and b.is_deleted=0 and b.status_active=1
								where p.id=q.work_order_id and q.pi_id=r.pi_id and r.com_btb_lc_master_details_id=a.id and a.importer_id=$cbo_company_name $item_cate_cond and a.lc_type_id like '$lc_type_id' and a.issuing_bank_id like '$issuing_bank' $ref_close_btb and a.is_deleted=0 and a.status_active=1 $btb_date_cond $import_source_cond
								group by a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id";
							}
							elseif($entry_form == 172)
							{
								if($item_cate>0){$item_cate_cond="and c.item_category_id=$item_cate";}else{$item_cate_cond='';}
								$sql="select a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id, LISTAGG(CAST(b.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.lc_sc_id) as lc_sc_id , LISTAGG(CAST(b.is_lc_sc AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.is_lc_sc) as is_lc_sc
								from com_btb_lc_master_details a left join com_btb_export_lc_attachment b on a.id=b.import_mst_id and b.is_deleted=0 and b.status_active=1
								where a.importer_id=$cbo_company_name and a.pi_entry_form=$entry_form and a.lc_type_id like '$lc_type_id' and a.issuing_bank_id like '$issuing_bank' and a.is_deleted=0 and a.status_active=1 $ref_close_btb $btb_date_cond $import_source_cond and a.id not in(Select a.id from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d  where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id and a.status_active=1 and a.is_deleted=0 and $item_cate_cond and  d.item_category=4 group by a.id)
								group by a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id";
							}
							else
							{
								$sql="select a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id, LISTAGG(CAST(b.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.lc_sc_id) as lc_sc_id , LISTAGG(CAST(b.is_lc_sc AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.is_lc_sc) as is_lc_sc
								from com_btb_lc_master_details a left join com_btb_export_lc_attachment b on a.id=b.import_mst_id and b.is_deleted=0 and b.status_active=1
								where a.importer_id=$cbo_company_name and a.pi_entry_form=$entry_form and a.lc_type_id like '$lc_type_id' and a.issuing_bank_id like '$issuing_bank' and a.is_deleted=0 and a.status_active=1 $ref_close_btb $btb_date_cond $import_source_cond group by a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id";
							}

						}

						$pi_ids="";
						foreach (sql_select($sql) as $value)
						{
							$pi_ids .=$value[csf("pi_id")].",";
							//$btb_lc_number_arr[$value[csf("pi_id")]]=$value[csf("lc_number")];
						}
						$pi_ids=implode(",", array_filter(array_unique(explode(",", chop($pi_ids, ",")))));
					}
					
					//echo $sql; die;
					
					//echo "mahbub"; die;

					if($pi_no != "")
					{
						$pi_sql_cond = " and a.pi_number='$pi_no'";
					}
					if ($all_pi_ids !="")
					{
						$pi_ids=$pi_ids.",".$all_pi_ids;
						$pi_ids=implode(",", array_filter(array_unique(explode(",", $pi_ids))));
					}

					$all_pi_cond="";
					if($pi_ids)
					{
						$all_pi_cond="and a.id in (".trim($pi_ids,',').")";
					}

					$ref_close_pi="";
					if($receive_status==4) $ref_close_pi=" and a.ref_closing_status<>1";
					if($entry_form == 167)
					{
						if($item_cate>0){$item_cate_cond="and b.item_category_id=$item_cate";}else{$item_cate_cond='';}
						$sql_pi="SELECT a.id, a.item_category_id, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, a.intendor_name, a.goods_rcv_status, b.color_id, b.count_name, b.yarn_type, b.uom, b.quantity, b.rate, b.amount, b.net_pi_rate, b.net_pi_amount, b.yarn_composition_item1, b.yarn_composition_percentage1, b.fabric_composition, b.fabric_construction, b.item_description, 1 as type, b.work_order_no, b.work_order_id, b.work_order_dtls_id, b.item_prod_id, b.item_group, b.hs_code, b.determination_id
						from com_pi_master_details a, com_pi_item_details b
						where a.id=b.pi_id and b.is_deleted=0 and b.status_active=1 and a.entry_form=$entry_form and a.importer_id=$cbo_company_name $ref_close_pi $item_cate_cond $pi_date_cond $all_pi_cond $pi_sql_cond
						union all
						SELECT a.id, c.item_category as item_category_id, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, a.intendor_name, a.goods_rcv_status, b.color_id, b.count_name, b.yarn_type, b.uom, b.quantity, b.rate, b.amount, b.net_pi_rate, b.net_pi_amount, b.yarn_composition_item1, b.yarn_composition_percentage1, b.fabric_composition, b.fabric_construction, b.item_description, 2 as type, b.work_order_no, b.work_order_id, b.work_order_dtls_id, b.item_prod_id, b.item_group, b.hs_code, b.determination_id
						from com_pi_master_details a, com_pi_item_details b,  wo_non_order_info_mst c
						where a.id=b.pi_id and b.work_order_id=c.id and b.is_deleted=0 and b.status_active=1 and c.item_category=4 and a.entry_form=$entry_form and a.importer_id=$cbo_company_name $ref_close_pi $item_cate_cond  $pi_date_cond $all_pi_cond $pi_sql_cond
						order by pi_date desc";
					}
					elseif($entry_form == 172)
					{
						if($item_cate>0){$item_cate_cond="and b.item_category_id=$item_cate";}else{$item_cate_cond='';}
						$sql_pi="SELECT a.id, a.item_category_id, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, a.intendor_name, a.goods_rcv_status, b.color_id, b.count_name, b.yarn_type, b.uom, b.quantity, b.rate, b.amount, b.net_pi_rate, b.net_pi_amount, b.yarn_composition_item1, b.yarn_composition_percentage1, b.fabric_composition, b.fabric_construction, b.item_description, b.work_order_no, b.work_order_id, b.work_order_dtls_id, b.item_prod_id, b.item_group, b.hs_code, b.determination_id
						from com_pi_master_details a, com_pi_item_details b
						where a.id=b.pi_id and b.is_deleted=0 and b.status_active=1 and a.entry_form=$entry_form and a.importer_id=$cbo_company_name $ref_close_pi $item_cate_cond $pi_date_cond $all_pi_cond $pi_sql_cond
						order by a.pi_date desc";
					}
					else
					{
						if($item_cate>0){$item_cate_cond="and b.item_category_id=$item_cate";}else{$item_cate_cond='';}
						$sql_pi="SELECT a.id, a.item_category_id, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, a.intendor_name, a.goods_rcv_status, b.color_id, b.count_name, b.yarn_type, b.uom, b.quantity, b.rate, b.amount, b.net_pi_rate, b.net_pi_amount, b.yarn_composition_item1, b.yarn_composition_percentage1, b.fabric_composition, b.fabric_construction, b.item_description, b.work_order_no, b.work_order_id, b.work_order_dtls_id, b.item_prod_id, b.item_group, b.hs_code, b.determination_id 
						from com_pi_master_details a, com_pi_item_details b
						where a.id=b.pi_id and b.is_deleted=0 and b.status_active=1 and a.entry_form=$entry_form and a.importer_id=$cbo_company_name $ref_close_pi $item_cate_cond $pi_date_cond $all_pi_cond $pi_sql_cond
						order by a.pi_date desc";
					}

					//echo $sql_pi;die;
					$sql_pi_result=sql_select($sql_pi);
					foreach($sql_pi_result as $row)
					{
						$description="";
						if($row[csf("item_category_id")]==1)
						{
							$description=$row[csf("count_name")]."*".$row[csf("yarn_composition_item1")]."*".$row[csf("yarn_composition_percentage1")]."*".$row[csf("yarn_type")]."*".$row[csf("color_id")];
						}
						else if($row[csf("item_category_id")]==13)
						{
							$description=$row[csf("fabric_construction")].', '.trim($row[csf("fabric_composition")]);
						}
						else if($row[csf("item_category_id")]==2 || $row[csf("item_category_id")]==3)
						{
							$description=$row[csf("determination_id")];
						}					
						else
						{
							$description=$row[csf("item_description")];
						}

						$pi_data_arr[$row[csf("id")]][$description]["id"]=$row[csf("id")];
						$pi_data_arr[$row[csf("id")]][$description]["item_category_id"]=$row[csf("item_category_id")];
						$pi_data_arr[$row[csf("id")]][$description]["supplier_id"]=$row[csf("supplier_id")];
						$pi_data_arr[$row[csf("id")]][$description]["pi_number"]=$row[csf("pi_number")];
						$pi_data_arr[$row[csf("id")]][$description]["pi_date"]=$row[csf("pi_date")];
						$pi_data_arr[$row[csf("id")]][$description]["goods_rcv_status"]=$row[csf("goods_rcv_status")];
						$pi_data_arr[$row[csf("id")]][$description]["last_shipment_date"]=$row[csf("last_shipment_date")];
						$pi_data_arr[$row[csf("id")]][$description]["currency_id"]=$row[csf("currency_id")];
						$pi_data_arr[$row[csf("id")]][$description]["intendor_name"]=$row[csf("intendor_name")];
						$pi_data_arr[$row[csf("id")]][$description]["work_order_no"]=$row[csf("work_order_no")];
						$pi_data_arr[$row[csf("id")]][$description]["item_prod_id"]=$row[csf("item_prod_id")];
						$pi_data_arr[$row[csf("id")]][$description]["color_id"]=$row[csf("color_id")];
						$pi_data_arr[$row[csf("id")]][$description]["count_name"]=$row[csf("count_name")];
						$pi_data_arr[$row[csf("id")]][$description]["yarn_type"]=$row[csf("yarn_type")];
						$pi_data_arr[$row[csf("id")]][$description]["yarn_composition_item1"]=$row[csf("yarn_composition_item1")];
						$pi_data_arr[$row[csf("id")]][$description]["yarn_composition_percentage1"]=$row[csf("yarn_composition_percentage1")];
						
						$pi_data_arr[$row[csf("id")]][$description]["item_group"]=$row[csf("item_group")];
						$pi_data_arr[$row[csf("id")]][$description]["hs_code"]=$row[csf("hs_code")];
						
						$pi_data_arr[$row[csf("id")]][$description]["uom"]=$row[csf("uom")];
						$pi_data_arr[$row[csf("id")]][$description]["quantity"]+=$row[csf("quantity")];
						$pi_data_arr[$row[csf("id")]][$description]["rate"]=$row[csf("rate")];
						$pi_data_arr[$row[csf("id")]][$description]["amount"]+=$row[csf("amount")];
						$pi_data_arr[$row[csf("id")]][$description]["net_pi_rate"]=$row[csf("net_pi_rate")];
						$pi_data_arr[$row[csf("id")]][$description]["net_pi_amount"]+=$row[csf("net_pi_amount")];

						$pi_data_arr[$row[csf("id")]][$description]["work_order_dtls_id"]=$row[csf("work_order_dtls_id")];
						
						if($duplicate_wo_check[$row[csf("work_order_id")]][$row[csf("id")]][$description]=="")
						{
							$duplicate_wo_check[$row[csf("work_order_id")]][$row[csf("id")]][$description]=$row[csf("work_order_id")];
							$pi_data_arr[$row[csf("id")]][$description]["work_order_id"].=$row[csf("work_order_id")].",";
						}

						$pi_data_arr[$row[csf("id")]][$description]["work_order_no_all"] .= $row[csf("work_order_no")].",";
						/*$work_order_arr[$row[csf("id")]]["work_order_no"] .= $row[csf("work_order_no")].",";
						$work_order_arr[$row[csf("id")]]["work_order_dtls_id"] .= $row[csf("work_order_dtls_id")].",";*/

						$work_order .= $row[csf("work_order_no")].",";
						$work_order_dtls .= $row[csf("work_order_dtls_id")].",";
						$pi_amount[$row[csf("id")]][$description]["amount"]+=$row[csf("amount")];
						//$pi_amount[$row[csf("id")]]["amount"]+=$row[csf("amount")];
						//$pi_quantity[$row[csf("id")]]["quantity"]+=$row[csf("quantity")];
						$pi_quantity[$row[csf("id")]][$description]["quantity"]+=$row[csf("quantity")];
						$pi_IDS.=$row[csf("id")].',';

					}
					//echo '<pre>';print_r($pi_data_arr);
					//echo "select a.id, a.lc_number from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and b.pi_id in (".trim($pi_ids,',').")";die;

					if ($pi_IDS != ''){
						$pi_IDS = implode(",", array_unique(explode(",", chop($pi_IDS, ","))));
						$btb_pi_cond="and b.pi_id in (".trim($pi_IDS,',').")";

						$btb_lc_array=array();
						$sql_btb=sql_select("select b.pi_id, a.lc_number from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id $btb_pi_cond");
						foreach ($sql_btb as $val) {
							$btb_lc_array[$val[csf("pi_id")]]=$val[csf("lc_number")];
						}
					}					

					$wo_num = "'".implode("','", array_unique(explode(",", chop($work_order, ","))))."'";
					$dtls_id = implode(",", array_unique(explode(",", chop($work_order_dtls, ","))));


					if ($item_cate==2)
					{
						$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
						$sql_comp="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 and b.is_deleted=0 order by b.id";
						$sql_comp_array=sql_select($sql_comp);
						$composition_arr=array();
						$construction_arr=array();
						if (count($sql_comp_array)>0)
						{
							foreach( $sql_comp_array as $row )
							{
								$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
								$construction_arr[$row[csf('id')]]=$row[csf('construction')];
							}
						}
					}

					$product_data_arr=array();
					$sql_prod=sql_select("select id as prod_id, item_description from product_details_master where company_id=$cbo_company_name and item_category_id=$item_cate and status_active=1 and is_deleted=0");
					foreach ($sql_prod as $value) {
						$product_data_arr[$value[csf("prod_id")]]=$value[csf("item_description")];
					}
					//echo '<pre>';print_r($product_data_arr);		


					$sql_wo="SELECT a.wo_number, a.wo_number_prefix_num, a.id as mst_id, b.supplier_order_quantity, b.amount, b.id as dtls_id, b.item_id,  null as description, 0 as type, a.supplier_id, a.wo_date, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.delivery_date, a.attention, a.delivery_place, a.location_id, b.requisition_no as requisition_id, d.requ_no, null as determination_id
					from wo_non_order_info_mst a, wo_non_order_info_dtls b
					left join inv_purchase_requisition_dtls c on b.requisition_dtls_id=c.id and c.status_active=1
					left join inv_purchase_requisition_mst d on c.mst_id=d.id
					where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.wo_number in ($wo_num)
					union
					SELECT a.booking_no as wo_number, a.booking_no_prefix_num as wo_number_prefix_num, a.id as mst_id, b.fin_fab_qnty as supplier_order_quantity, b.amount, b.id as dtls_id, 0 as item_id, (b.construction || ', ' || b.copmposition) as description, 1 as type, null as supplier_id, null as wo_date, null as currency_id, null as wo_basis_id, null as pay_mode, null as source, null as delivery_date, null as attention, null as delivery_place, null as location_id, null as requisition_id, null as requ_no, LIB_YARN_COUNT_DETER_ID as determination_id
					from wo_booking_mst a, wo_booking_dtls b, WO_PRE_COST_FABRIC_COST_DTLS c 
					where a.booking_no=b.booking_no and b.PRE_COST_FABRIC_COST_DTLS_ID=c.id and b.JOB_NO=c.JOB_NO and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in ($wo_num) and a.item_category not in(2)
					union
					SELECT a.booking_no as wo_number, a.booking_no_prefix_num as wo_number_prefix_num, a.id as mst_id, b.fin_fab_qnty as supplier_order_quantity, b.amount, b.id as dtls_id, 0 as item_id, (b.construction || ', ' || b.copmposition) as description, 1 as type, null as supplier_id, null as wo_date, null as currency_id, null as wo_basis_id, null as pay_mode, null as source, null as delivery_date, null as attention, null as delivery_place, null as location_id, null as requisition_id, null as requ_no , LIB_YARN_COUNT_DETER_ID as determination_id
					from wo_booking_mst a, wo_booking_dtls b, WO_PRE_COST_FABRIC_COST_DTLS c 
					where a.booking_no=b.booking_no and b.PRE_COST_FABRIC_COST_DTLS_ID=c.id and b.JOB_NO=c.JOB_NO and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in ($wo_num) and a.item_category in(2)
					union
					SELECT a.booking_no as wo_number, a.booking_no_prefix_num as wo_number_prefix_num, a.id as mst_id, b.finish_fabric as supplier_order_quantity, b.amount, b.id as dtls_id, 0 as item_id, b.fabric_description as description, 2 as type, null as supplier_id, null as wo_date, null as currency_id, null as wo_basis_id, null as pay_mode, null as source, null as delivery_date, null as attention, null as delivery_place, null as location_id, null as requisition_id, null as requ_no, b.lib_yarn_count_deter_id as determination_id
					from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
					where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in ($wo_num) and a.item_category in(2)";
					//echo $sql_wo;
					$sql_wo_res=sql_select($sql_wo);
 					
					foreach ($sql_wo_res as $val)
					{
						if($dtls_id_check[$val[csf("dtls_id")]]=="")
						{
							$dtls_id_check[$val[csf("dtls_id")]]=$val[csf("dtls_id")];
							$wo_data_arr[$val[csf("dtls_id")]]["wo_number_prefix_num"].=$val[csf("wo_number_prefix_num")].",";
							$wo_data_arr[$val[csf("dtls_id")]]["supplier_order_quantity"]+=$val[csf("supplier_order_quantity")];
							$wo_data_arr[$val[csf("dtls_id")]]["wo_number"]=$val[csf("wo_number")];
							$wo_data_arr[$val[csf("dtls_id")]]["mst_id"]=$val[csf("mst_id")];
							$wo_data_arr[$val[csf("dtls_id")]]["supplier_id"]=$val[csf("supplier_id")];
							$wo_data_arr[$val[csf("dtls_id")]]["wo_date"]=$val[csf("wo_date")];
							$wo_data_arr[$val[csf("dtls_id")]]["currency_id"]=$val[csf("currency_id")];
							$wo_data_arr[$val[csf("dtls_id")]]["wo_basis_id"]=$val[csf("wo_basis_id")];
							$wo_data_arr[$val[csf("dtls_id")]]["pay_mode"]=$val[csf("pay_mode")];
							$wo_data_arr[$val[csf("dtls_id")]]["source"]=$val[csf("source")];
							$wo_data_arr[$val[csf("dtls_id")]]["delivery_date"]=$val[csf("delivery_date")];
							$wo_data_arr[$val[csf("dtls_id")]]["attention"]=$val[csf("attention")];
							$wo_data_arr[$val[csf("dtls_id")]]["location_id"]=$val[csf("location_id")];
							$wo_data_arr[$val[csf("dtls_id")]]["delivery_place"]=$val[csf("delivery_place")];
							$wo_data_arr[$val[csf("dtls_id")]]["requisition_id"]=$val[csf("requisition_id")];
							$wo_data_arr[$val[csf("dtls_id")]]["requ_no"]=$val[csf("requ_no")];
							
							$wo_data_arr[$val[csf("dtls_id")]]["amount"]+=$val[csf("amount")];
	
							if ($val[csf("type")]==0) 
								$descript=$product_data_arr[$val[csf("item_id")]];
							else if ($val[csf("type")]==2) 
								$descript=$descript=$val[csf("determination_id")];
							else 
								$descript=$val[csf("determination_id")];
	
							$aftergoods_wo_data_arr[$val[csf("wo_number")]][$descript]["wo_number_prefix_num"].=$val[csf("wo_number_prefix_num")].",";
							$aftergoods_wo_data_arr[$val[csf("wo_number")]][$descript]["supplier_order_quantity"]+=$val[csf("supplier_order_quantity")];
							$aftergoods_wo_data_arr[$val[csf("wo_number")]][$descript]["mst_id"]=$val[csf("mst_id")];
							$aftergoods_wo_data_arr[$val[csf("wo_number")]][$descript]["amount"]+=$val[csf("amount")];
						}
					}

					//echo '<pre>';print_r($aftergoods_wo_data_arr);
					
					
					$sql_office_note="select a.id as ID, a.con_system_id as CON_SYSTEM_ID, a.section as SECTION, b.pi_id as PI_ID
					from commercial_office_note_mst a, commercial_office_note_dtls b 
					where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.importer_id=$cbo_company_name
					group by a.id, a.con_system_id, a.section, b.pi_id";

					$sql_office_note_result=sql_select($sql_office_note);
					$office_note_data=array();
					foreach($sql_office_note_result as $row)
					{
						$office_note_data[$row["PI_ID"]]["CON_SYSTEM_ID"]=$row["CON_SYSTEM_ID"];
						$office_note_data[$row["PI_ID"]]["SECTION"]=$row["SECTION"];
					}
					unset($sql_office_note_result);


					//echo "<pre>";print_r($pi_data_arr); 

					$i=1; $total_pi_qty=0;$total_pi_val=0; $total_short_val=0;$total_mrr_qnty=0;
					foreach ($pi_data_arr as $pi_id => $value)
					{
						$rowspan=count($value);
						$z=1;
						foreach ($value as $description => $data)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$wo_dtls_id=$data[('work_order_dtls_id')];
							if ($data[('item_category_id')]==1)
							{
								$des_ref=explode('*',$description);
								$good_description=$count_arr[$des_ref[0]].' '.$composition[$des_ref[1]].' '.$des_ref[2].' '.$yarn_type[$des_ref[3]].' '.$color_arr[$des_ref[4]];
							}
							else
							{
								if($data[('item_category_id')]==2 || $data[('item_category_id')]==3)
								{
									$good_description=$construction_arr[$description].", ".$composition_arr[$description];
								}
								else
								{
									$good_description=$description;
								}
								
							}
							
							$item_rate=$data[('amount')]/$data[('quantity')];//work_order_no
							$mrr_qnty=0; $mrr_value=0; $mrr_value_usd=0; $supplier_order_quantity=$all_wo=$wo_amount=$req_no=$requisition_id="";
							if($data[('goods_rcv_status')]==1)
							{
								$rcv_basis=2;
								// $pi_wo_id=chop($pi_IDS,",");
								$pi_wo_id=chop($data[('work_order_id')],",");
								// $all_wo_id_arr=explode(",",$pi_wo_id);
								$all_wo_id_arr=explode(",",chop($data[('work_order_id')],","));
								// var_dump($all_wo_id_arr);die;
								foreach($all_wo_id_arr as $wo_id)
								{
									$mrr_qnty+=$receive_arr[$wo_id][$description]['qnty'];
									// echo $receive_arr[$wo_id]['amnt']."=".$receive_arr[$wo_id]['exchange_rate'];die;
									if($receive_arr[$wo_id][$description]['qnty']!=0 && $receive_arr[$wo_id][$description]['qnty'] !='') $mrr_value_usd+=$receive_arr[$wo_id][$description]['qnty']*$item_rate;
									//$mrr_value_usd+=$receive_arr[$wo_id][$description]['amnt']/$receive_arr[$wo_id][$description]['exchange_rate'];
								}
								
								/*$exchange_rate=$AfterRecv_arr[$pi_id]['exchange_rate'];
								if($mrr_value!=0 && $mrr_value !='')
								{
									$mrr_value_usd=$mrr_value/$exchange_rate;
								}*/
								
								$all_wo_no_arr = array_unique(explode(',', chop($data[('work_order_no_all')],',')));
								foreach($all_wo_no_arr as $val)
								{
									$all_wo .=rtrim($aftergoods_wo_data_arr[$val][$description]["wo_number_prefix_num"],',').',';
									$supplier_order_quantity+=$aftergoods_wo_data_arr[$val][$description]["supplier_order_quantity"];
									$wo_amount+=$aftergoods_wo_data_arr[$val][$description]["amount"];
								}
								$all_wo=implode(',',array_unique(explode(',', chop($all_wo,','))));

								/*$all_wo = implode(',',array_unique(explode(',', chop($aftergoods_wo_data_arr[$data[('work_order_no')]][$data[('item_prod_id')]]["wo_number_prefix_num"],','))));
								$supplier_order_quantity=$aftergoods_wo_data_arr[$data[('work_order_no')]][$data[('item_prod_id')]]["supplier_order_quantity"];
								$wo_amount=$aftergoods_wo_data_arr[$data[('work_order_no')]][$data[('item_prod_id')]]["amount"];*/	

							}
							else
							{
								$rcv_basis=1;
								$pi_wo_id=$pi_id;								
								$mrr_qnty=$AfterRecv_arr[$pi_id][$description]['qnty'];
								$mrr_value_usd=$AfterRecv_arr[$pi_id][$description]['amnt'];
								//echo $pi_id."=".$description;
								/* $mrr_value=$AfterRecv_arr[$pi_id]['amnt'];
								$exchange_rate=$AfterRecv_arr[$pi_id]['exchange_rate'];
								if($mrr_value!=0 && $mrr_value !='')
								{
									$mrr_value_usd=$mrr_value/$exchange_rate;
								} */
								//$mrr_value_usd=$mrr_value/$exchange_rate;

								$all_wo_no_arr = array_unique(explode(',', chop($data[('work_order_no_all')],',')));
								foreach($all_wo_no_arr as $val)
								{

									$all_wo .=rtrim($aftergoods_wo_data_arr[$val][$description]["wo_number_prefix_num"],',').',';
									$supplier_order_quantity+=$aftergoods_wo_data_arr[$val][$description]["supplier_order_quantity"];
									$wo_amount+=$aftergoods_wo_data_arr[$val][$description]["amount"];								
								}
								$all_wo=implode(',',array_unique(explode(',', chop($all_wo,','))));

								// $all_wo = implode(',', array_unique(explode(',', chop($wo_data_arr[$wo_dtls_id]["wo_number_prefix_num"],','))));
								// $supplier_order_quantity=$wo_data_arr[$wo_dtls_id]["supplier_order_quantity"];
								// $wo_amount=$wo_data_arr[$wo_dtls_id]["amount"];
								
								// $requisition_id=$wo_data_arr[$wo_dtls_id]["requisition_id"];
								// $req_no=$wo_data_arr[$wo_dtls_id]["requ_no"];
							}

							$cbo_company_name=str_replace("'", "", $cbo_company_name);
							$mst_id=$wo_data_arr[$wo_dtls_id]["mst_id"];
							$wo_number=$wo_data_arr[$wo_dtls_id]["wo_number"];
							$supplier_id=$wo_data_arr[$wo_dtls_id]["supplier_id"];
							$wo_date=change_date_format($wo_data_arr[$wo_dtls_id]["wo_date"]);
							$currency_id=$wo_data_arr[$wo_dtls_id]["currency_id"];
							$wo_basis_id=$wo_data_arr[$wo_dtls_id]["wo_basis_id"];
							$pay_mode=$wo_data_arr[$wo_dtls_id]["pay_mode"];
							$source=$wo_data_arr[$wo_dtls_id]["source"];
							$delivery_date=change_date_format($wo_data_arr[$wo_dtls_id]["delivery_date"]);
							$attention=$wo_data_arr[$wo_dtls_id]["attention"];
							$location_id=$wo_data_arr[$wo_dtls_id]["location_id"];
							$delivery_place=$wo_data_arr[$wo_dtls_id]["delivery_place"];
							$requisition_id=$wo_data_arr[$wo_dtls_id]["requisition_id"];
							if (in_array($data[('item_category_id')], $dyes_chemical_category_arr))
							{
								$print_format_id=$print_report_format_dyesChemicalPerOrder[0];
								if ($print_format_id==78)  //Print button
								{									
									$print_link="<a href='#' onClick=\"print_report('".$cbo_company_name."*".$mst_id."', 'dyes_chemical_work_print', '../../commercial/work_order/requires/dyes_and_chemical_work_order_controller')\"> ".$all_wo." <a/>";
								}
								else if ($print_format_id==84)  //Print 2 button
								{									
									$print_link="<a href='#' onClick=\"print_report('".$cbo_company_name."*".$mst_id."', 'dyes_chemical_work_print2', '../../commercial/work_order/requires/dyes_and_chemical_work_order_controller')\"> ".$all_wo." <a/>";
								}  								
								else $print_link=$all_wo;
							}
							else if (in_array($data[('item_category_id')], $stationary_purchase_category_arr))
							{
								$print_format_id=$print_report_format_statinaryPerOrder[0];							
								if ($print_format_id==134)  //Print button
								{									
									$report_title='Work Order';						
									$print_link="<a href='#' onClick=\"print_report('".$cbo_company_name."*".$wo_number."*".$data[('item_category_id')]."*".$supplier_id."*".$wo_date."*".$currency_id."*".$wo_basis_id."*".$pay_mode."*".$source."*".$delivery_date."*".$attention."*0*".$requisition_id."*0*".$delivery_place."*".$mst_id."*".$report_title."*".$location_id."', 'stationary_work_print', '../../commercial/work_order/requires/stationary_work_order_controller')\"> ".$all_wo." <a/>";
								}
								else if ($print_format_id==66)  //Print 2 Button
								{
									$report_title='Purchase Order';
									$print_link="<a href='#' onClick=\"print_report('".$cbo_company_name."*".$wo_number."*".$data[('item_category_id')]."*".$supplier_id."*".$wo_date."*".$currency_id."*".$wo_basis_id."*".$pay_mode."*".$source."*".$delivery_date."*".$attention."*0*".$requisition_id."*0*".$delivery_place."*".$mst_id."*".$report_title."*".$location_id."', 'stationary_work_order_print', '../../commercial/work_order/requires/stationary_work_order_controller')\"> ".$all_wo." <a/>";						
								}
								else $print_link=$all_wo;
							}
							else if (in_array($data[('item_category_id')], $others_purchase_category_arr))
							{
								$print_format_id=$print_report_format_otherPerOrder[0];								
								if ($print_format_id==84)  //Print 2 button
								{		
									$report_title='Others Purchase Order';
									$print_link="<a href='#' onClick=\"print_report('".$cbo_company_name."*".$mst_id."*".$requisition_id."*".$report_title."*".$location_id."', 'spare_parts_work_order_print2', '../../commercial/work_order/requires/spare_parts_work_order_controller')\"> ".$all_wo." <a/>";	
								}
								else if ($print_format_id==85)  //Print 3 Button
								{
									$report_title='Others Purchase Order';
									$print_link="<a href='#' onClick=\"print_report('".$cbo_company_name."*".$mst_id."*".$requisition_id."*".$report_title."*".$wo_number."*".$wo_date."*".$location_id."', 'spare_parts_work_order_print3', '../../commercial/work_order/requires/spare_parts_work_order_controller')\"> ".$all_wo." <a/>";
								}
								else if ($print_format_id==134)  //Print Button
								{
									$report_title='Others Purchase Order';
									$print_link="<a href='#' onClick=\"print_report('".$cbo_company_name."*".$mst_id."*".$report_title."*".$location_id."*4', 'spare_parts_work_print', '../../commercial/work_order/requires/spare_parts_work_order_controller')\"> ".$all_wo." <a/>";						
								}
								else $print_link=$all_wo;
							}
							else $print_link=$all_wo;
							/*else if (in_array($data[('item_category_id')], $woven_partial_fab_booking_arr))
							{
								$print_format_id=$print_report_format_wovenPartialFabBooking[0];								
								if ($print_format_id==143)  //Print 1 button
								{		
									$report_title='Partial Fabric Booking';
									$print_link="<a href='#' onClick=\"print_report('".$cbo_company_name."*".$mst_id."*".$requisition_id."*".$report_title."*".$location_id."', 'spare_parts_work_order_print2', '../../commercial/work_order/requires/spare_parts_work_order_controller')\"> ".$all_wo." <a/>";	
								}
								else if ($print_format_id==84)  //Print 2 Button
								{
									$report_title='Others Purchase Order';
									$print_link="<a href='#' onClick=\"print_report('".$cbo_company_name."*".$mst_id."*".$requisition_id."*".$report_title."*".$wo_number."*".$wo_date."*".$location_id."', 'spare_parts_work_order_print3', '../../order/woven_gmts/requires/partial_fabric_booking_controller')\"> ".$all_wo." <a/>";
								}
								else if ($print_format_id==85)  //Print 3 Button
								{
									$report_title='Others Purchase Order';
									$print_link="<a href='#' onClick=\"print_report('".$cbo_company_name."*".$mst_id."*".$report_title."*".$location_id."*4', 'spare_parts_work_print', '../../commercial/work_order/requires/spare_parts_work_order_controller')\"> ".$all_wo." <a/>";						
								}
								else if ($print_format_id==151)  //Print 4 Button
								{
									$report_title='Others Purchase Order';
									$print_link="<a href='#' onClick=\"print_report('".$cbo_company_name."*".$mst_id."*".$report_title."*".$location_id."*4', 'spare_parts_work_print', '../../commercial/work_order/requires/spare_parts_work_order_controller')\"> ".$all_wo." <a/>";						
								}
								else if ($print_format_id==160)  //Print 5 Button
								{
									$report_title='Others Purchase Order';
									$print_link="<a href='#' onClick=\"print_report('".$cbo_company_name."*".$mst_id."*".$report_title."*".$location_id."*4', 'spare_parts_work_print', '../../commercial/work_order/requires/spare_parts_work_order_controller')\"> ".$all_wo." <a/>";						
								}
								else if ($print_format_id==175)  //Print 6 Button
								{
									$report_title='Others Purchase Order';
									$print_link="<a href='#' onClick=\"print_report('".$cbo_company_name."*".$mst_id."*".$report_title."*".$location_id."*4', 'spare_parts_work_print', '../../commercial/work_order/requires/spare_parts_work_order_controller')\"> ".$all_wo." <a/>";						
								}
								else if ($print_format_id==155)  //Fabric Booking Report Button
								{
									$report_title='Others Purchase Order';
									$print_link="<a href='#' onClick=\"print_report('".$cbo_company_name."*".$mst_id."*".$report_title."*".$location_id."*4', 'spare_parts_work_print', '../../commercial/work_order/requires/spare_parts_work_order_controller')\"> ".$all_wo." <a/>";						
								}
								else $print_link=$all_wo;
							}*/	


							
							//echo  $mrr_value_usd.test;die;
							
							//if($receive_status==1 && ($mrr_qnty==0 || $mrr_qnty==''))
							//echo $receive_status.'system';
							if($receive_status==1 && ($mrr_value_usd=='' || $mrr_value_usd==0))
							{
								//echo $data[('amount')]."==".$mrr_value_usd."==".$exchange_rate."++";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" class="wrd_brk" align="center" <? echo $lc_bgcolor; ?>>
										<? echo $i; ?>
									</td>
									<td width="120" class="wrd_brk" <? echo $lc_bgcolor; ?>>
										<p><? echo $data[('pi_number')]; ?></p>
									</td>
									<td width="120" <? echo $lc_bgcolor; ?> class="wrd_brk">
										<p><? echo $btb_lc_array[$pi_id]; ?></p>
									</td>
									<td width="80" class="wrd_brk" align="center">
										<? if($data[('pi_date')]!="" && $data[('pi_date')]!="0000-00-00")   echo change_date_format($data[('pi_date')]); ?>
									</td>
									<td width="80" class="wrd_brk" align="center">
										<? if($data[('last_shipment_date')]!="" && $data[('last_shipment_date')]) echo change_date_format($data[('last_shipment_date')]); ?>
									</td>
                                    <td width="110" class="wrd_brk"><p><? echo $office_note_data[$pi_id]["CON_SYSTEM_ID"]; ?></p></td>
                                    <td width="100" class="wrd_brk"><p><? echo $section_arr[$office_note_data[$pi_id]["SECTION"]]; ?></p></td>
									<td width="140" class="wrd_brk"><p><? echo $supplier_arr[$data[('supplier_id')]]; ?></p></td>
									<td width="100" class="wrd_brk"><p><? echo $item_category[$data[('item_category_id')]]; ?></p></td>
                                    <td width="110" class="wrd_brk" title="<?= $data[('item_group')];?>"><p><? echo $trim_group_arr[$data[('item_group')]]['name']; ?></p></td>
									<td width="120" class="wrd_brk" align="center">
										<p><? echo $good_description; ?></p>
									</td>
                                    <td width="70" class="wrd_brk" align="center"><p><? echo $data[('hs_code')]; ?></p></td>
									<td width="60" class="wrd_brk" align="center"><p><? echo $unit_of_measurement[$data[('uom')]]; ?></p></td>
									<td width="80" class="wrd_brk" align="right">
										<p>
											<?
												$total_pi_qty+=$data[('quantity')];
												echo number_format($data[('quantity')],2,'.','');
											?>
										</p>
									</td>
									<td width="80" class="wrd_brk" align="right"><p><? echo number_format($item_rate,2,'.',''); ?></p></td>
									<td width="100" class="wrd_brk" align="right">
										<p>
											<?
												$total_pi_val+=$data[('amount')];
												echo number_format($data[('amount')],2,'.','');
											?>
										</p>
									</td>
									<td width="80" class="wrd_brk" align="center"><P><? echo $currency[$data[('currency_id')]]; ?></P></td>
									<td width="80" class="wrd_brk" align="right"><p>
										<?
											echo $print_link;
										?></p>
									</td>
									<td width="80" class="wrd_brk" align="right">
										<?
											//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["supplier_order_quantity"],2);
											echo number_format($supplier_order_quantity,2);
										?>
									</td>
									<td width="80" class="wrd_brk" align="right" title="<? echo $z; ?>">
										<?
											//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["amount"],2);
											echo number_format($wo_amount,2);
										?>
									</td>
                                    <td width="110" class="wrd_brk" align="right" title="<? echo $requisition_id; ?>"><? echo $req_no; ?></td>
                                    <td width="80" class="wrd_brk" align="right" title="<? echo chop($data[('work_order_id')],","); ?>">
                                        <a href="javascript:void(0)" onClick="show_qty_details('<? echo $pi_wo_id; ?>',<? echo $item_cate; ?>,<? echo $data[('goods_rcv_status')]; ?>)">
                                            <?
                                                echo $display_font_color.number_format($mrr_qnty,2).$font_end;
                                            ?>
                                        </a>
                                    </td>
                                    <td width="90" align="right" class="wrd_brk">
                                            <?
                                                echo $display_font_color.number_format($mrr_value_usd,2).$font_end;
												$balance_qnty=$pi_quantity[$pi_id][$description]["quantity"]-$mrr_qnty;
												$short_value=$balance_value=0;
												$short_value=$mrr_value_usd-$pi_amount[$pi_id][$description]["amount"];
												$balance_value=$pi_amount[$pi_id][$description]["amount"]-$mrr_value_usd;
                                            ?>
                                    </td>
                                    <td width="80" class="wrd_brk" align="right"><? echo $display_font_color.number_format($balance_qnty,2).$font_end; ?></td>
                                    <td width="90" align="right" class="wrd_brk"><? echo $display_font_color.number_format($balance_value,2).$font_end;?></td>                                    
									<?
                                    if($z==1)
                                    {
                                        //$total_mrr_qnty+=$receive_arr[$pi_id]['qnty'];
                                        $total_mrr_val+=$mrr_value_usd;
                                        $total_short_val+=$short_value;
										$total_balance_value+=$balance_value;
										$total_balance_qnty+=$balance_qnty;
                                        $test_data.=$mrr_qnty."__".$i.",";
                                    }
									?>
								</tr>
								<?
							}
							else if($receive_status==2 && ($data[('amount')] > $mrr_value_usd) && $mrr_value_usd>0)
							{
								//echo $data[('amount')]."==".$mrr_value_usd."==".$exchange_rate."++";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" class="wrd_brk" align="center" <? echo $lc_bgcolor; ?>>
										<? echo $i; ?>
									</td>
									<td width="120" class="wrd_brk" <? echo $lc_bgcolor; ?>>
										<p><? echo $data[('pi_number')]; ?></p>
									</td>
									<td width="120" <? echo $lc_bgcolor; ?> class="wrd_brk">
										<p><? echo $btb_lc_array[$pi_id]; ?></p>
									</td>
									<td width="80" class="wrd_brk" align="center">
										<? if($data[('pi_date')]!="" && $data[('pi_date')]!="0000-00-00")   echo change_date_format($data[('pi_date')]); ?>
									</td>
									<td width="80" class="wrd_brk" align="center">
										<? if($data[('last_shipment_date')]!="" && $data[('last_shipment_date')]) echo change_date_format($data[('last_shipment_date')]); ?>
									</td>
									<td width="110" class="wrd_brk"><p><? echo $office_note_data[$pi_id]["CON_SYSTEM_ID"]; ?></p></td>
                                    <td width="100" class="wrd_brk"><p><? echo $section_arr[$office_note_data[$pi_id]["SECTION"]]; ?></p></td>
									<td width="140" class="wrd_brk"><p><? echo $supplier_arr[$data[('supplier_id')]]; ?></p></td>
									<td width="100" class="wrd_brk"><p><? echo $item_category[$data[('item_category_id')]]; ?></p></td>
                                    <td width="110" class="wrd_brk" title="<?= $data[('item_group')];?>"><p><? echo $trim_group_arr[$data[('item_group')]]['name']; ?></p></td>

									<td width="120" class="wrd_brk" align="center">
										<p><? echo $good_description; ?></p>
									</td>
                                    <td width="70" class="wrd_brk" align="center"><p><? echo $data[('hs_code')]; ?></p></td>
									<td width="60" class="wrd_brk" align="center"><p><? echo $unit_of_measurement[$data[('uom')]]; ?></p></td>
									<td width="80" class="wrd_brk" align="right">
										<p>
											<?
												$total_pi_qty+=$data[('quantity')];
												echo number_format($data[('quantity')],2,'.','');
											?>
										</p>
									</td>
									<td width="80" class="wrd_brk" align="right"><p><? echo number_format($item_rate,2,'.',''); ?></p></td>
									<td width="100" class="wrd_brk" align="right">
										<p>
											<?
												$total_pi_val+=$data[('amount')];
												echo number_format($data[('amount')],2,'.','');
											?>
										</p>
									</td>
									<td width="80" class="wrd_brk" align="center"><P><? echo $currency[$data[('currency_id')]]; ?></P></td>

									<td width="80" class="wrd_brk" align="right"><p>
										<?
											echo $print_link;
										?></p>
									</td>
									<td width="80" class="wrd_brk" align="right">
										<?
											//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["supplier_order_quantity"],2);
											echo number_format($supplier_order_quantity,2);
										?>
									</td>
									<td width="80" class="wrd_brk" align="right" title="<? echo $z; ?>">
										<?
											//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["amount"],2);
											echo number_format($wo_amount,2);
										?>
									</td>
                                    <td width="110" class="wrd_brk" align="right" title="<? echo $z; ?>"><? echo $req_no; ?></td>
									<td width="80" class="wrd_brk" align="right" title="<? echo chop($data[('work_order_id')],","); ?>">
										<a href="javascript:void(0)" onClick="show_qty_details('<? echo $pi_wo_id; ?>',<? echo $item_cate; ?>,<? echo $data[('goods_rcv_status')]; ?>,'<? echo $description; ?>')">
											<?
												echo $display_font_color.number_format($mrr_qnty,2).$font_end;
											?>
										</a>
									</td>
                                    <td width="90" class="wrd_brk" align="right">
                                    <?
                                        echo $display_font_color.number_format($mrr_value_usd,2).$font_end;
										$balance_qnty=$pi_quantity[$pi_id][$description]["quantity"]-$mrr_qnty;
										$short_value=$balance_value=0;
										$short_value=$mrr_value_usd-$pi_amount[$pi_id][$description]["amount"];
										$balance_value=$pi_amount[$pi_id][$description]["amount"]-$mrr_value_usd;
                                    ?>
                                    </td>
                                    <td width="80" class="wrd_brk" align="right"><? echo $display_font_color.number_format($balance_qnty,2).$font_end; ?></td>
                                    <td width="90" align="right" class="wrd_brk"><? echo $display_font_color.number_format($balance_value,2).$font_end;?></td>
										<?
										if($z==1)
										{
											//$total_mrr_qnty+=$receive_arr[$pi_id]['qnty'];
											$total_mrr_val+=$mrr_value_usd;
											$total_short_val+=$short_value;
											$total_balance_value+=$balance_value;
											$total_balance_qnty+=$balance_qnty;
											$test_data.=$mrr_qnty."__".$i.",";
										}
											//$z++;
										//}

									?>
								</tr>
								<?
							}
							else if($receive_status==3 && ($data[('amount')] <= $mrr_value_usd))
							{
								//echo $data[('amount')]."==".$mrr_value_usd."==".$exchange_rate."++";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" class="wrd_brk" <? echo $lc_bgcolor; ?>>
										<? echo $i; ?>
									</td>
									<td width="120" class="wrd_brk" <? echo $lc_bgcolor; ?>>
										<p><? echo $data[('pi_number')]; ?></p>
									</td>
									<td width="120" <? echo $lc_bgcolor; ?> class="wrd_brk">
										<p><? echo $btb_lc_array[$pi_id]; ?></p>
									</td>
									<td width="80" class="wrd_brk" align="center">
										<? if($data[('pi_date')]!="" && $data[('pi_date')]!="0000-00-00")   echo change_date_format($data[('pi_date')]); ?>
									</td>
									<td width="80" class="wrd_brk" align="center">
										<? if($data[('last_shipment_date')]!="" && $data[('last_shipment_date')]) echo change_date_format($data[('last_shipment_date')]); ?>
									</td>
									<td width="110" class="wrd_brk"><p><? echo $office_note_data[$pi_id]["CON_SYSTEM_ID"]; ?></p></td>
                                    <td width="100" class="wrd_brk"><p><? echo $section_arr[$office_note_data[$pi_id]["SECTION"]]; ?></p></td>
									<td width="140" class="wrd_brk"><p><? echo $supplier_arr[$data[('supplier_id')]]; ?></p></td>
									<td width="100" class="wrd_brk"><p><? echo $item_category[$data[('item_category_id')]]; ?></p></td>
                                    <td width="110" class="wrd_brk" title="<?= $data[('item_group')];?>"><p><? echo $trim_group_arr[$data[('item_group')]]['name']; ?></p></td>

									<td width="120" class="wrd_brk" align="center">
										<p><? echo $good_description; ?></p>
									</td>
                                    <td width="70" class="wrd_brk" align="center"><p><? echo $data[('hs_code')]; ?></p></td>
									<td width="60" class="wrd_brk" align="center"><p><? echo $unit_of_measurement[$data[('uom')]]; ?></p></td>
									<td width="80" class="wrd_brk" align="right">
										<p>
											<?
												$total_pi_qty+=$data[('quantity')];
												echo number_format($data[('quantity')],2,'.','');
											?>
										</p>
									</td>
									<td width="80" class="wrd_brk" align="right"><p><? echo number_format($item_rate,2,'.',''); ?></p></td>
									<td width="100" class="wrd_brk" align="right">
										<p>
											<?
												$total_pi_val+=$data[('amount')];
												echo number_format($data[('amount')],2,'.','');
											?>
										</p>
									</td>
									<td width="80" class="wrd_brk" align="center"><P><? echo $currency[$data[('currency_id')]]; ?></P></td>

									<td width="80" class="wrd_brk" align="right"><p>
										<?
											echo $print_link;
										?></p>
									</td>
									<td width="80" class="wrd_brk" align="right">
										<?
											//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["supplier_order_quantity"],2);
											echo number_format($supplier_order_quantity,2);
										?>
									</td>
									<td width="80" class="wrd_brk" align="right" title="<? echo $z; ?>">
										<?
											//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["amount"],2);
											echo number_format($wo_amount,2);
										?>
									</td>
                                    <td width="110" class="wrd_brk" align="right" title="<? echo $z; ?>"><? echo $req_no; ?></td>

									<td width="80" class="wrd_brk" align="right" title="<? echo chop($data[('work_order_id')],","); ?>">
										<a href="javascript:void(0)" onClick="show_qty_details('<? echo $pi_wo_id; ?>',<? echo $item_cate; ?>,<? echo $data[('goods_rcv_status')]; ?>,'<? echo $description; ?>')">
											<?

												echo $display_font_color.number_format($mrr_qnty,2).$font_end;
											?>
										</a>
									</td>
									<td width="90" class="wrd_brk" align="right">
                                    <?
                                        echo $display_font_color.number_format($mrr_value_usd,2).$font_end;
										$balance_qnty=$pi_quantity[$pi_id][$description]["quantity"]-$mrr_qnty;
										$short_value=$balance_value=0;
										$short_value=$mrr_value_usd-$pi_amount[$pi_id][$description]["amount"];
										$balance_value=$pi_amount[$pi_id][$description]["amount"]-$mrr_value_usd;
                                    ?>
                                    </td>
                                    <td width="80" class="wrd_brk" align="right"><? echo $display_font_color.number_format($balance_qnty,2).$font_end; ?></td>
                                    <td width="90" class="wrd_brk" align="right"><? echo $display_font_color.number_format($balance_value,2).$font_end;?></td>									
										<?
										if($z==1)
										{
											//$total_mrr_qnty+=$receive_arr[$pi_id]['qnty'];
											$total_mrr_val+=$mrr_value_usd;
											$total_short_val+=$short_value;
											$total_balance_value+=$balance_value;
											$total_balance_qnty+=$balance_qnty;
											$test_data.=$mrr_qnty."__".$i.",";
										}
											//$z++;
										//}

									?>
								</tr>
								<?
							}
							else if($receive_status==4 && ($data[('amount')] > $mrr_value_usd))
							{
								
								//echo $data[('amount')]."==".$mrr_value_usd."==".$exchange_rate."++"; die;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" <? echo $lc_bgcolor; ?> class="wrd_brk">
										<? echo $i; ?>
									</td>
									<td width="120" <? echo $lc_bgcolor; ?> class="wrd_brk">
										<p><? echo $data[('pi_number')]; ?></p>
									</td>
									<td width="120" <? echo $lc_bgcolor; ?> class="wrd_brk">
										<p><? echo $btb_lc_array[$pi_id]; ?></p>
									</td>
									<td width="80" align="center" class="wrd_brk">
										<? if($data[('pi_date')]!="" && $data[('pi_date')]!="0000-00-00")   echo change_date_format($data[('pi_date')]); ?>
									</td>
									<td width="80" align="center" class="wrd_brk">
										<? if($data[('last_shipment_date')]!="" && $data[('last_shipment_date')]) echo change_date_format($data[('last_shipment_date')]); ?>
									</td>
									<td width="110" class="wrd_brk"><p><? echo $office_note_data[$pi_id]["CON_SYSTEM_ID"]; ?></p></td>
                                    <td width="100" class="wrd_brk"><p><? echo $section_arr[$office_note_data[$pi_id]["SECTION"]]; ?></p></td>
									<td width="140" class="wrd_brk"><p><? echo $supplier_arr[$data[('supplier_id')]]; ?></p></td>
									<td width="100" class="wrd_brk"><p><? echo $item_category[$data[('item_category_id')]]; ?></p></td>
                                    <td width="110" class="wrd_brk" title="<?= $data[('item_group')];?>"><p><? echo $trim_group_arr[$data[('item_group')]]['name']; ?></p></td>

									<td width="120" class="wrd_brk" align="center">
										<p><? echo $good_description; ?></p>
									</td>
                                    <td width="70" class="wrd_brk" align="center"><p><? echo $data[('hs_code')]; ?></p></td>
									<td width="60" class="wrd_brk" align="center"><p><? echo $unit_of_measurement[$data[('uom')]]; ?></p></td>
									<td width="80" class="wrd_brk" align="right">
										<p>
											<?
												$total_pi_qty+=$data[('quantity')];
												echo number_format($data[('quantity')],2,'.','');
											?>
										</p>
									</td>
									<td width="80" class="wrd_brk" align="right"><p><? echo number_format($item_rate,2,'.',''); ?></p></td>
									<td width="100" class="wrd_brk" align="right">
										<p>
											<?
												$total_pi_val+=$data[('amount')];
												echo number_format($data[('amount')],2,'.','');
											?>
										</p>
									</td>
									<td width="80" class="wrd_brk" align="center"><P><? echo $currency[$data[('currency_id')]]; ?></P></td>

									<td width="80" class="wrd_brk" align="right"><p>
										<?
											echo $print_link;
										?></p>
									</td>
									<td width="80" class="wrd_brk" align="right">
										<?
											//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["supplier_order_quantity"],2);
											echo number_format($supplier_order_quantity,2);
										?>
									</td>
									<td width="80" class="wrd_brk" align="right" title="<? echo $z; ?>">
										<?
											//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["amount"],2);
											echo number_format($wo_amount,2);
										?>
									</td>
                                    <td width="110" class="wrd_brk" align="right" title="<? echo $z; ?>"><? echo $req_no; ?></td>

									<?
										/*if($z==1)
										{*/
									?>

											<td width="80" class="wrd_brk" align="right" title="<? echo chop($data[('work_order_id')],","); ?>">
												<a href="javascript:void(0)" onClick="show_qty_details('<? echo $pi_wo_id; ?>',<? echo $item_cate; ?>,<? echo $data[('goods_rcv_status')]; ?>,'<? echo $description; ?>')">
													<?

														echo $display_font_color.number_format($mrr_qnty,2).$font_end;
													?>
												</a>
											</td>
											<td width="90" class="wrd_brk" align="right">
                                            <?

                                                echo $display_font_color.number_format($mrr_value_usd,2).$font_end;
												$balance_qnty=$pi_quantity[$pi_id][$description]["quantity"]-$mrr_qnty;
												$short_value=$balance_value=0;
												$short_value=$mrr_value_usd-$pi_amount[$pi_id][$description]["amount"];
												$balance_value=$pi_amount[$pi_id][$description]["amount"]-$mrr_value_usd;
                                            ?>
                                            </td>
                                            <td width="80" class="wrd_brk" align="right"><? echo $display_font_color.number_format($balance_qnty,2).$font_end; ?></td>
                                            <td align="right"><? echo $display_font_color.number_format($balance_value,2).$font_end;?></td>
										<?
										if($z==1)
										{
											//$total_mrr_qnty+=$receive_arr[$pi_id]['qnty'];
											$total_mrr_val+=$mrr_value_usd;
											$total_short_val+=$short_value;
											$total_balance_value+=$balance_value;
											$total_balance_qnty+=$balance_qnty;
											$test_data.=$mrr_qnty."__".$i.",";
										}
											//$z++;
										//}

									?>
								</tr>
								<?
							}
							else if($receive_status==5)
							{
								//echo 'system';
								//echo $data[('amount')]."==".$mrr_value_usd."==".$exchange_rate."++";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" <? echo $lc_bgcolor; ?> class="wrd_brk"><? echo $i; ?></td>
									<td width="120" <? echo $lc_bgcolor; ?> class="wrd_brk"><p><? echo $data[('pi_number')]; ?></p></td>
									<td width="120" <? echo $lc_bgcolor; ?> class="wrd_brk"><p><? echo $btb_lc_array[$pi_id]; ?></p></td>
									<td width="80" align="center" class="wrd_brk"><? if($data[('pi_date')]!="" && $data[('pi_date')]!="0000-00-00")   echo change_date_format($data[('pi_date')]); ?></td>
									<td width="80" align="center">
										<? if($data[('last_shipment_date')]!="" && $data[('last_shipment_date')]) echo change_date_format($data[('last_shipment_date')]); ?>
									</td>
									<td width="110" class="wrd_brk"><p><? echo $office_note_data[$pi_id]["CON_SYSTEM_ID"]; ?></p></td>
                                    <td width="100" class="wrd_brk"><p><? echo $section_arr[$office_note_data[$pi_id]["SECTION"]]; ?></p></td>
									<td width="140" class="wrd_brk"><p><? echo $supplier_arr[$data[('supplier_id')]]; ?></p></td>
									<td width="100" class="wrd_brk"><p><? echo $item_category[$data[('item_category_id')]]; ?></p></td>
                                    <td width="110" class="wrd_brk" title="<?= $data[('item_group')];?>"><p><? echo $trim_group_arr[$data[('item_group')]]['name']; ?></p></td>
									<td width="120" align="center" class="wrd_brk">
										<p><? echo $good_description; ?></p>
									</td>
                                    <td width="70" align="center" class="wrd_brk"><p><? echo $data[('hs_code')]; ?></p></td>
									<td width="60" align="center" class="wrd_brk"><p><? echo $unit_of_measurement[$data[('uom')]]; ?></p></td>
									<td width="80" align="right" class="wrd_brk">
										<p>
											<?
												$total_pi_qty+=$data[('quantity')];
												echo number_format($data[('quantity')],2,'.','');
											?>
										</p>
									</td>
									<td width="80" align="right" class="wrd_brk"><p><? echo number_format($item_rate,2,'.',''); ?></p></td>
									<td width="100" align="right" class="wrd_brk">
										<p>
											<?
												$total_pi_val+=$data[('amount')];
												echo number_format($data[('amount')],2,'.','');
											?>
										</p>
									</td>
									<td width="80" align="center" class="wrd_brk"><P><? echo $currency[$data[('currency_id')]]; ?></P></td>
									<td width="80" align="right"><p>
										<?
											//echo $all_wo;
										echo $print_link;
										?></p>
									</td>
									<td width="80" align="right" class="wrd_brk"><p>
										<?
											//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["supplier_order_quantity"],2);
											echo number_format($supplier_order_quantity,2);
										?></p>
									</td>
									<td width="80" align="right" class="wrd_brk" title="<? echo $z; ?>"><p>
										<?
											//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["amount"],2);
											echo number_format($wo_amount,2);
										?></p>
									</td>
                                    <td width="110" align="right" class="wrd_brk" title="<? echo $z; ?>"><p><? echo $req_no; ?></p></td>
									<td width="80" align="right" class="wrd_brk" title="<? echo chop($data[('work_order_id')],","); ?>"><p>
										<a href="javascript:void(0)" onClick="show_qty_details('<? echo $pi_wo_id; ?>',<? echo $item_cate; ?>,<? echo $data[('goods_rcv_status')]; ?>,'<? echo $description; ?>')">
											<?
												echo  $display_font_color.number_format($mrr_qnty,2).$font_end;
											?>
										</a></p>
									</td>
									<td width="90" align="right" class="wrd_brk"><p>
                                    <?
                                        echo $display_font_color.number_format($mrr_value_usd,2).$font_end;
										$balance_qnty=$pi_quantity[$pi_id][$description]["quantity"]-$mrr_qnty;
										$short_value=$balance_value=0;
										if($data[('item_category_id')]==2)
										{
											$short_value=$data[('amount')]-$mrr_value_usd;
										}
										else
										{
											$short_value=$mrr_value_usd-$pi_amount[$pi_id][$description]["amount"];
										}
										$balance_value=$pi_amount[$pi_id][$description]["amount"]-$mrr_value_usd;
                                    ?></p>
                                    </td>
                                    <td width="80" align="right" class="wrd_brk"><p><? echo $display_font_color.number_format($balance_qnty,2).$font_end; ?></p></td>
                                    <td align="right" class="wrd_brk"><p><? echo $display_font_color.number_format($balance_value,2).$font_end;?></p></td>
										<?
										if($z==1)
										{
											//$total_mrr_qnty+=$receive_arr[$pi_id]['qnty'];
											$total_mrr_val+=$mrr_value_usd;
											$total_short_val+=$short_value;
											$total_balance_value+=$balance_value;
											$total_balance_qnty+=$balance_qnty;
											$test_data.=$mrr_qnty."__".$i.",";
										}
											//$z++;
										//}

									?>
								</tr>
								<?
							}
							else{

							}
							$i++;
							$z++;
						}
					}
					?>
                </table>
                </div>
                <table width="<? echo $table_width; ?>" class="tbl_bottom" cellspacing="0" cellpadding="0" border="1" rules="all">
	                <tr>
	                    <td width="40">&nbsp;</td>
	                    <td width="120">&nbsp;</td>
	                    <td width="120">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
                        <td width="110">&nbsp;</td>
                        <td width="100">&nbsp;</td>
	                    <td width="140">&nbsp;</td>
	                    <td width="100">&nbsp;</td>
                        <td width="110">&nbsp;</td>
	                    <td width="120">&nbsp;</td>
                        <td width="70">&nbsp;</td>
	                    <td width="60">Total</td>
	                    <td width="80" id="value_td_pi_qty"><? echo number_format($total_pi_qty,2);?></td>
	                    <td width="80"><? //echo $total_pi_val;?></td>
	                    <td width="100" id="value_td_pi_val"><? echo number_format($total_pi_val,2);?></td>
	                    <td width="80">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
                        <td width="110">&nbsp;</td>
	                    <td width="80" id="value_td_mrr_qnty"><? echo number_format($total_mrr_qnty,2);?></td>
	                    <td width="90" id="value_td_mrr_val"><? echo number_format($total_mrr_val,2);?></td>
                        <td width="80" id="value_td_balance_qnty"><? echo number_format($total_balance_qnty,2);?></td>
	                    <td id="value_td_balance_val"><? echo number_format($total_balance_value,2);?></td>	                   
	                </tr>
			</table>

	    </fieldset>
	    </div>
		<?

	}
	else if (str_replace("'","",$type)==3)  //Trims Recap
	{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process ));

		$item_cate=str_replace("'","",$cbo_item_category_id);
		$cbo_date_type=str_replace("'","",$cbo_date_type);
		if($pi_no != ""){
			$pi_sql_cond = " and a.pi_number='$pi_no'";
		}
		if(str_replace("'","",$cbo_issuing_bank)==0) $issuing_bank="%%"; else $issuing_bank=str_replace("'","",$cbo_issuing_bank);
		if(str_replace("'","",$cbo_lc_type_id)==0) $lc_type_id="%%"; else $lc_type_id=str_replace("'","",$cbo_lc_type_id);

		$recvData="select a.pi_wo_batch_no, a.receive_basis, c.item_group_id, c.item_description, c.rate, a.order_qnty as qnty, a.order_amount as amnt from inv_transaction a, inv_trims_entry_dtls c  
		where a.id=c.trans_id and a.receive_basis in(1,2) and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name and a.item_category=$cbo_item_category_id";
		$recvDataArray=sql_select($recvData);
		$receive_arr = array();
		foreach($recvDataArray as $row)
		{
			$rate=number_format($row[csf('rate')],6);
			if($row[csf('receive_basis')]==1)
			{
				$receive_arr[$row[csf('pi_wo_batch_no')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$rate]['qnty'] += $row[csf('qnty')];
				$receive_arr[$row[csf('pi_wo_batch_no')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$rate]['amnt'] += $row[csf('amnt')];
			}
			else
			{
				$AfterRecv_arr[$row[csf('pi_wo_batch_no')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$rate]['qnty'] += $row[csf('qnty')];
				$AfterRecv_arr[$row[csf('pi_wo_batch_no')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$rate]['amnt'] += $row[csf('amnt')];
			}
		}
	

		$lc_sc_sql=sql_select("select id, export_lc_no as lc_sc_no, lc_date as lc_sc_date, 0 as type from com_export_lc where beneficiary_name=$cbo_company_name
			union all
			select id, contract_no as lc_sc_no, contract_date as lc_sc_date, 1 as type from com_sales_contract where beneficiary_name=$cbo_company_name");

		$lc_sc_data = array();
		foreach($lc_sc_sql as $row)
		{
			$lc_sc_data[$row[csf('id')]][$row[csf('type')]]['lc_sc_no'] = $row[csf('lc_sc_no')];
			$lc_sc_data[$row[csf('id')]][$row[csf('type')]]['lc_sc_date'] = $row[csf('lc_sc_date')];
		}
		$trim_group_arr =array(); 
		$data_array=sql_select("select id, item_name, order_uom from lib_item_group");
		foreach($data_array as $row)
		{
			$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
			$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('order_uom')];
		}

		$print_report_format=sql_select("select format_id, report_id from lib_report_template where template_name=".$cbo_company_name." and module_id in(2) and report_id in(26) and status_active=1 and is_deleted=0");		
		$print_report_format_exp=explode(',',$print_report_format[0][csf('format_id')]);
		ob_start();
		$table_width=1700;
		?>
	    <div style="width:<? echo $table_width+30; ?>px; margin-left:10px">
	        <fieldset style="width:100%;">
	            <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption">
	                <tr>
	                   <td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
	                </tr>
	                <tr>
	                   <td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
	                </tr>
	            </table>
	            <br />
	            <table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
	                <thead>
	                    <tr>
	                        <th colspan="13" >PI Details</th>
	                        <th colspan="3">PO/MPO Information</th>
	                        <th colspan="3">Matarials Received Information</th>
	                    </tr>
	                    <tr>
	                        <th width="40">SL</th>
	                        <th width="120">PI No</th>
	                        <th width="80">PI Date</th>
	                        <th width="80">Last Ship Date</th>
	                        <th width="140">Supplier Name</th>
	                        <th width="100">Item Category</th>
                            <th width="100">Item Group</th>
	                        <th width="120">Goods Description</th>
	                        <th width="60">UOM</th>
	                        <th width="80">PI Quantity</th>
	                        <th width="80">Unit Price</th>
	                        <th width="100">PI Value</th>
	                        <th width="80">Currency</th>

	                        <th width="80">PO/MPO No.</th>
	                        <th width="80">PO/MPO Qty.</th>
	                        <th width="80">PO/MPO Value</th>

	                        <th width="80">MRR Qnty</th>
	                        <th width="90">MRR Value</th>
	                        <th>Short Value</th>
	                    </tr>
	                </thead>
	            </table>

	            <div style="width:<? echo $table_width+20; ?>px; max-height:300px; overflow-y:scroll" id="scroll_body">
			 			<table width="<? echo $table_width; ?>" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_body">
	                <?
					if($cbo_date_type==1) //Pi Date
					{
						$pi_date_cond="";
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") $pi_date_cond=" and a.pi_date between $txt_date_from and $txt_date_to";
					}
					else if($cbo_date_type==2) //Pi Insert Date
					{
						$pi_date_cond="";
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
						{
							if($db_type==0)
							{
								$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
								$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
								$pi_date_cond=" and a.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
							}
							else if($db_type==2)
							{
								$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
								$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
								$pi_date_cond=" and a.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
							}
						}
					}
					else if($cbo_date_type==3) //BTB Date
					{
						$btb_date_cond="";
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") $btb_date_cond=" and a.lc_date between $txt_date_from and $txt_date_to";
					}
					else if($cbo_date_type==4) // BTB Insert Date
					{
						$btb_date_cond="";
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
						{
							if($db_type==0)
							{
								$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
								$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
								$btb_date_cond=" and a.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
							}
							else if($db_type==2)
							{
								$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
								$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
								$btb_date_cond=" and a.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
							}
						}
					}
					else if($cbo_date_type==5) //Maturity Date
					{
						$mat_str_cond="";
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") $mat_str_cond=" and a.maturity_date between $txt_date_from and $txt_date_to";
					}
					else if($cbo_date_type==6) //Maturity Insert Date
					{
						$mat_str_cond="";
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
						{
							if($db_type==0)
							{
								$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
								$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
								$mat_str_cond=" and a.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
							}
							else if($db_type==2)
							{
								$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
								$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
								$mat_str_cond=" and a.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
							}
						}
					}

					$all_pi_ids="";
					if($cbo_date_type==5 || $cbo_date_type==6)
					{
						$pi_id_arr=array();
						$invoice_sql =sql_select("select b.pi_id from com_import_invoice_mst a, com_import_invoice_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $mat_str_cond");
						foreach ($invoice_sql as $value)
						{
							$pi_id_arr[$value[csf("pi_id")]]=$value[csf("pi_id")];
						}
						$all_pi_ids=implode(",", $pi_id_arr);
					}
					
					if($cbo_date_type==3 || $cbo_date_type==4)
					{
						$ref_close_btb="";
						if($receive_status==4) $ref_close_btb=" and a.ref_closing_status<>1";
						if($addStringMulti!=""){ $import_source_cond = " and a.lc_category in($addStringMulti)";}else{ $import_source_cond = "";}
						if($db_type==0)
						{
							if($entry_form == 167)
							{
								if($item_cate>0){$item_cate_cond="and q.item_category_id=$item_cate";}else{$item_cate_cond='';}
								$sql="select a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id, group_concat(b.lc_sc_id) as lc_sc_id , group_concat(b.is_lc_sc) as is_lc_sc
								from com_btb_lc_master_details a left join com_btb_export_lc_attachment b on a.id=b.import_mst_id and b.is_deleted=0 and b.status_active=1
								where a.importer_id=$cbo_company_name and a.pi_entry_form=$entry_form and a.lc_type_id like '$lc_type_id' and a.issuing_bank_id like '$issuing_bank' and a.is_deleted=0 and a.status_active=1 $ref_close_btb $btb_date_cond $import_source_cond  group by a.id
								union all
								select a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id, group_concat(b.lc_sc_id) as lc_sc_id , group_concat(b.is_lc_sc) as is_lc_sc
								from wo_non_order_info_mst p, com_pi_item_details q, com_btb_lc_pi r, com_btb_lc_master_details a left join com_btb_export_lc_attachment b on a.id=b.import_mst_id and b.is_deleted=0 and b.status_active=1
								where p.id=q.work_order_id and q.pi_id=r.pi_id and r.com_btb_lc_master_details_id=a.id and a.importer_id=$cbo_company_name and p.item_category=4 and a.lc_type_id like '$lc_type_id' $item_cate_cond and a.issuing_bank_id like '$issuing_bank'  $ref_close_btb and a.is_deleted=0 and a.status_active=1 $btb_date_cond $import_source_cond  group by a.id";
							}
						}
						else if($db_type==2)
						{
							if($entry_form == 167)
							{
								if($item_cate>0){$item_cate_cond="and q.item_category_id=$item_cate";}else{$item_cate_cond='';}
								 $sql="select a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id, LISTAGG(CAST(b.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.lc_sc_id) as lc_sc_id , LISTAGG(CAST(b.is_lc_sc AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.is_lc_sc) as is_lc_sc, 1 as type
								from com_btb_lc_master_details a left join com_btb_export_lc_attachment b on a.id=b.import_mst_id and b.is_deleted=0 and b.status_active=1
								where a.importer_id=$cbo_company_name and a.pi_entry_form=$entry_form and a.lc_type_id like '$lc_type_id' and a.issuing_bank_id like '$issuing_bank'  $ref_close_btb and a.is_deleted=0 and a.status_active=1 $btb_date_cond $import_source_cond
								group by a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id
								union all
								select a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id, LISTAGG(CAST(b.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.lc_sc_id) as lc_sc_id , LISTAGG(CAST(b.is_lc_sc AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.is_lc_sc) as is_lc_sc, 2 as type
								from wo_non_order_info_mst p, com_pi_item_details q, com_btb_lc_pi r, com_btb_lc_master_details a left join com_btb_export_lc_attachment b on a.id=b.import_mst_id and b.is_deleted=0 and b.status_active=1
								where p.id=q.work_order_id and q.pi_id=r.pi_id and r.com_btb_lc_master_details_id=a.id and a.importer_id=$cbo_company_name $item_cate_cond and a.lc_type_id like '$lc_type_id' and a.issuing_bank_id like '$issuing_bank' $ref_close_btb and a.is_deleted=0 and a.status_active=1 $btb_date_cond $import_source_cond
								group by a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, a.lc_category, a.currency_id, a.pi_id";
							}
						}
						$pi_ids="";
						foreach (sql_select($sql) as $value)
						{
							$pi_ids .=$value[csf("pi_id")].",";
						}
						$pi_ids=implode(",", array_filter(array_unique(explode(",", chop($pi_ids, ",")))));
					}
					
					//echo $sql; die;
					
					//echo "mahbub"; die;

					if ($all_pi_ids !="")
					{
						$pi_ids=$pi_ids.",".$all_pi_ids;
						$pi_ids=implode(",", array_filter(array_unique(explode(",", $pi_ids))));
					}

					$all_pi_cond="";
					if($pi_ids)
					{
						$all_pi_cond="and a.id in (".trim($pi_ids,',').")";
					}

					$ref_close_pi="";
					if($receive_status==4) $ref_close_pi=" and a.ref_closing_status<>1";
					if($entry_form == 167)
					{
						if($item_cate>0){$item_cate_cond="and b.item_category_id=$item_cate";}else{$item_cate_cond='';}
						$sql_pi="SELECT a.id, a.item_category_id, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, a.intendor_name, a.goods_rcv_status, b.count_name, b.yarn_type, b.uom, b.quantity, b.rate, b.amount, b.net_pi_rate, b.net_pi_amount, b.yarn_composition_item1, b.yarn_composition_percentage1, b.fabric_composition, b.fabric_construction, b.item_group,b.item_description, 1 as type, b.work_order_no, b.work_order_id, b.work_order_dtls_id, b.item_prod_id
						from com_pi_master_details a, com_pi_item_details b
						where a.id=b.pi_id and b.is_deleted=0 and b.status_active=1 and a.entry_form=$entry_form and a.importer_id=$cbo_company_name $ref_close_pi $item_cate_cond $pi_date_cond $all_pi_cond $pi_sql_cond
						union all
						SELECT a.id, c.item_category as item_category_id, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, a.intendor_name, a.goods_rcv_status, b.count_name, b.yarn_type, b.uom, b.quantity, b.rate, b.amount, b.net_pi_rate, b.net_pi_amount, b.yarn_composition_item1, b.yarn_composition_percentage1, b.fabric_composition, b.fabric_construction, b.item_group,b.item_description, 2 as type, b.work_order_no, b.work_order_id, b.work_order_dtls_id, b.item_prod_id
						from com_pi_master_details a, com_pi_item_details b,  wo_non_order_info_mst c
						where a.id=b.pi_id and b.work_order_id=c.id and b.is_deleted=0 and b.status_active=1 and c.item_category=4 and a.entry_form=$entry_form and a.importer_id=$cbo_company_name $ref_close_pi $item_cate_cond  $pi_date_cond $all_pi_cond $pi_sql_cond
						order by pi_date desc";
					}
					//echo $sql_pi;die;

					$sql_pi_result=sql_select($sql_pi);
					foreach($sql_pi_result as $row)
					{
						$description=$itemgroup=$piRate="";
						$description=trim($row[csf("item_description")]);
						$itemgroup=$row[csf("item_group")];
						$piRate=number_format($row[csf("rate")],6);
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["id"]=$row[csf("id")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["item_category_id"]=$row[csf("item_category_id")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["supplier_id"]=$row[csf("supplier_id")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["pi_number"]=$row[csf("pi_number")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["pi_date"]=$row[csf("pi_date")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["goods_rcv_status"]=$row[csf("goods_rcv_status")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["last_shipment_date"]=$row[csf("last_shipment_date")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["currency_id"]=$row[csf("currency_id")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["intendor_name"]=$row[csf("intendor_name")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["work_order_no"].=$row[csf("work_order_no")].",";
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["item_prod_id"]=$row[csf("item_prod_id")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["count_name"]=$row[csf("count_name")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["yarn_type"]=$row[csf("yarn_type")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["uom"]=$row[csf("uom")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["quantity"]+=$row[csf("quantity")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["rate"]=$row[csf("rate")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["amount"]+=$row[csf("amount")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["net_pi_rate"]=$row[csf("net_pi_rate")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["net_pi_amount"]+=$row[csf("net_pi_amount")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["item_group"]=$row[csf("item_group")];
						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["item_description"]=$description;

						$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["work_order_dtls_id"]=$row[csf("work_order_dtls_id")];
						
						if($duplicate_wo_check[$row[csf("work_order_id")]][$row[csf("id")]][$itemgroup][$description][$piRate]=="")
						{
							$duplicate_wo_check[$row[csf("work_order_id")]][$row[csf("id")]][$itemgroup][$description][$piRate]=$row[csf("work_order_id")];
							$pi_data_arr[$row[csf("id")]][$itemgroup][$description][$piRate]["work_order_id"].=$row[csf("work_order_id")].",";
						}

						/*$work_order_arr[$row[csf("id")]]["work_order_no"] .= $row[csf("work_order_no")].",";
						$work_order_arr[$row[csf("id")]]["work_order_dtls_id"] .= $row[csf("work_order_dtls_id")].",";*/

						$work_order .= $row[csf("work_order_no")].",";
						$work_order_dtls .= $row[csf("work_order_dtls_id")].",";
						$pi_amount[$row[csf("id")]][$itemgroup][$description][$piRate]["amount"]+=$row[csf("amount")];

					}
					//echo "<pre>";
					//print_r($pi_data_arr);

					$wo_num = "'".implode("','", array_unique(explode(",", chop($work_order, ","))))."'";
					$dtls_id = implode(",", array_unique(explode(",", chop($work_order_dtls, ","))));

					$sql_wo="SELECT a.booking_no as wo_number, a.booking_no_prefix_num as wo_number_prefix_num, a.is_approved, a.cbo_level, c.cons as supplier_order_quantity, c.amount, b.id as dtls_id, 0 as item_id, c.description, c.rate, 1 as type, b.trim_group 
					from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c  
					where a.booking_no=b.booking_no and a.booking_no=c.booking_no  and b.id=c.wo_trim_booking_dtls_id  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.booking_type=2  and b.booking_type=2  and a.item_category=4 and a.booking_no in ($wo_num) ";
					$sql_wo_res=sql_select($sql_wo);

					foreach ($sql_wo_res as $val)
					{
						$descript=$trimgroup=$bookRate="";
					    $descript=trim($val[csf("description")]);
						$trimgroup=$val[csf("trim_group")];
						$bookRate=number_format($val[csf("rate")],6);

						$aftergoods_wo_data_arr[$val[csf("wo_number")]][$trimgroup][$descript][$bookRate]["wo_number_prefix_num"].=$val[csf("wo_number_prefix_num")].",";
						$aftergoods_wo_data_arr[$val[csf("wo_number")]][$trimgroup][$descript][$bookRate]["wo_number"].=$val[csf("wo_number")].",";
						$aftergoods_wo_data_arr[$val[csf("wo_number")]][$trimgroup][$descript][$bookRate]["is_approved"].=$val[csf("is_approved")].",";
						$aftergoods_wo_data_arr[$val[csf("wo_number")]][$trimgroup][$descript][$bookRate]["cbo_level"].=$val[csf("cbo_level")].",";
						$aftergoods_wo_data_arr[$val[csf("wo_number")]][$trimgroup][$descript][$bookRate]["supplier_order_quantity"]+=$val[csf("supplier_order_quantity")];
						$aftergoods_wo_data_arr[$val[csf("wo_number")]][$trimgroup][$descript][$bookRate]["amount"]+=$val[csf("amount")];
					}

					//echo "<pre>";print_r($pi_data_arr); die;

					$i=1; $total_pi_qty=0;$total_pi_val=0; $total_short_val=0;$total_mrr_qnty=0; $total_wo_amount=0;
					foreach ($pi_data_arr as $pi_id => $value)
					{
						
						//$rowspan=count($value);
						//$z=1;
						foreach ($value as $itemgroup_id => $itemgroup_data)
						{
							foreach ($itemgroup_data as $description => $rate_data)
							{
								foreach ($rate_data as $rate => $data)
								{
							
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									$wo_dtls_id=$data[('work_order_dtls_id')];
									$des_ref=explode('*',$description);
									
									$item_rate=$data[('amount')]/$data[('quantity')];//work_order_no
									$mrr_qnty=0; $mrr_value=0; $mrr_value_usd=0; $supplier_order_quantity=$all_wo=$wo_amount="";
									//echo $pi_wo_id.'**';
									//echo $data[('item_group')]."kkkkkkkk".$data[('item_description')]."kkkkkkkk".$rate."mahbub";
									if($data[('goods_rcv_status')]==1)
									{
										$rcv_basis=2;
										$pi_wo_id=chop($data[('work_order_id')],",");
										$all_wo_id_arr=explode(",",chop($data[('work_order_id')],","));
										$wo_id_test= $item_group_test=$item_des_test="";
										foreach($all_wo_id_arr as $wo_id)
										{
											$mrr_qnty+=$AfterRecv_arr[$wo_id][$data[('item_group')]][$data[('item_description')]][$rate]['qnty'];
											//echo $AfterRecv_arr[$wo_id]['amnt']."=".$AfterRecv_arr[$wo_id]['exchange_rate'];die;
											$wo_id_test.=$wo_id.",";
											$item_group_test.=$data[('item_group')].",";
											$item_des_test.=$data[('item_description')].",";
											
											if($AfterRecv_arr[$wo_id][$data[('item_group')]][$data[('item_description')]][$rate]['amnt']!=0 && $AfterRecv_arr[$wo_id][$data[('item_group')]][$data[('item_description')]][$rate]['amnt'] !='') $mrr_value_usd+=$AfterRecv_arr[$wo_id][$data[('item_group')]][$data[('item_description')]][$rate]['amnt'];
										}
										$supplier_order_quantity=''; 
										if($data[('item_category_id')]==4)
										{
											$all_work_order_arr=array_unique(explode(",",chop($data[('work_order_no')],",")));
											//echo "<pre>";print_r($all_work_order_arr); 
											$print_link='';
											foreach($all_work_order_arr as $wo_no)
											{
												$all_wo= implode(',', array_unique(explode(',', chop($aftergoods_wo_data_arr[$wo_no][$data[('item_group')]][$description][$rate]["wo_number_prefix_num"],','))));
												$wo_number= implode(',', array_unique(explode(',', chop($aftergoods_wo_data_arr[$wo_no][$data[('item_group')]][$description][$rate]["wo_number"],','))));
												$is_approved = implode(',', array_unique(explode(',', chop($aftergoods_wo_data_arr[$wo_no][$data[('item_group')]][$description][$rate]["is_approved"],','))));
												$cbo_level = implode(',', array_unique(explode(',', chop($aftergoods_wo_data_arr[$wo_no][$data[('item_group')]][$description][$rate]["cbo_level"],','))));
												$supplier_order_quantity +=$aftergoods_wo_data_arr[$wo_no][$data[('item_group')]][$description][$rate]["supplier_order_quantity"];
												$wo_amount+=$aftergoods_wo_data_arr[$wo_no][$data[('item_group')]][$description][$rate]["amount"];


												if ($print_report_format_exp[0]==67)  //Print Booking
												{	
													if(!empty($print_link))	
													{
														$print_link.=",<a href='#' onClick=\"generate_trim_report('show_trim_booking_report','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
													}
													else
													{
														$print_link="<a href='#' onClick=\"generate_trim_report('show_trim_booking_report','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
													}							
													
												}
												else if ($print_report_format_exp[0]==183)  //Print Report 2
												{	
													if(!empty($print_link))	
													{
														$print_link.=",<a href='#' onClick=\"generate_trim_report('show_trim_booking_report2','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
													}
													else
													{
														$print_link="<a href='#' onClick=\"generate_trim_report('show_trim_booking_report2','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
													}								
													
												}
												else if ($print_report_format_exp[0]==177)  //Print Report 4
												{	
													if(!empty($print_link))	
													{
														$print_link.=",<a href='#' onClick=\"generate_trim_report('show_trim_booking_report4','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
													}
													else
													{
														$print_link="<a href='#' onClick=\"generate_trim_report('show_trim_booking_report4','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
													}								
													
												}
												else if ($print_report_format_exp[0]==235)  //Print Booking9
												{		
													if(!empty($print_link))	
													{
														$print_link.=",<a href='#' onClick=\"generate_trim_report('show_trim_booking_report5','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
													}
													else
													{
														$print_link="<a href='#' onClick=\"generate_trim_report('show_trim_booking_report5','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
													}							
													
												}																				
												else $print_link=$all_wo;
											}
										}		
									}
									else
									{
										$rcv_basis=1;
										$pi_wo_id=$pi_id;
										$mrr_qnty=$receive_arr[$pi_id][$data[('item_group')]][$data[('item_description')]][$rate]['qnty'];
										$mrr_value=$receive_arr[$pi_id][$data[('item_group')]][$data[('item_description')]][$rate]['amnt'];
										//$exchange_rate=$receive_arr[$pi_id][$data[('item_group')]][$data[('item_description')]][$rate]['exchange_rate'];
										if($mrr_value!=0 && $mrr_value !='')
										{
											$mrr_value_usd=$mrr_value;
										}
										$supplier_order_quantity ='';
										
										$all_work_order_arr=array_unique(explode(",",chop($data[('work_order_no')],",")));
										//echo "<pre>";print_r($all_work_order_arr); 
										$print_link='';
										foreach($all_work_order_arr as $wo_no)
										{									
											$all_wo= implode(',', array_unique(explode(',', chop($aftergoods_wo_data_arr[$wo_no][$data[('item_group')]][$description][$rate]["wo_number_prefix_num"],','))));
											$wo_number= implode(',', array_unique(explode(',', chop($aftergoods_wo_data_arr[$wo_no][$data[('item_group')]][$description][$rate]["wo_number"],','))));
											$is_approved = implode(',', array_unique(explode(',', chop($aftergoods_wo_data_arr[$wo_no][$data[('item_group')]][$description][$rate]["is_approved"],','))));
											$cbo_level = implode(',', array_unique(explode(',', chop($aftergoods_wo_data_arr[$wo_no][$data[('item_group')]][$description][$rate]["cbo_level"],','))));
											$supplier_order_quantity += $aftergoods_wo_data_arr[$wo_no][$data[('item_group')]][$description][$rate]["supplier_order_quantity"];
											$wo_amount+=$aftergoods_wo_data_arr[$wo_no][$data[('item_group')]][$description][$rate]["amount"];



											if ($print_report_format_exp[0]==67)  //Print Booking
											{	
												if(!empty($print_link))	
												{
													$print_link.=",<a href='#' onClick=\"generate_trim_report('show_trim_booking_report','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
												}
												else
												{
													$print_link="<a href='#' onClick=\"generate_trim_report('show_trim_booking_report','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
												}							
												
											}
											else if ($print_report_format_exp[0]==183)  //Print Report 2
											{	
												if(!empty($print_link))	
												{
													$print_link.=",<a href='#' onClick=\"generate_trim_report('show_trim_booking_report2','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
												}
												else
												{
													$print_link="<a href='#' onClick=\"generate_trim_report('show_trim_booking_report2','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
												}								
												
											}
											else if ($print_report_format_exp[0]==177)  //Print Report 4
											{	
												if(!empty($print_link))	
												{
													$print_link.=",<a href='#' onClick=\"generate_trim_report('show_trim_booking_report4','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
												}
												else
												{
													$print_link="<a href='#' onClick=\"generate_trim_report('show_trim_booking_report4','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
												}								
												
											}
											else if ($print_report_format_exp[0]==235)  //Print Booking9
											{		
												if(!empty($print_link))	
												{
													$print_link.=",<a href='#' onClick=\"generate_trim_report('show_trim_booking_report5','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
												}
												else
												{
													$print_link="<a href='#' onClick=\"generate_trim_report('show_trim_booking_report5','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
												}							
												
											}																				
											else $print_link=$all_wo;
										}
										
										//$mrr_value_usd=$mrr_value/$exchange_rate;
										//$all_wo = implode(',', array_unique(explode(',', chop($wo_data_arr[$wo_dtls_id]["wo_number_prefix_num"],','))));
										//$supplier_order_quantity=$wo_data_arr[$wo_dtls_id]["supplier_order_quantity"];
										//$wo_amount=$wo_data_arr[$wo_dtls_id]["amount"];
										
										/*$all_wo = implode(',', array_unique(explode(',', chop($aftergoods_wo_data_arr[$data[('work_order_no')]][$data[('item_group')]][$description]["wo_number_prefix_num"],','))));
											$supplier_order_quantity=$aftergoods_wo_data_arr[$data[('work_order_no')]][$data[('item_group')]][$description]["supplier_order_quantity"];
											$wo_amount=$aftergoods_wo_data_arr[$data[('work_order_no')]][$data[('item_group')]][$description]["amount"];
										*/
		
									}

									/*if ($print_report_format_exp[0]==67)  //Print Booking
									{									
										$print_link="<a href='#' onClick=\"generate_trim_report('show_trim_booking_report','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
									}
									else if ($print_report_format_exp[0]==183)  //Print Report 2
									{									
										$print_link="<a href='#' onClick=\"generate_trim_report('show_trim_booking_report2','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
									}
									else if ($print_report_format_exp[0]==177)  //Print Report 4
									{									
										$print_link="<a href='#' onClick=\"generate_trim_report('show_trim_booking_report4','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
									}
									else if ($print_report_format_exp[0]==235)  //Print Booking9
									{									
										$print_link="<a href='#' onClick=\"generate_trim_report('show_trim_booking_report5','".$wo_number."','".str_replace("'", "", $cbo_company_name)."','".$is_approved."','".$cbo_level."')\"> ".$all_wo." <a/>";
									}
																	
									else $print_link=$all_wo;*/

									
									//echo $receive_status."==".$mrr_value_usd; die;
									
									if($receive_status==1 && ($mrr_value_usd=='' || $mrr_value_usd==0))
									{
										//echo $data[('amount')]."==".$mrr_value_usd."==".$exchange_rate."++"; die;
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="40" align="center" <? echo $lc_bgcolor; ?>>
												<? echo $i; ?>
											</td>
											<td width="120" <? echo $lc_bgcolor; ?>>
												<p><? echo $data[('pi_number')]; ?></p>
											</td>
											<td width="80" align="center">
												<? if($data[('pi_date')]!="" && $data[('pi_date')]!="0000-00-00")   echo change_date_format($data[('pi_date')]); ?>
											</td>
											<td width="80" align="center">
												<? if($data[('last_shipment_date')]!="" && $data[('last_shipment_date')]) echo change_date_format($data[('last_shipment_date')]); ?>
											</td>
											<td width="140"><p><? echo $supplier_arr[$data[('supplier_id')]]; ?></p></td>
											<td width="100"><p><? echo $item_category[$data[('item_category_id')]]; ?></p></td>
											<td width="100" align="center">
												<p><? echo   $trim_group_arr[$data[('item_group')]]['name']; ?></p>
											</td>
											<td width="120" align="center">
												<p><? echo $des_ref[0]; if($des_ref[1]>0) echo $des_ref[1]."%"; ?></p>
											</td>
											<td width="60" align="center"><p><? echo $unit_of_measurement[$data[('uom')]]; ?></p></td>
											<td width="80" align="right">
												<p>
													<?
														$total_pi_qty+=$data[('quantity')];
														echo number_format($data[('quantity')],2,'.','');
													?>
												</p>
											</td>
											<td width="80" align="right"><p><? echo number_format($item_rate,6,'.',''); ?></p></td>
											<td width="100" align="right">
												<p>
													<?
														$total_pi_val+=$data[('amount')];
														echo number_format($data[('amount')],2,'.','');
													?>
												</p>
											</td>
											<td width="80" align="center"><P><? echo $currency[$data[('currency_id')]]; ?></P></td>
		
											<td width="80" align="right"><p>
												<?
													echo $all_wo;
												?></p>
											</td>
											<td width="80" align="right">
												<?
													//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["supplier_order_quantity"],2);
													echo number_format($supplier_order_quantity,2);
												?>
											</td>
											<td width="80" align="right" title="<? echo $z; ?>">
												<?
													//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["amount"],2);
													//echo number_format($wo_amount,2);
													
													$total_wo_amount +=$wo_amount;
													echo number_format($wo_amount,2);
												?>
											</td>
											<td width="80" align="right" title="<? echo chop($data[('work_order_id')],","); ?>" rowspan="<? //echo $rowspan;?>">
												<a href="javascript:void(0)" onClick="show_receive_qty_details('<? echo $pi_wo_id; ?>',<? echo $item_cate; ?>,<? echo $data[('goods_rcv_status')]; ?>,'<? echo $des_ref[0]; if($des_ref[1]>0) echo $des_ref[1]; ?>',<? echo $data[('uom')]; ?>,<? echo $data[('item_group')]; ?>,'<? echo $data[('item_description')]; ?>','<? echo $rate; ?>')">
													<?
		
														echo $display_font_color.number_format($mrr_qnty,2).$font_end;
													?>
												</a>
											</td>
											<td width="90" align="right" rowspan="<? //echo $rowspan;?>">
													<?
		
														echo $display_font_color.number_format($mrr_value_usd,2).$font_end;
													?>
											</td>
											<td align="right" rowspan="<? //echo $rowspan;?>">
												<?
												$short_value=0;
													$short_value=$mrr_value_usd-$data[('amount')];
		
													echo $display_font_color.number_format($short_value, 2).$font_end;
												?>
											</td>
											<?
											if($z==1)
											{
												//$total_mrr_qnty+=$receive_arr[$pi_id]['qnty'];
												$total_mrr_val+=$mrr_value_usd;
												$total_short_val+=$short_value;
												$test_data.=$mrr_qnty."__".$i.",";
											}
											?>
										</tr>
										<?
									}
									else if($receive_status==2 && ($data[('amount')] > $mrr_value_usd) && $mrr_value_usd>0)
									{
										//echo $data[('amount')]."==".$mrr_value_usd."==".$exchange_rate."++";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="40" align="center" <? echo $lc_bgcolor; ?>>
												<? echo $i; ?>
											</td>
											<td width="120" <? echo $lc_bgcolor; ?>>
												<p><? echo $data[('pi_number')]; ?></p>
											</td>
											<td width="80" align="center">
												<? if($data[('pi_date')]!="" && $data[('pi_date')]!="0000-00-00")   echo change_date_format($data[('pi_date')]); ?>
											</td>
											<td width="80" align="center">
												<? if($data[('last_shipment_date')]!="" && $data[('last_shipment_date')]) echo change_date_format($data[('last_shipment_date')]); ?>
											</td>
											<td width="140"><p><? echo $supplier_arr[$data[('supplier_id')]]; ?></p></td>
											<td width="100"><p><? echo $item_category[$data[('item_category_id')]]; ?></p></td>
											<td width="100" align="center">
												<p><? echo   $trim_group_arr[$data[('item_group')]]['name']; ?></p>
											</td>
											<td width="120" align="center">
												<p><? echo $des_ref[0]; if($des_ref[1]>0) echo $des_ref[1]."%"; ?></p>
											</td>
											<td width="60" align="center"><p><? echo $unit_of_measurement[$data[('uom')]]; ?></p></td>
											<td width="80" align="right">
												<p>
													<?
														$total_pi_qty+=$data[('quantity')];
														echo number_format($data[('quantity')],2,'.','');
													?>
												</p>
											</td>
											<td width="80" align="right"><p><? echo number_format($item_rate,6,'.',''); ?></p></td>
											<td width="100" align="right">
												<p>
													<?
														$total_pi_val+=$data[('amount')];
														echo number_format($data[('amount')],2,'.','');
													?>
												</p>
											</td>
											<td width="80" align="center"><P><? echo $currency[$data[('currency_id')]]; ?></P></td>
		
											<td width="80" align="right"><p>
												<?
													echo $all_wo;
												?></p>
											</td>
											<td width="80" align="right">
												<?
													//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["supplier_order_quantity"],2);
													echo number_format($supplier_order_quantity,2);
												?>
											</td>
											<td width="80" align="right" title="<? echo $z; ?>">
												<?
													//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["amount"],2);
													//echo number_format($wo_amount,2);
													$total_wo_amount +=$wo_amount;
													echo number_format($wo_amount,2);
												?>
											</td>
		
											<?
												/*if($z==1)
												{*/
											?>
		
													<td width="80" align="right" title="<? echo chop($data[('work_order_id')],","); ?>" rowspan="<? //echo $rowspan;?>">
														<a href="javascript:void(0)" onClick="show_receive_qty_details('<? echo $pi_wo_id; ?>',<? echo $item_cate; ?>,<? echo $data[('goods_rcv_status')]; ?>,'<? echo $des_ref[0]; if($des_ref[1]>0) echo $des_ref[1]; ?>',<? echo $data[('uom')]; ?>,<? echo $data[('item_group')]; ?>,'<? echo $data[('item_description')]; ?>','<? echo $rate; ?>')">
															<?
		
																echo $display_font_color.number_format($mrr_qnty,2).$font_end;
															?>
														</a>
													</td>
													<td width="90" align="right" rowspan="<? //echo $rowspan;?>">
															<?
		
																echo $display_font_color.number_format($mrr_value_usd,2).$font_end;
															?>
													</td>
													<td align="right" rowspan="<? //echo $rowspan;?>">
														<?
														$short_value=0;
															$short_value=$mrr_value_usd-$data[('amount')];
		
															echo $display_font_color.number_format($short_value, 2).$font_end;
														?>
													</td>
												<?
												if($z==1)
												{
													//$total_mrr_qnty+=$receive_arr[$pi_id]['qnty'];
													$total_mrr_val+=$mrr_value_usd;
													$total_short_val+=$short_value;
													$test_data.=$mrr_qnty."__".$i.",";
												}
													//$z++;
												//}
		
											?>
										</tr>
										<?
									}
									else if($receive_status==3 && ($data[('amount')] <= $mrr_value_usd))
									{
										//echo $data[('amount')]."==".$mrr_value_usd."==".$exchange_rate."++";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="40" align="center" <? echo $lc_bgcolor; ?>>
												<? echo $i; ?>
											</td>
											<td width="120" <? echo $lc_bgcolor; ?>>
												<p><? echo $data[('pi_number')]; ?></p>
											</td>
											<td width="80" align="center">
												<? if($data[('pi_date')]!="" && $data[('pi_date')]!="0000-00-00")   echo change_date_format($data[('pi_date')]); ?>
											</td>
											<td width="80" align="center">
												<? if($data[('last_shipment_date')]!="" && $data[('last_shipment_date')]) echo change_date_format($data[('last_shipment_date')]); ?>
											</td>
											<td width="140"><p><? echo $supplier_arr[$data[('supplier_id')]]; ?></p></td>
											<td width="100"><p><? echo $item_category[$data[('item_category_id')]]; ?></p></td>
											<td width="100" align="center">
												<p><? echo   $trim_group_arr[$data[('item_group')]]['name']; ?></p>
											</td>
											<td width="120" align="center">
												<p><? echo $des_ref[0]; if($des_ref[1]>0) echo $des_ref[1]."%"; ?></p>
											</td>
											<td width="60" align="center"><p><? echo $unit_of_measurement[$data[('uom')]]; ?></p></td>
											<td width="80" align="right">
												<p>
													<?
														$total_pi_qty+=$data[('quantity')];
														echo number_format($data[('quantity')],2,'.','');
													?>
												</p>
											</td>
											<td width="80" align="right"><p><? echo number_format($item_rate,6,'.',''); ?></p></td>
											<td width="100" align="right">
												<p>
													<?
														$total_pi_val+=$data[('amount')];
														echo number_format($data[('amount')],2,'.','');
													?>
												</p>
											</td>
											<td width="80" align="center"><P><? echo $currency[$data[('currency_id')]]; ?></P></td>
		
											<td width="80" align="right"><p>
												<?
													echo $all_wo;
												?></p>
											</td>
											<td width="80" align="right">
												<?
													//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["supplier_order_quantity"],2);
													echo number_format($supplier_order_quantity,2);
												?>
											</td>
											<td width="80" align="right" title="<? echo $z; ?>">
												<?
													//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["amount"],2);
													//echo number_format($wo_amount,2);
													$total_wo_amount +=$wo_amount;
													echo number_format($wo_amount,2);
												?>
											</td>
		
											<?
												/*if($z==1)
												{*/
											?>
		
													<td width="80" align="right" title="<? echo chop($data[('work_order_id')],","); ?>" rowspan="<? //echo $rowspan;?>">
														<a href="javascript:void(0)" onClick="show_receive_qty_details('<? echo $pi_wo_id; ?>',<? echo $item_cate; ?>,<? echo $data[('goods_rcv_status')]; ?>,'<? echo $des_ref[0]; if($des_ref[1]>0) echo $des_ref[1]; ?>',<? echo $data[('uom')]; ?>,<? echo $data[('item_group')]; ?>,'<? echo $data[('item_description')]; ?>','<? echo $rate; ?>')">
															<?
		
																echo $display_font_color.number_format($mrr_qnty,2).$font_end;
															?>
														</a>
													</td>
													<td width="90" align="right" rowspan="<? //echo $rowspan;?>">
															<?
		
																echo $display_font_color.number_format($mrr_value_usd,2).$font_end;
															?>
													</td>
													<td align="right" rowspan="<? //echo $rowspan;?>">
														<?
														$short_value=0;
															$short_value=$mrr_value_usd-$data[('amount')];
		
															echo $display_font_color.number_format($short_value, 2).$font_end;
														?>
													</td>
												<?
												if($z==1)
												{
													//$total_mrr_qnty+=$receive_arr[$pi_id]['qnty'];
													$total_mrr_val+=$mrr_value_usd;
													$total_short_val+=$short_value;
													$test_data.=$mrr_qnty."__".$i.",";
												}
													//$z++;
												//}
		
											?>
										</tr>
										<?
									}
									else if($receive_status==4 && ($data[('amount')] > $mrr_value_usd))
									{
										
										//echo $data[('amount')]."==".$mrr_value_usd."==".$exchange_rate."++"; die;
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="40" align="center" <? echo $lc_bgcolor; ?>>
												<? echo $i; ?>
											</td>
											<td width="120" <? echo $lc_bgcolor; ?>>
												<p><? echo $data[('pi_number')]; ?></p>
											</td>
											<td width="80" align="center">
												<? if($data[('pi_date')]!="" && $data[('pi_date')]!="0000-00-00")   echo change_date_format($data[('pi_date')]); ?>
											</td>
											<td width="80" align="center">
												<? if($data[('last_shipment_date')]!="" && $data[('last_shipment_date')]) echo change_date_format($data[('last_shipment_date')]); ?>
											</td>
											<td width="140"><p><? echo $supplier_arr[$data[('supplier_id')]]; ?></p></td>
											<td width="100"><p><? echo $item_category[$data[('item_category_id')]]; ?></p></td>
											<td width="100" align="center">
												<p><? echo   $trim_group_arr[$data[('item_group')]]['name']; ?></p>
											</td>
											<td width="120" align="center">
												<p><? echo $des_ref[0]; if($des_ref[1]>0) echo $des_ref[1]."%"; ?></p>
											</td>
											<td width="60" align="center"><p><? echo $unit_of_measurement[$data[('uom')]]; ?></p></td>
											<td width="80" align="right">
												<p>
													<?
														$total_pi_qty+=$data[('quantity')];
														echo number_format($data[('quantity')],2,'.','');
													?>
												</p>
											</td>
											<td width="80" align="right"><p><? echo number_format($item_rate,6,'.',''); ?></p></td>
											<td width="100" align="right">
												<p>
													<?
														$total_pi_val+=$data[('amount')];
														echo number_format($data[('amount')],2,'.','');
													?>
												</p>
											</td>
											<td width="80" align="center"><P><? echo $currency[$data[('currency_id')]]; ?></P></td>
		
											<td width="80" align="right"><p>
												<?
													echo $all_wo;
												?></p>
											</td>
											<td width="80" align="right">
												<?
													//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["supplier_order_quantity"],2);
													echo number_format($supplier_order_quantity,2);
												?>
											</td>
											<td width="80" align="right" title="<? echo $z; ?>">
												<?
													//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["amount"],2);
													//echo number_format($wo_amount,2);
													
													$total_wo_amount +=$wo_amount;
													echo number_format($wo_amount,2);
												?>
											</td>
		
											<?
												/*if($z==1)
												{*/
											?>
		
											<td width="80" align="right" title="<? echo chop($data[('work_order_id')],","); ?>" rowspan="<? //echo $rowspan;?>">
												<a href="javascript:void(0)" onClick="show_receive_qty_details('<? echo $pi_wo_id; ?>',<? echo $item_cate; ?>,<? echo $data[('goods_rcv_status')]; ?>,'<? echo $des_ref[0]; if($des_ref[1]>0) echo $des_ref[1]; ?>',<? echo $data[('uom')]; ?>,<? echo $data[('item_group')]; ?>,'<? echo $data[('item_description')]; ?>','<? echo $rate; ?>')">
													<?

														echo $display_font_color.number_format($mrr_qnty,2).$font_end;
													?>
												</a>
											</td>
											<td width="90" align="right" rowspan="<? //echo $rowspan;?>">
													<?

														echo $display_font_color.number_format($mrr_value_usd,2).$font_end;
													?>
											</td>
											<td align="right" rowspan="<? //echo $rowspan;?>">
												<?
												$short_value=0;
													$short_value=$mrr_value_usd-$data[('amount')];

													echo $display_font_color.number_format($short_value, 2).$font_end;
												?>
											</td>
											<?
											if($z==1)
											{
												//$total_mrr_qnty+=$receive_arr[$pi_id]['qnty'];
												$total_mrr_val+=$mrr_value_usd;
												$total_short_val+=$short_value;
												$test_data.=$mrr_qnty."__".$i.",";
											}
												//$z++;
											//}

										?>
										</tr>
										<?
									}
									else if($receive_status==5)
									{
										//echo $data[('amount')]."==".$mrr_value_usd."==".$exchange_rate."++";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="40" align="center" <? echo $lc_bgcolor; ?>>
												<? echo $i; ?>
											</td>
											<td width="120" <? echo $lc_bgcolor; ?>>
												<p><? echo $data[('pi_number')]; ?></p>
											</td>
											<td width="80" align="center">
												<? if($data[('pi_date')]!="" && $data[('pi_date')]!="0000-00-00")   echo change_date_format($data[('pi_date')]); ?>
											</td>
											<td width="80" align="center">
												<? if($data[('last_shipment_date')]!="" && $data[('last_shipment_date')]) echo change_date_format($data[('last_shipment_date')]); ?>
											</td>
											<td width="140"><p><? echo $supplier_arr[$data[('supplier_id')]]; ?></p></td>
											<td width="100"><p><? echo $item_category[$data[('item_category_id')]]; ?></p></td>
											<td width="100" align="center">
												<p><? echo   $trim_group_arr[$data[('item_group')]]['name'];  //$trim_group_arr[$data[('item_group')]]; ?></p>
											</td>
		
											<td width="120" align="center">
												<p><? echo $des_ref[0]; if($des_ref[1]>0) echo $des_ref[1]."%"; ?></p>
											</td>
											<td width="60" align="center"><p><? echo $unit_of_measurement[$data[('uom')]]; ?></p></td>
											<td width="80" align="right">
												<p>
													<?
														$total_pi_qty+=$data[('quantity')];
														echo number_format($data[('quantity')],2,'.','');
													?>
												</p>
											</td>
											<td width="80" align="right"><p><? echo number_format($item_rate,6,'.',''); ?></p></td>
											<td width="100" align="right">
												<p>
													<?
														$total_pi_val+=$data[('amount')];
														echo number_format($data[('amount')],2,'.','');
													?>
												</p>
											</td>
											<td width="80" align="center"><P><? echo $currency[$data[('currency_id')]]; ?></P></td>
		
											<td width="80" align="right"><p>
												<?
												echo $print_link;
													//echo $all_wo;
												?></p>
											</td>
											<td width="80" align="right">
												<?
													//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["supplier_order_quantity"],2);
													echo number_format($supplier_order_quantity,2);
												?>
											</td>
											<td width="80" align="right" title="<? echo $z; ?>">
												<?
													//echo number_format($wo_data_arr[$pi_id][$wo_dtls_id]["amount"],2);
													$total_wo_amount +=$wo_amount;
													echo number_format($wo_amount,2);
												?>
											</td>
		
											<?
												/*if($z==1)
												{*/
											?>
		
											<td width="80" align="right" title="<? echo  $pi_wo_id."item_description".$item_cate."item_description".$data[('goods_rcv_status')]."item_description".$des_ref[0]."item_description".$des_ref[1]."item_description".$data[('uom')]."item_description".$data[('item_group')]."item_description".$data[('item_description')]; //chop($data[('work_order_id')],",")."=".$wo_id_test."=". $item_group_test."=".$item_des_test; ?>" rowspan="<? //echo $rowspan;?>">
												<a href="javascript:void(0)" onClick="show_receive_qty_details('<? echo $pi_wo_id; ?>',<? echo $item_cate; ?>,<? echo $data[('goods_rcv_status')]; ?>,'<? echo $des_ref[0]; if($des_ref[1]>0) echo $des_ref[1]; ?>',<? echo $data[('uom')]; ?>,<? echo $data[('item_group')]; ?>,'<? echo $data[('item_description')]; ?>','<? echo $rate; ?>')">
													<?	
														echo number_format($mrr_qnty,2);
													?>
														
												</a>
											</td>
											<td width="90" align="right" rowspan="<? //echo $rowspan;?>">
												<?

													echo number_format($mrr_value_usd,2);
												?>
											</td>
											<td align="right" rowspan="<? //echo $rowspan;?>">
												<?
												$short_value=0;
													$short_value=$mrr_value_usd-$data[('amount')];

													echo number_format($short_value, 2);
												?>
											</td>
											<?
											if($z==1)
											{
												//$total_mrr_qnty+=$receive_arr[$pi_id]['qnty'];
												$total_mrr_val+=$mrr_value_usd;
												$total_short_val+=$short_value;
												$test_data.=$mrr_qnty."__".$i.",";
											}
											//$z++;
											//}
											?>
										</tr>
										<?
										}
									else
									{
									}
									$i++;
									//$z++;
								}
							}
						}
					 
					}
					?>
                </table>
                </div>
                <table width="<? echo $table_width; ?>" class="tbl_bottom" cellspacing="0" cellpadding="0" border="1" rules="all">
	                <tr>
	                    <td width="40">&nbsp;</td>
	                    <td width="120">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="140">&nbsp;</td>
	                    <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
	                    <td width="120">&nbsp;</td>
	                    <td width="60">Total</td>
                        <td width="80" >&nbsp;<? //echo number_format($total_pi_qty,2);?></td>
	                    <td width="80">&nbsp;</td>
	                    <td width="100" id="td_pi_val"><? echo number_format($total_pi_val,2);?></td>
	                    <td width="80">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="80" id="td_wo_amount"><?  echo number_format($total_wo_amount,2); ?></td>
	                    <td width="80"><? // echo number_format($total_mrr_qnty,2);?></td>
	                    <td width="90" id="td_mrr_val"><? echo number_format($total_mrr_val,2);?></td>
	                    <td id="td_short_val"><? echo number_format($total_short_val,2);?></td>
	                </tr>
			</table>

	    </fieldset>
	    </div>
		<?

	}


	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	$retype=str_replace("'","",$type);
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$filename_short=$user_id."_".$name."short.xls";
    $filename_small=$user_id."_".$name."small.xls";
	$create_new_doc = fopen($filename, 'w');
	$create_new_doc_short = fopen($filename_short, 'w');
    $create_new_doc_small = fopen($filename_small, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$is_created_short = fwrite($create_new_doc_short,$exel_short);
    $is_created_short = fwrite($create_new_doc_small,$exel_small);
	$filename=$user_id."_".$name.".xls";
	$filename_short=$user_id."_".$name."short.xls";
    $filename_small=$user_id."_".$name."small.xls";
	echo "$total_data****$filename****$filename_short****$filename_small****$retype";
	exit();
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	//echo "SELECT format_id from lib_report_template where template_name ='".$data."'  and module_id=5 and report_id=93 and is_deleted=0 and status_active=1";
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=5 and report_id=93 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#search').hide();\n";
	echo "$('#search2').hide();\n";
	echo "$('#search3').hide();\n";

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==178){echo "$('#search').show();\n";}
			if($id==278){echo "$('#search2').show();\n";}
			if($id==279){echo "$('#search3').show();\n";}
		}
	}
	/*else
	{
		echo "$('#search').show();\n";
		echo "$('#search2').show();\n";
		echo "$('#search3').show();\n";
	}*/
	exit();
}

if($action=="receive_qnty")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$item_cate=str_replace("'","",$category_id);
	$entry_form=$category_wise_entry_form[$item_cate];

	if ($item_cate==11) $item_categoryids="4,11";
	else $item_categoryids=$item_cate;

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
	<div style="width:490px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:480px">
            <div style="width:480px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="100%" align="center">
                    <thead>
                        <th align="center">Category Name : <? echo $item_category[$category_id]; ?></th>
                    </thead>
                </table>
                <br />
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="140">MRR No.</th>
                        <th width="90">Receive Date</th>
                        <th width="70">UOM</th>
                        <th>Receive Qty</th>
                    </thead>
                </table>
            </div>
            <div style="width:100%; overflow-y:scroll; max-height:230px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="462" cellpadding="0" cellspacing="0">
                <?	
	
                    $i=1; $total_qty=0;
					if ($item_cate==1) 
					{
						$description=explode('*',$description);
						$item_description_cond="  and c.yarn_count_id='$description[0]' and c.yarn_comp_type1st='$description[1]' and c.yarn_comp_percent1st='$description[2]' and c.yarn_type='$description[3]' and c.color='$description[4]'";
					}
					else if ($item_cate==2 || $item_cate==3) $item_description_cond="and c.detarmination_id='$description'";
					else $item_description_cond=" and c.item_description='$description'";
					if($goods_rcv_status==1)
					{
						$sql="select a.recv_number, a.receive_date, b.cons_uom, sum(b.cons_quantity) as qnty, sum(b.cons_amount) as amnt, c.detarmination_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type
						from inv_receive_master a, inv_transaction b, product_details_master c
						where a.id=b.mst_id and b.prod_id=c.id and b.receive_basis=2 and b.item_category in($item_categoryids) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.booking_id in($pi_id) $item_description_cond
						group by a.id,a.recv_number, a.receive_date, b.cons_uom, c.item_description, c.detarmination_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type";
						//b.pi_wo_batch_no in($pi_id)
					}
					else
					{
						$sql="select a.recv_number, a.receive_date, b.cons_uom, sum(b.cons_quantity) as qnty, sum(b.cons_amount) as amnt, c.detarmination_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type
						from inv_receive_master a, inv_transaction b, product_details_master c
						where a.id=b.mst_id and b.prod_id=c.id and b.item_category in($item_categoryids) and b.receive_basis=1 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.booking_id in($pi_id) $item_description_cond
						group by a.id,a.recv_number, a.receive_date, b.cons_uom, c.item_description, c.detarmination_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type";
						//b.pi_wo_batch_no in($pi_id)
					}
				    //echo $sql;
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
                        $total_qty += $row[csf('qnty')];

                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="140"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="90" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="70" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                            <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                     <tfoot>
                        <tr class="tbl_bottom">
                            <td colspan="4" align="right">Total</td>
                            <td align="right"><? echo number_format($total_qty,2); ?></td>
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

if($action=="receive_qnty_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$item_cate=str_replace("'","",$category_id);
	$entry_form=$category_wise_entry_form[$item_cate];
	//pi_id,category_id,goods_rcv_status,goods_description,goods_uom
	//echo goods_description
	$item_group=str_replace("'","",$item_group);
	$item_description="'$item_description'";
	$rate="'$rate'";
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
	<div style="width:490px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:480px">
            <div style="width:480px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="100%" align="center">
                    <thead>
                        <th align="center">Category Name : <? echo $item_category[$category_id]; ?></th>
                    </thead>
                </table>
                <br />
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="140">MRR No.</th>
                        <th width="90">Receive Date</th>
                        <th width="70">UOM</th>
                        <th>Receive Qty</th>
                    </thead>
                </table>
            </div>
            <div style="width:100%; overflow-y:scroll; max-height:230px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="462" cellpadding="0" cellspacing="0">
                <?
                    $i=1; $total_qty=0;
					
					
				//$recvDataArray=sql_select("select a.pi_wo_batch_no, a.receive_basis, c.item_group_id, c.item_description, a.order_qnty as qnty, a.order_amount as amnt 
		//from inv_transaction a, inv_trims_entry_dtls c  
		//where a.id=c.trans_id and a.receive_basis in(1,2) and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name and a.item_category=$cbo_item_category_id");
					
					if($goods_rcv_status==1)
					{
						$sql="select a.recv_number, a.receive_date, b.cons_uom,b.prod_id,b.item_category,c.item_group_id, c.item_description, sum(b.order_qnty) as qnty, sum(b.order_amount) as amnt
						from inv_receive_master a, inv_transaction b,inv_trims_entry_dtls c
						where a.id=b.mst_id and b.id=c.trans_id and b.receive_basis=2 and b.item_category=$category_id and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.pi_wo_batch_no in($pi_id) and c.item_group_id=$item_group and c.item_description=$item_description and c.rate=$rate
						group by a.recv_number, a.receive_date, b.cons_uom,b.prod_id,b.item_category,c.item_group_id, c.item_description";
						//a.id=c.mst_id  and b.prod_id=c.prod_id
					}
					else
					{
						$sql="select a.recv_number, a.receive_date, b.cons_uom,b.prod_id,b.item_category,c.item_group_id, c.item_description, sum(b.order_qnty) as qnty, sum(b.order_amount) as amnt
						from inv_receive_master a, inv_transaction b,inv_trims_entry_dtls c
						where a.id=b.mst_id and b.id=c.trans_id and b.item_category=$category_id and b.receive_basis=1 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.pi_wo_batch_no in($pi_id) and c.item_group_id=$item_group and c.item_description=$item_description and c.rate=$rate
						group by a.recv_number, a.receive_date, b.cons_uom,b.prod_id,b.item_category,c.item_group_id, c.item_description";
					}
					//echo $sql;
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
                        $total_qty += $row[csf('qnty')];

                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="140"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="90" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="70" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                            <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                     <tfoot>
                        <tr class="tbl_bottom">
                            <td colspan="4" align="right">Total</td>
                            <td align="right"><? echo number_format($total_qty,2); ?></td>
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


disconnect($con);
?>
