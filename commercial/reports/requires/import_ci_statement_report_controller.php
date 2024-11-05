<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$seource_des_array=array(3=>"Non EPZ",4=>"Non EPZ",5=>"Abroad",6=>"Abroad",11=>"EPZ",12=>"EPZ");


/*$receive_value_arr1 = return_library_array("select a.pi_wo_batch_no,sum(b.cons_amount) as cons_amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.item_category=1 group by a.pi_wo_batch_no ","pi_wo_batch_no","cons_amount");
$receive_value_arr1 = return_library_array("select a.pi_wo_batch_no,sum(b.cons_amount) as cons_amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.item_category=1 group by a.pi_wo_batch_no ","pi_wo_batch_no","cons_amount");
$receive_value_arr2 = return_library_array("select a.pi_wo_batch_no,sum(b.cons_amount) as cons_amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.item_category=2 group by a.pi_wo_batch_no ","pi_wo_batch_no","cons_amount");
$receive_value_arr4 = return_library_array("select a.pi_wo_batch_no,sum(b.cons_amount) as cons_amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.item_category=4 group by a.pi_wo_batch_no ","pi_wo_batch_no","cons_amount");
$receive_value_arr567 = return_library_array("select a.pi_wo_batch_no,sum(b.cons_amount) as cons_amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.item_category in(5,6,7) group by a.pi_wo_batch_no ","pi_wo_batch_no","cons_amount");
$receive_value_arr13 = return_library_array("select a.pi_wo_batch_no,sum(b.cons_amount) as cons_amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.item_category=13 group by a.pi_wo_batch_no ","pi_wo_batch_no","cons_amount");
*/

if($action=="lc_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
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
				
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push(str);					
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
				
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
		}
		
    </script>
    <body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="500" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th>Search By</th>
						<th align="center" width="200" id="search_by_td_up">Please Enter LC No</th>
 						<th>
                       		<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
                            <input type='hidden' id='txt_selected_id' />
							<input type='hidden' id='txt_selected' />
                        </th>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<td align="center">
							<?  
								$search_by = array(1=>'LC No');
								$dd="change_search_event(this.value, '0', '0', '../../')";
								echo create_drop_down( "cbo_search_by", 150, $search_by, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td width="180" align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+'<? echo $company; ?>', 'create_lc_search_list_view', 'search_div', 'import_ci_statement_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />				
						</td>
					</tr>
 				</tbody>
			 </tr>         
			</table>    
			<div align="center" valign="top" style="margin-top:5px" id="search_div"> </div> 
			</form>
	   </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
	</html>
    <?
	exit();
}

if($action=="create_lc_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($txt_search_by==1)
	{
		$sql_cond="";
		if($txt_search_common!="") $sql_cond=" and lc_number LIKE '%$txt_search_common%'";
		
		$sql = "select id,importer_id,lc_number,lc_value from com_btb_lc_master_details where importer_id in($company) and status_active=1 and is_deleted=0 $sql_cond"; 
		
	}
	// echo $sql;die;
	$arr=array(0=>$company_arr);
	echo create_list_view("list_view", "Company,Lc No,Value","120,100","600","260",0, $sql , "js_set_value", "id,lc_number", "", 1, "importer_id,0,0", $arr, "importer_id,lc_number,lc_value", "","","0,0,0,0,2","",1) ;
	exit();	
}


if($action == "item_category_list_popup")
{
	echo load_html_head_contents("Item Category", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		function js_set_value( str )
		{
			var id = $('#txt_individual_id' + str).val()
			var name= $('#txt_individual' + str).val()
			$('#hidden_item_category_id').val(id);
			$('#hidden_item_category_name').val(name);
			parent.emailwindow.hide();
		}
    </script>

	</head>

	<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
	    	<input type="hidden" name="hidden_item_category_id" id="hidden_item_category_id" class="text_boxes" value="">
	    	<input type="hidden" name="hidden_item_category_name" id="hidden_item_category_name" class="text_boxes" value="">
	        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
	                <thead>
	                    <th width="50">SL</th>
	                    <th>Item Category Name</th>
	                </thead>
	            </table>
	            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
	                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
	                <?
	                    $i=1;
	                    foreach($item_category as $id=>$name)
	                    {
	                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
	                                <td width="50" align="center"><?php echo "$i"; ?>
	                                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
	                                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
	                                </td>
	                                <td><p><? echo $name; ?></p></td>
	                        </tr>
	                        <?
	                        $i++;
	                    }
	                ?>
	                </table>
	            </div>
	        </form>
	    </fieldset>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();

}

if($action == "supplier_list_popup")
{
	echo load_html_head_contents("Supplier List", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		function js_set_value( str )
		{
			var id = $('#txt_individual_id' + str).val()
			var name= $('#txt_individual' + str).val()
			$('#hidden_supplier_id').val(id);
			$('#hidden_supplier_name').val(name);
			parent.emailwindow.hide();
		}
    </script>

	</head>
	<?
		$catWiseParty= array(
		0=>"0", 1=>"1,2", 2=>"1,9", 3=>"1,9", 13=>"1,9", 14=>"1,9", 4=>"1,4,5", 5=>"1,3",
		6=>"1,3",
		7=>"1,3",
		9=>"1,6",
		10=>"1,6",
		11=>"1,8",
		12=>"1,20,21,22,23,24,30,31,32,35,36,37,38,39",
		24=>"1,20,21,22,23,24,30,31,32,35,36,37,38,39",
		25=>"1,20,21,22,23,24,30,31,32,35,36,37,38,39",
		31=>"1,26",
		32=>"1,92"
		);

		if($catWiseParty[$category] != "")
		{
			$party_type = $catWiseParty[$category];
		}else{
			$party_type = "1,7";
		}

		$result = sql_select("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id  and c.tag_company in('$company') and b.party_type in ($party_type) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name");
	?>
	<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
	    	<input type="hidden" name="hidden_supplier_id" id="hidden_supplier_id" class="text_boxes" value="">
	    	<input type="hidden" name="hidden_supplier_name" id="hidden_supplier_name" class="text_boxes" value="">
	        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
	                <thead>
	                    <th width="50">SL</th>
	                    <th>Supplier Name</th>
	                </thead>
	            </table>
	            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
	                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
	                <?
	                    $i=1;
	                    foreach($result as $row)
	                    {
	                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
                                <td width="50" align="center"><?php echo "$i"; ?>
                                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
                                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf('supplier_name')]; ?>"/>
                                </td>
                                <td><p><? echo $row[csf('supplier_name')]; ?></p></td>
	                        </tr>
	                        <?
	                        $i++;
	                    }
	                ?>
	                </table>
	            </div>
	        </form>
	    </fieldset>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();

}

if ($action=="load_drop_down_supplier")
{

	//echo create_drop_down( "cbo_supplier_id", 100, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where FIND_IN_SET($data,a.tag_company) and a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 group by a.id order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier--", "", "" );  //and b.party_type in (1,6,7,8,90)

	$data = explode('_',$data);

	//category => party_type
	$catWiseParty= array(
	0=>"0", 1=>"1,2", 2=>"1,9", 3=>"1,9", 13=>"1,9", 14=>"1,9", 4=>"1,4,5", 5=>"1,3",
	6=>"1,3",
	7=>"1,3",
	9=>"1,6",
	10=>"1,6",
	11=>"1,8",
	12=>"1,20,21,22,23,24,30,31,32,35,36,37,38,39",
	24=>"1,20,21,22,23,24,30,31,32,35,36,37,38,39",
	25=>"1,20,21,22,23,24,30,31,32,35,36,37,38,39",
	31=>"1,26",
	32=>"1,92"
	);

	if($catWiseParty[$data[0]] != "")
	{
		$party_type = $catWiseParty[$data[0]];
	}else{
		$party_type = "1,7";
	}

	echo create_drop_down( "cbo_supplier_id", 90, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id  and c.tag_company in('$data[1]') and b.party_type in ($party_type) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
	exit();
}

if ($action=="report_generate")
{
	extract($_REQUEST);
	//ob_start();
	//echo $cbo_company_id;
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_issue=str_replace("'","",$cbo_issue_banking);
	$cbo_supplier=str_replace("'","",$cbo_supplier_id);
	$cbo_lc_type=str_replace("'","",$cbo_lc_type_id);
	$cbo_currency=str_replace("'","",$cbo_currency_id);
	$cbo_item_category=str_replace("'","",$cbo_item_category_id);
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);
	$from_date_p=str_replace("'","",$txt_date_from_p);
	$to_date_p=str_replace("'","",$txt_date_to_p);
	$from_date_c=str_replace("'","",$txt_date_from_c);
	$to_date_c=str_replace("'","",$txt_date_to_c);
	$from_date_b=str_replace("'","",$txt_date_from_b);
	$to_date_b=str_replace("'","",$txt_date_to_b);
	$pending_type=str_replace("'","",$pending_type);
	$txt_pending_date=str_replace("'","",$txt_pending_date);
	$txt_date_from_btb=str_replace("'","",$txt_date_from_btb);
	$txt_date_to_btb=str_replace("'","",$txt_date_to_btb);
	$txt_lc_no=str_replace("'","",$txt_lc_no);
	$txt_lc_id=str_replace("'","",$txt_lc_id);
	$txt_lc_sc=trim(str_replace("'","",$txt_lc_sc));
	$report_type=str_replace("'","",$report_type);
	
	//echo $report_type;die;
	// echo $cbo_company."**";die;

	if($report_type==3 || $report_type==4)
	{
		$user_arr = return_library_array("select id,user_name from user_passwd ","id","user_name");
		$issueBankrArr = return_library_array("select id,bank_name from lib_bank ","id","bank_name");
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		if($txt_lc_sc!="")
		{
			$sql_btb="SELECT a.import_mst_id as IMPORT_MST_ID
			from com_btb_export_lc_attachment a, com_export_lc b 
			where a.lc_sc_id=b.id and a.is_lc_sc=0 and b.export_lc_no='$txt_lc_sc' and b.beneficiary_name in($cbo_company)
			union all
			select a.import_mst_id as IMPORT_MST_ID
			from com_btb_export_lc_attachment a, com_sales_contract b 
			where a.lc_sc_id=b.id and a.is_lc_sc=1 and b.contract_no='$txt_lc_sc' and b.beneficiary_name in ($cbo_company)";
			//echo $sql_lc_sc;die;
			$sql_btb_result=sql_select($sql_btb);
			if(count($sql_btb_result)==0)
			{
				echo "<div class='alert alert-danger' style='text-align: center; width:50%;'><b>Data Not Found</b></div>";
				die;
			}

			foreach($sql_btb_result as $row)
			{
				$btb_id.=$row["IMPORT_MST_ID"].",";
			}
			$btb_id=rtrim($btb_id,",");
			if($txt_lc_id!=""){ $txt_lc_id.=",".$btb_id; }else{ $txt_lc_id=$btb_id; }
			unset($sql_btb_result);
		}
		//cbo_source_id
		$cbo_source_id = str_replace("'","",$cbo_source_id);
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

		
		
		/*$lc_sc_attach_sql=sql_select("select import_mst_id,lc_sc_id,is_lc_sc from com_btb_export_lc_attachment");
		$exp_lc_sc_data=array();
		foreach($exp_lc_sc_data as $row)
		{
			$exp_lc_sc_data[$row[csf("import_mst_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
			$exp_lc_sc_data[$row[csf("import_mst_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")];
		}

		$file_reference_lc_arr = return_library_array("select f.import_mst_id,h.internal_file_no from com_btb_export_lc_attachment f,com_export_lc h where f.lc_sc_id=h.id and f.is_lc_sc=0","import_mst_id","internal_file_no");
		$file_reference_sales_arr = return_library_array("select f.import_mst_id,h.internal_file_no from com_btb_export_lc_attachment f,com_sales_contract h where f.lc_sc_id=h.id and f.is_lc_sc=1 ","import_mst_id","internal_file_no");*/
		
		$sql_lc_sc="select a.import_mst_id as IMPORT_MST_ID, a.lc_sc_id as LC_SC_ID, a.is_lc_sc as IS_LC_SC, a.import_mst_id as IMPORT_MST_ID, b.internal_file_no as INTERNAL_FILE_NO 
		from com_btb_export_lc_attachment a, com_export_lc b 
		where a.lc_sc_id=b.id and a.is_lc_sc=0 and b.beneficiary_name in($cbo_company)
		union all
		select a.import_mst_id as IMPORT_MST_ID, a.lc_sc_id as LC_SC_ID, a.is_lc_sc as IS_LC_SC, a.import_mst_id as IMPORT_MST_ID, b.internal_file_no as INTERNAL_FILE_NO 
		from com_btb_export_lc_attachment a, com_sales_contract b 
		where a.lc_sc_id=b.id and a.is_lc_sc=1 and b.beneficiary_name in($cbo_company)";
		//echo $sql_lc_sc;die;
		$sql_lc_sc_result=sql_select($sql_lc_sc);
		foreach($sql_lc_sc_result as $row)
		{
			$exp_lc_sc_data[$row["IMPORT_MST_ID"]]["lc_sc_id"]=$row["LC_SC_ID"];
			$exp_lc_sc_data[$row["IMPORT_MST_ID"]]["is_lc_sc"]=$row["IS_LC_SC"];
			if($row["IS_LC_SC"]==0)
			{
				$file_reference_lc_arr[$row["IMPORT_MST_ID"]]=$row["INTERNAL_FILE_NO"];
			}
			else
			{
				$file_reference_sales_arr[$row["IMPORT_MST_ID"]]=$row["INTERNAL_FILE_NO"];
			}
		}
		unset($sql_lc_sc_result);
		
		
		$pay_data_sql=sql_select("select invoice_id, payment_date, adj_source as payment_head, accepted_ammount from com_import_payment where status_active=1 order by payment_date");
		$pay_data_arr=array();
		foreach($pay_data_sql as $row)
		{
			$pay_data_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
			$pay_data_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
			if($row[csf("payment_head")]==5)
			{
				$pay_data_arr[$row[csf("invoice_id")]]["margin"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("payment_head")]==6)
			{
				$pay_data_arr[$row[csf("invoice_id")]]["erq"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("payment_head")]==11)
			{
				$pay_data_arr[$row[csf("invoice_id")]]["std"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("payment_head")]==10)
			{
				$pay_data_arr[$row[csf("invoice_id")]]["cd"]+=$row[csf("accepted_ammount")];
			}
		}
		//echo "<pre>";print_r($pay_data_arr);die;
		unset($pay_data_sql);
		$pay_data_atsite_sql=sql_select("select invoice_id, payment_date, payment_head, adj_source, accepted_ammount from com_import_payment_com where status_active=1 order by payment_date");
		$pay_data_atsite_arr=array();
		foreach($pay_data_atsite_sql as $row)
		{
			$pay_data_atsite_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
			$pay_data_atsite_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
			if($row[csf("adj_source")]==5)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["margin"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("adj_source")]==6)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["erq"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("adj_source")]==11)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["std"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("adj_source")]==10)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["cd"]+=$row[csf("accepted_ammount")];
			}

			if($row[csf("payment_head")]==40)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["il"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("payment_head")]==45)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["bc"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("payment_head")]==70)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["int"]+=$row[csf("accepted_ammount")];
			}
		}
		unset($pay_data_atsite_sql);

		$receive_Return_sql=sql_select("select pi_wo_batch_no,sum(cons_quantity) as cons_quantity, sum(cons_amount) as cons_amount from inv_transaction where transaction_type=3 and company_id in($cbo_company) group by pi_wo_batch_no");
		$receive_Return_data_arr=array();
		foreach($receive_Return_sql as $row)
		{
			$receive_Return_data_arr[$row[csf("pi_wo_batch_no")]]["cons_quantity"]=$row[csf("cons_quantity")];
			$receive_Return_data_arr[$row[csf("pi_wo_batch_no")]]["cons_amount"]=$row[csf("cons_amount")];
		}
		unset($receive_Return_sql);

		
		$category_con='';
		if ($cbo_company=='') $company_id =""; else $company_id =" and d.importer_id in($cbo_company) ";
		if ($cbo_issue==0) $issue_banking =""; else $issue_banking =" and d.issuing_bank_id=$cbo_issue ";
		if ($cbo_supplier==0) $supplier_id =""; else $supplier_id =" and d.supplier_id=$cbo_supplier ";
		if ($cbo_lc_type==0) $type_id =""; else $type_id =" and d.lc_type_id=$cbo_lc_type ";
		if ($cbo_currency==0) $currency_id =""; else $currency_id =" and d.currency_id=$cbo_currency ";  

		if ($txt_lc_id!="") $lc_sc_id =" and d.id in($txt_lc_id)";

		$item_category_id='';
		if($cbo_item_category!='')
		{
			$ids=$cbo_item_category;
			$ids_arr=explode(",",$ids);
			$id_all="";
			$entryForms=array();
			foreach($ids_arr as $values)
			{
				if($values==1 )
				{
					if(!in_array(165,$entryForms))
					{
						array_push($entryForms,"165");
					}
				}
				else if($values==2 || $values ==3 || $values==13 || $values ==14)
				{
					if(!in_array(166,$entryForms))
					{
						array_push($entryForms,"166");
					}
				}
				else if($values==5 || $values ==6 || $values==7 || $values ==23)
				{
					if(!in_array(227,$entryForms))
					{
						array_push($entryForms,"227");
					}
				}
				else if($values==4)
				{
					if(!in_array(167,$entryForms))
					{
						array_push($entryForms,"167");
					}
				}
				else if($values==12)
				{
					if(!in_array(168,$entryForms))
					{
						array_push($entryForms,"168");
					}
				}
				else if($values==24)
				{
					if(!in_array(169,$entryForms))
					{
						array_push($entryForms,"169");
					}
				}
				else if($values==25)
				{
					if(!in_array(170,$entryForms))
					{
						array_push($entryForms,"170");
					}
				}
				else if($values==30)
				{
					if(!in_array(197,$entryForms))
					{
						array_push($entryForms,"197");
					}
					//$item_category_id =" and d.pi_entry_form=197 ";
				}
				else if($values==31)
				{

					if(!in_array(171,$entryForms))
					{
						array_push($entryForms,"171");

					}
					//$item_category_id =" and d.pi_entry_form=171 ";
					//$entry_form =171;
				}
				else if($values!=1)
				{
					if(!in_array(172,$entryForms))
					{
						array_push($entryForms,"172");
					}
				}
			}
			foreach($entryForms as $value)
			{
				if($id_all=="") $id_all=$value;
				else $id_all.=",".$value;
			}
			if(!$id_all)$id_all=0;
			$item_category_id=" and d.pi_entry_form in($id_all)";
		}


		//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
		if($db_type==0) $txt_pending_date=change_date_format($txt_pending_date,"yyyy-mm-dd"); else if($db_type==2) $txt_pending_date=date("j-M-Y",strtotime($txt_pending_date));
		if($pending_type==0) $pending_cond="";
		if($pending_type==1) $pending_cond="";

		if($pending_type==2) { if($txt_pending_date!="") $pending_cond="and a.maturity_date>'$txt_pending_date'";}
		if($pending_type==3) { if($txt_pending_date!="")  $pending_cond="and a.maturity_date<='$txt_pending_date'";}
		//echo $pending_cond;die;

		if($db_type==2)
		{
			if( $from_date=="") $maturity_date=""; else $maturity_date= " and a.maturity_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
			if( $from_date_c=="" ) $com_accep_date=""; else $com_accep_date= " and a.company_acc_date between '".date("j-M-Y",strtotime($from_date_c))."' and '".date("j-M-Y",strtotime($to_date_c))."'";
			if( $from_date_b=="" ) $bank_accep_date=""; else $bank_accep_date= " and a.bank_acc_date between '".date("j-M-Y",strtotime($from_date_b))."' and '".date("j-M-Y",strtotime($to_date_b))."'";
			if($report_type==3)
			{
				if( $from_date_p=="" ) $payment_date=""; else $payment_date= " and e.payment_date between '".date("j-M-Y",strtotime($from_date_p))."' and '".date("j-M-Y",strtotime($to_date_p))."'";
			}
			else
			{
				if( $from_date_p=="" ) $payment_date=""; else $payment_date= " and (case when d.payterm_id<>1 and e.payment_date between '".date("j-M-Y",strtotime($from_date_p))."' and '".date("j-M-Y",strtotime($to_date_p))."' then 1 when d.payterm_id=1 then 1 else 0 end )=1 ";
			}
			
			if( $txt_date_from_btb!="" &&  $txt_date_to_btb!="") $btb_date_cond= " and d.lc_date between '".date("j-M-Y",strtotime($txt_date_from_btb))."' and '".date("j-M-Y",strtotime($txt_date_to_btb))."'";else  $btb_date_cond="";
		}
		else if($db_type==0)
		{
			if( $from_date=="") $maturity_date=""; else $maturity_date= " and a.maturity_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
			if( $from_date_c=="" ) $com_accep_date=""; else $com_accep_date= " and a.company_acc_date between '".change_date_format($from_date_c,'yyyy-mm-dd')."' and '".change_date_format($to_date_c,'yyyy-mm-dd')."'";
			if( $from_date_b=="" ) $bank_accep_date=""; else $bank_accep_date= " and a.bank_acc_date between '".change_date_format($from_date_b,'yyyy-mm-dd')."' and '".change_date_format($to_date_b,'yyyy-mm-dd')."'";

			if( $from_date_p=="" ) $payment_date=""; else $payment_date= " and e.payment_date between '".change_date_format($from_date_p,'yyyy-mm-dd')."' and '".change_date_format($to_date_p,'yyyy-mm-dd')."'";
			if( $txt_date_from_btb!="" &&  $txt_date_to_btb!="" ) $btb_date_cond= " and d.lc_date between '".change_date_format($txt_date_from_btb,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to_btb,'yyyy-mm-dd')."'";else $btb_date_cond="";
		}
		
		$wo_cat_cond="";
		if($cbo_item_category!='') $wo_cat_cond=" and a.item_category_id in($cbo_item_category)";
		$sql_wo_pi="SELECT a.id as ID, b.work_order_id as WORK_ORDER_ID from com_pi_master_details a, com_pi_item_details b
		where a.id=b.pi_id and a.importer_id in($cbo_company) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id>0 and a.goods_rcv_status=1 $wo_cat_cond";
		$sql_wo_pi_result=sql_select($sql_wo_pi);
		$pi_wo_ids=array();
		foreach($sql_wo_pi_result as $row)
		{
			if($pi_wo_check[$row["ID"]][$row["WORK_ORDER_ID"]]=="")
			{
				$pi_wo_check[$row["ID"]][$row["WORK_ORDER_ID"]]=$row["WORK_ORDER_ID"];
				$pi_wo_ids[$row["ID"]].=$row["WORK_ORDER_ID"].",";
			}
		}
		unset($sql_wo_pi_result);
		if($addStringMulti!=""){ $import_source_cond = " and d.lc_category in($addStringMulti)";}else{ $import_source_cond = "";}
		$i=1;
		if($db_type==0)
		{
			if( $payment_date=="")
			{
				if($cbo_item_category==4)
				{
					$sql="Select a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id, group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( d.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id and d.item_category_id=4 $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $bank_accep_date $pending_cond  $lc_sc_id $btb_date_cond $import_source_cond
					GROUP BY
							a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref, a.release_date,a.inserted_by
	
					union all
	
					Select a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id,group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max(g.item_category) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d, com_pi_item_details f, wo_non_order_info_mst g
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id=c.id and c.id=f.pi_id and f.work_order_id=g.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and g.item_category=4 $company_id $issue_banking $supplier_id $type_id $currency_id $maturity_date  $com_accep_date $lc_sc_id $bank_accep_date $pending_cond  $btb_date_cond $import_source_cond
					GROUP BY
							a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref , a.release_date,a.inserted_by";
				}
				else if($cbo_item_category==11)
				{
					$sql="Select a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id, group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( d.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.item_category_id=11 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $bank_accep_date $pending_cond  $btb_date_cond $lc_sc_id $import_source_cond and d.id not in(Select a.id from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d  where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id and a.status_active=1 and a.is_deleted=0 and a.item_category_id=11 and  d.item_category=4 group by a.id)
					GROUP BY
							a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref, a.release_date,a.inserted_by";
				}
				else
				{
					if(!empty($cbo_item_category))
					{
	
						$category_con="and d.item_category_id in ($cbo_item_category)";
					}
					$sql="Select a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id, group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( d.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $category_con $lc_sc_id $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $bank_accep_date $pending_cond  $btb_date_cond $import_source_cond
					GROUP BY
							a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref, a.release_date,a.inserted_by";
				}
	
			}
			else
			{
				if($cbo_item_category==4)
				{
					$sql="Select a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id, group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( d.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, 1 as type, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d, com_import_payment e
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.item_category_id =4 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $bank_accep_date $lc_sc_id $payment_date $pending_cond  $btb_date_cond $import_source_cond
					GROUP BY
							a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref, a.release_date,a.inserted_by
	
					union all
	
					Select a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id, group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( g.item_category) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, 2 as type, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d, com_import_payment e,  com_pi_item_details f, wo_non_order_info_mst g
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id and c.id=f.pi_id and f.work_order_id=g.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and g.item_category=4 $company_id $issue_banking $supplier_id $type_id  $lc_sc_id $currency_id $maturity_date  $com_accep_date $bank_accep_date $payment_date $pending_cond  $btb_date_cond  $import_source_cond
					GROUP BY
							a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref, a.release_date,a.inserted_by";
				}
				elseif($cbo_item_category==11)
				{
					$sql="Select a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id, group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( d.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d, com_import_payment e
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.item_category_id=11 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $bank_accep_date $lc_sc_id  $payment_date $pending_cond  $btb_date_cond $import_source_cond and d.id not in(Select a.id from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d  where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id and a.status_active=1 and a.is_deleted=0 and a.item_category_id=11 and  d.item_category=4 group by a.id)
					GROUP BY
							a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref, a.release_date,a.inserted_by";
				}
				else
				{
					if(!empty($cbo_item_category))
					{
	
						$category_con=" and d.item_category_id in ($cbo_item_category)";
					}
	
					$sql="Select a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id, group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( d.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d, com_import_payment e
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $bank_accep_date $lc_sc_id $payment_date $pending_cond  $btb_date_cond $import_source_cond $category_con
					GROUP BY
							a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref, a.release_date,a.inserted_by";
				}
	
			}
		}
		else if($db_type==2)
		{
			if( $payment_date=="")
			{
				if($cbo_item_category==4)
				{
					$sql="SELECT a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date,  a.company_acc_date,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.bill_date, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,
					LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date, a.inserted_by
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.item_category_id=4 $lc_sc_id $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date $com_accep_date $bank_accep_date $pending_cond $btb_date_cond $import_source_cond
					GROUP BY
							a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no, a.bill_date, a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date, a.inserted_by
	
					union all
	
					Select a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date, a.company_acc_date, a.bank_acc_date, a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.bill_date, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,
					LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( g.item_category) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d, com_pi_item_details f, wo_non_order_info_mst g
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and c.id=f.pi_id and f.work_order_id=g.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and g.item_category=4 $company_id $issue_banking $supplier_id $type_id $currency_id $maturity_date $com_accep_date $lc_sc_id $bank_accep_date $pending_cond $btb_date_cond $import_source_cond
					GROUP BY
							a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no, a.bill_date,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date, a.inserted_by";
				}
				elseif($cbo_item_category==11)
				{
					$sql="Select a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date, a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.bill_date, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,
					LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date, a.inserted_by
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.item_category_id=11 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date $com_accep_date $bank_accep_date $pending_cond $btb_date_cond $import_source_cond and d.id not in(Select a.id from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d  where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id $lc_sc_id and a.status_active=1 and a.is_deleted=0 and c.item_category_id=11 and d.item_category=4 group by a.id)
					GROUP BY
							a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no, a.bill_date,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date,a.inserted_by";
				}
				else
				{
					if(!empty($cbo_item_category))
					{
	
						$category_con=" and c.item_category_id in ($cbo_item_category)";
					}
	
					$sql="Select a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date, a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.bill_date, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id, 
					LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date $com_accep_date $bank_accep_date $lc_sc_id $pending_cond $btb_date_cond $import_source_cond $category_con
					GROUP BY
							a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no, a.bill_date, a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date,a.inserted_by";
				}
	
			}
			else
			{
				if($cbo_item_category==4)
				{
					$sql="Select a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date, a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.bill_date, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id, 
					LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d ,com_import_payment e
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.item_category_id=4 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $lc_sc_id $bank_accep_date  $payment_date $pending_cond $btb_date_cond $import_source_cond
					GROUP BY
							a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no, a.bill_date,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date,a.inserted_by
	
					union all
	
					Select a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date, a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.bill_date, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id, 
					LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( g.item_category) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d ,com_import_payment e, com_pi_item_details f, wo_non_order_info_mst g
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and c.id=f.pi_id and f.work_order_id=g.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and g.item_category=4 $company_id $issue_banking $supplier_id $type_id $currency_id $lc_sc_id $maturity_date  $com_accep_date $bank_accep_date  $payment_date $pending_cond $btb_date_cond $import_source_cond
					GROUP BY
							a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no, a.bill_date,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date,a.inserted_by";
				}
				elseif($cbo_item_category==11)
				{
					$sql="Select a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date, a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.bill_date, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,
					LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d ,com_import_payment e
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.item_category_id=11 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $lc_sc_id $bank_accep_date  $payment_date $pending_cond $btb_date_cond $import_source_cond and d.id not in(Select a.id from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d  where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id and a.status_active=1 and a.is_deleted=0 and c.item_category_id=11 and d.item_category=4 group by a.id)
					GROUP BY
							a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no, a.bill_date,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date,a.inserted_by";
				}
				else
				{
					if(!empty($cbo_item_category))
					{
	
						$category_con=" and c.item_category_id in ($cbo_item_category)";
					}
	
					$sql="Select a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.bill_date, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id, 
					LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date, a.inserted_by
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d ,com_import_payment e
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id $lc_sc_id $issue_banking $supplier_id $type_id $currency_id $maturity_date  $com_accep_date $bank_accep_date  $payment_date $pending_cond $btb_date_cond $import_source_cond $category_con
					GROUP BY
							a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no, a.bill_date, a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date, a.inserted_by";
				}
	
			}
		}
		//echo $sql;die;
		//echo $issue_banking.'=='.$supplier_id.'=='.$type_id.'=='.$currency_id.'=='.$string.'=='.$maturity_date.'=='.$com_accep_date.'=='.$bank_accep_date.'=='.$payment_date.'=='.$pending_cond.'=='.$btb_date_cond.'=='.$import_source_cond; die;
		//echo $sql;//die;
		
		$exportPiSupp = sql_select("select c.import_pi, a.id from com_btb_lc_master_details a , com_btb_lc_pi b , com_pi_master_details c where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id");
		foreach ($exportPiSupp as $value)
		{
			$exportPiSuppArr[$value[csf("id")]] = $value[csf("import_pi")];
		}
		
		$category_con='';
		if(!empty($cbo_item_category))
		{
			$category_con=" and c.item_category_id in ($cbo_item_category)";
		}
	
		if($db_type==2)
		{
			$lc_item_category_sql=sql_select("Select  LISTAGG( c.item_category_id, ',') WITHIN GROUP (ORDER BY c.item_category_id) as item_category_id , d.id from  com_btb_lc_master_details d,com_btb_lc_pi b, com_pi_item_details c where d.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and d.status_active=1 and b.status_active=1 and c.status_active=1 $category_con $company_id group by d.id");
		}
		else if($db_type==0)
		{
			$lc_item_category_sql=sql_select("Select  group_concat( c.item_category_id) as item_category_id , d.id from  com_btb_lc_master_details d,com_btb_lc_pi b, com_pi_item_details c where d.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and d.status_active=1 and b.status_active=1 and c.status_active=1 $category_con $company_id group by d.id");
		}
		$item_category_data=array();
		foreach($lc_item_category_sql as $row)
		{
			$item_category_data[$row[csf("id")]]['item_category_id']=$row[csf("item_category_id")];
		}
		//print_r($item_category_data);, a.doc_rcv_date, a.local_doc_send_date
		$pi_id_all='';
		$sql_data = sql_select($sql);
		$cat_arr=array();
		foreach($sql_data as $row)
		{
			if($report_type==3)
			{
				if($pending_type>0)
				{
					if($row[csf('current_acceptance_value')]>$pay_data_arr[$row[csf('id')]]["accepted_ammount"])
					{
						$result_arr[$row[csf('id')]]['id']=$row[csf('id')];
						$result_arr[$row[csf('id')]]['invoice_no']=$row[csf('invoice_no')];
						$result_arr[$row[csf('id')]]['invoice_date']=$row[csf('invoice_date')];
						$result_arr[$row[csf('id')]]['doc_rcv_date']=$row[csf('doc_rcv_date')];
						$result_arr[$row[csf('id')]]['local_doc_send_date']=$row[csf('local_doc_send_date')];
						$result_arr[$row[csf('id')]]['company_acc_date']=$row[csf('company_acc_date')];
						$result_arr[$row[csf('id')]]['bank_acc_date']=$row[csf('bank_acc_date')];
						$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
						$result_arr[$row[csf('id')]]['shipment_date']=$row[csf('shipment_date')];
						$result_arr[$row[csf('id')]]['eta_date']=$row[csf('eta_date')]; 
						$result_arr[$row[csf('id')]]['bill_no']=$row[csf('bill_no')];
						$result_arr[$row[csf('id')]]['bill_date']=$row[csf('bill_date')];
						$result_arr[$row[csf('id')]]['feeder_vessel']=$row[csf('feeder_vessel')];
						$result_arr[$row[csf('id')]]['mother_vessel']=$row[csf('mother_vessel')];
						$result_arr[$row[csf('id')]]['container_no']=$row[csf('container_no')];
						$result_arr[$row[csf('id')]]['pkg_quantity']=$row[csf('pkg_quantity')];
						$result_arr[$row[csf('id')]]['bill_of_entry_no']=$row[csf('bill_of_entry_no')];
						$result_arr[$row[csf('id')]]['maturity_date']=$row[csf('maturity_date')];
						$result_arr[$row[csf('id')]]['acceptance_time']=$row[csf('acceptance_time')];
						$result_arr[$row[csf('id')]]['copy_doc_receive_date']=$row[csf('copy_doc_receive_date')];
						$result_arr[$row[csf('id')]]['doc_to_cnf']=$row[csf('doc_to_cnf')];
						$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
						$result_arr[$row[csf('id')]]['current_acceptance_value']=$row[csf('current_acceptance_value')];
						$result_arr[$row[csf('id')]]['import_invoice_id']=$row[csf('import_invoice_id')];
						$result_arr[$row[csf('id')]]['pi_number']=$row[csf('pi_number')];
						$result_arr[$row[csf('id')]]['pi_date']=$row[csf('pi_date')];
						$result_arr[$row[csf('id')]]['pi_mst_id']=$row[csf('pi_mst_id')];
						$result_arr[$row[csf('id')]]['btb_lc_id']=$row[csf('btb_lc_id')];
						$result_arr[$row[csf('id')]]['lc_type_id']=$row[csf('lc_type_id')];
						$result_arr[$row[csf('id')]]['issuing_bank_id']=$row[csf('issuing_bank_id')];
						$result_arr[$row[csf('id')]]['lc_number']=$row[csf('lc_number')];
						$result_arr[$row[csf('id')]]['lc_date']=$row[csf('lc_date')];
						$result_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
						$result_arr[$row[csf('id')]]['lca_no']=$row[csf('lca_no')];
						$result_arr[$row[csf('id')]]['item_category_id']=$row[csf('item_category_id')];
						$result_arr[$row[csf('id')]]['tenor']=$row[csf('tenor')];
						$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
						$result_arr[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
						$result_arr[$row[csf('id')]]['lc_expiry_date']=$row[csf('lc_expiry_date')];
						$result_arr[$row[csf('id')]]['etd_date']=$row[csf('etd_date')];
						$result_arr[$row[csf('id')]]['lc_category']=$row[csf('lc_category')];
						$result_arr[$row[csf('id')]]['payterm_id']=$row[csf('payterm_id')];
						$result_arr[$row[csf('id')]]['goods_rcv_status']=$row[csf('goods_rcv_status')];
						$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
						$result_arr[$row[csf('id')]]['ready_to_approved']=$row[csf('ready_to_approved')];
						$result_arr[$row[csf('id')]]['approved']=$row[csf('approved')];
						$result_arr[$row[csf('id')]]['release_date']=$row[csf('release_date')];
						$result_arr[$row[csf('id')]]['inserted_by']=$row[csf('inserted_by')];
						$pi_id_all.=$row[csf('pi_mst_id')].',';
						$btb_id_all[$row[csf('btb_lc_id')]]=$row[csf('btb_lc_id')];
					}
				}
				else
				{
					$result_arr[$row[csf('id')]]['id']=$row[csf('id')];
					$result_arr[$row[csf('id')]]['invoice_no']=$row[csf('invoice_no')];
					$result_arr[$row[csf('id')]]['invoice_date']=$row[csf('invoice_date')];
					$result_arr[$row[csf('id')]]['doc_rcv_date']=$row[csf('doc_rcv_date')];
					$result_arr[$row[csf('id')]]['local_doc_send_date']=$row[csf('local_doc_send_date')];
					$result_arr[$row[csf('id')]]['company_acc_date']=$row[csf('company_acc_date')];
					$result_arr[$row[csf('id')]]['bank_acc_date']=$row[csf('bank_acc_date')];
					$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
					$result_arr[$row[csf('id')]]['shipment_date']=$row[csf('shipment_date')];
					$result_arr[$row[csf('id')]]['eta_date']=$row[csf('eta_date')]; 
					$result_arr[$row[csf('id')]]['bill_no']=$row[csf('bill_no')];
					$result_arr[$row[csf('id')]]['bill_date']=$row[csf('bill_date')];
					$result_arr[$row[csf('id')]]['feeder_vessel']=$row[csf('feeder_vessel')];
					$result_arr[$row[csf('id')]]['mother_vessel']=$row[csf('mother_vessel')];
					$result_arr[$row[csf('id')]]['container_no']=$row[csf('container_no')];
					$result_arr[$row[csf('id')]]['pkg_quantity']=$row[csf('pkg_quantity')];
					$result_arr[$row[csf('id')]]['bill_of_entry_no']=$row[csf('bill_of_entry_no')];
					$result_arr[$row[csf('id')]]['maturity_date']=$row[csf('maturity_date')];
					$result_arr[$row[csf('id')]]['acceptance_time']=$row[csf('acceptance_time')];
					$result_arr[$row[csf('id')]]['copy_doc_receive_date']=$row[csf('copy_doc_receive_date')];
					$result_arr[$row[csf('id')]]['doc_to_cnf']=$row[csf('doc_to_cnf')];
					$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
					$result_arr[$row[csf('id')]]['current_acceptance_value']=$row[csf('current_acceptance_value')];
					$result_arr[$row[csf('id')]]['import_invoice_id']=$row[csf('import_invoice_id')];
					$result_arr[$row[csf('id')]]['pi_number']=$row[csf('pi_number')];
					$result_arr[$row[csf('id')]]['pi_date']=$row[csf('pi_date')];
					$result_arr[$row[csf('id')]]['pi_mst_id']=$row[csf('pi_mst_id')];
					$result_arr[$row[csf('id')]]['btb_lc_id']=$row[csf('btb_lc_id')];
					$result_arr[$row[csf('id')]]['lc_type_id']=$row[csf('lc_type_id')];
					$result_arr[$row[csf('id')]]['issuing_bank_id']=$row[csf('issuing_bank_id')];
					$result_arr[$row[csf('id')]]['lc_number']=$row[csf('lc_number')];
					$result_arr[$row[csf('id')]]['lc_date']=$row[csf('lc_date')];
					$result_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
					$result_arr[$row[csf('id')]]['lca_no']=$row[csf('lca_no')];
					$result_arr[$row[csf('id')]]['item_category_id']=$row[csf('item_category_id')];
					$result_arr[$row[csf('id')]]['tenor']=$row[csf('tenor')];
					$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
					$result_arr[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
					$result_arr[$row[csf('id')]]['lc_expiry_date']=$row[csf('lc_expiry_date')];
					$result_arr[$row[csf('id')]]['etd_date']=$row[csf('etd_date')];
					$result_arr[$row[csf('id')]]['lc_category']=$row[csf('lc_category')];
					$result_arr[$row[csf('id')]]['payterm_id']=$row[csf('payterm_id')];
					$result_arr[$row[csf('id')]]['goods_rcv_status']=$row[csf('goods_rcv_status')];
					$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
					$result_arr[$row[csf('id')]]['ready_to_approved']=$row[csf('ready_to_approved')];
					$result_arr[$row[csf('id')]]['approved']=$row[csf('approved')];
					$result_arr[$row[csf('id')]]['release_date']=$row[csf('release_date')];
					$result_arr[$row[csf('id')]]['inserted_by']=$row[csf('inserted_by')];
					$pi_id_all.=$row[csf('pi_mst_id')].',';
					$btb_id_all[$row[csf('btb_lc_id')]]=$row[csf('btb_lc_id')];
				}
				$all_mst_id .= $row[csf('id')].",";
				$cat_arr[$row[csf("item_category_id")]]=$row[csf("item_category_id")];
			}
			else
			{
				if($row[csf('payterm_id')]==1)
				{
					//echo strtotime($pay_data_atsite_arr[$row[csf('id')]]["payment_date"]).test.strtotime($from_date_p).test.strtotime($to_date_p);die;
					if($pay_data_atsite_arr[$row[csf('id')]]["payment_date"]!="" && $pay_data_atsite_arr[$row[csf('id')]]["payment_date"]!="0000-00-00" && strtotime($pay_data_atsite_arr[$row[csf('id')]]["payment_date"])>=strtotime($from_date_p) && strtotime($pay_data_atsite_arr[$row[csf('id')]]["payment_date"])<=strtotime($to_date_p))
					{
						$result_arr[$row[csf('id')]]['id']=$row[csf('id')];
						$result_arr[$row[csf('id')]]['invoice_no']=$row[csf('invoice_no')];
						$result_arr[$row[csf('id')]]['invoice_date']=$row[csf('invoice_date')];
						$result_arr[$row[csf('id')]]['doc_rcv_date']=$row[csf('doc_rcv_date')];
						$result_arr[$row[csf('id')]]['local_doc_send_date']=$row[csf('local_doc_send_date')];
						$result_arr[$row[csf('id')]]['company_acc_date']=$row[csf('company_acc_date')];
						$result_arr[$row[csf('id')]]['bank_acc_date']=$row[csf('bank_acc_date')];
						$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
						$result_arr[$row[csf('id')]]['shipment_date']=$row[csf('shipment_date')];
						$result_arr[$row[csf('id')]]['eta_date']=$row[csf('eta_date')]; 
						$result_arr[$row[csf('id')]]['bill_no']=$row[csf('bill_no')];
						$result_arr[$row[csf('id')]]['bill_date']=$row[csf('bill_date')];
						$result_arr[$row[csf('id')]]['feeder_vessel']=$row[csf('feeder_vessel')];
						$result_arr[$row[csf('id')]]['mother_vessel']=$row[csf('mother_vessel')];
						$result_arr[$row[csf('id')]]['container_no']=$row[csf('container_no')];
						$result_arr[$row[csf('id')]]['pkg_quantity']=$row[csf('pkg_quantity')];
						$result_arr[$row[csf('id')]]['bill_of_entry_no']=$row[csf('bill_of_entry_no')];
						$result_arr[$row[csf('id')]]['maturity_date']=$row[csf('maturity_date')];
						$result_arr[$row[csf('id')]]['acceptance_time']=$row[csf('acceptance_time')];
						$result_arr[$row[csf('id')]]['copy_doc_receive_date']=$row[csf('copy_doc_receive_date')];
						$result_arr[$row[csf('id')]]['doc_to_cnf']=$row[csf('doc_to_cnf')];
						$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
						$result_arr[$row[csf('id')]]['current_acceptance_value']=$row[csf('current_acceptance_value')];
						$result_arr[$row[csf('id')]]['import_invoice_id']=$row[csf('import_invoice_id')];
						$result_arr[$row[csf('id')]]['pi_number']=$row[csf('pi_number')];
						$result_arr[$row[csf('id')]]['pi_date']=$row[csf('pi_date')];
						$result_arr[$row[csf('id')]]['pi_mst_id']=$row[csf('pi_mst_id')];
						$result_arr[$row[csf('id')]]['btb_lc_id']=$row[csf('btb_lc_id')];
						$result_arr[$row[csf('id')]]['lc_type_id']=$row[csf('lc_type_id')];
						$result_arr[$row[csf('id')]]['issuing_bank_id']=$row[csf('issuing_bank_id')];
						$result_arr[$row[csf('id')]]['lc_number']=$row[csf('lc_number')];
						$result_arr[$row[csf('id')]]['lc_date']=$row[csf('lc_date')];
						$result_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
						$result_arr[$row[csf('id')]]['lca_no']=$row[csf('lca_no')];
						$result_arr[$row[csf('id')]]['item_category_id']=$row[csf('item_category_id')];
						$result_arr[$row[csf('id')]]['tenor']=$row[csf('tenor')];
						$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
						$result_arr[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
						$result_arr[$row[csf('id')]]['lc_expiry_date']=$row[csf('lc_expiry_date')];
						$result_arr[$row[csf('id')]]['etd_date']=$row[csf('etd_date')];
						$result_arr[$row[csf('id')]]['lc_category']=$row[csf('lc_category')];
						$result_arr[$row[csf('id')]]['payterm_id']=$row[csf('payterm_id')];
						$result_arr[$row[csf('id')]]['goods_rcv_status']=$row[csf('goods_rcv_status')];
						$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
						$result_arr[$row[csf('id')]]['release_date']=$row[csf('release_date')];
						$result_arr[$row[csf('id')]]['inserted_by']=$row[csf('inserted_by')];
						$pi_id_all.=$row[csf('pi_mst_id')].',';
						$btb_id_all[$row[csf('btb_lc_id')]]=$row[csf('btb_lc_id')];
					}
				}
				else
				{
					if($pay_data_arr[$row[csf('id')]]["payment_date"]!="" && $pay_data_arr[$row[csf('id')]]["payment_date"]!="0000-00-00")
					{
						$result_arr[$row[csf('id')]]['id']=$row[csf('id')];
						$result_arr[$row[csf('id')]]['invoice_no']=$row[csf('invoice_no')];
						$result_arr[$row[csf('id')]]['invoice_date']=$row[csf('invoice_date')];
						$result_arr[$row[csf('id')]]['release_date']=$row[csf('release_date')];
						$result_arr[$row[csf('id')]]['inserted_by']=$row[csf('inserted_by')];
						$result_arr[$row[csf('id')]]['doc_rcv_date']=$row[csf('doc_rcv_date')];
						$result_arr[$row[csf('id')]]['local_doc_send_date']=$row[csf('local_doc_send_date')];
						$result_arr[$row[csf('id')]]['company_acc_date']=$row[csf('company_acc_date')];
						$result_arr[$row[csf('id')]]['bank_acc_date']=$row[csf('bank_acc_date')];
						$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
						$result_arr[$row[csf('id')]]['shipment_date']=$row[csf('shipment_date')];
						$result_arr[$row[csf('id')]]['eta_date']=$row[csf('eta_date')]; 
						$result_arr[$row[csf('id')]]['bill_no']=$row[csf('bill_no')];
						$result_arr[$row[csf('id')]]['bill_date']=$row[csf('bill_date')];
						$result_arr[$row[csf('id')]]['feeder_vessel']=$row[csf('feeder_vessel')];
						$result_arr[$row[csf('id')]]['mother_vessel']=$row[csf('mother_vessel')];
						$result_arr[$row[csf('id')]]['container_no']=$row[csf('container_no')];
						$result_arr[$row[csf('id')]]['pkg_quantity']=$row[csf('pkg_quantity')];
						$result_arr[$row[csf('id')]]['bill_of_entry_no']=$row[csf('bill_of_entry_no')];
						$result_arr[$row[csf('id')]]['maturity_date']=$row[csf('maturity_date')];
						$result_arr[$row[csf('id')]]['acceptance_time']=$row[csf('acceptance_time')];
						$result_arr[$row[csf('id')]]['copy_doc_receive_date']=$row[csf('copy_doc_receive_date')];
						$result_arr[$row[csf('id')]]['doc_to_cnf']=$row[csf('doc_to_cnf')];
						$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
						$result_arr[$row[csf('id')]]['current_acceptance_value']=$row[csf('current_acceptance_value')];
						$result_arr[$row[csf('id')]]['import_invoice_id']=$row[csf('import_invoice_id')];
						$result_arr[$row[csf('id')]]['pi_number']=$row[csf('pi_number')];
						$result_arr[$row[csf('id')]]['pi_date']=$row[csf('pi_date')];
						$result_arr[$row[csf('id')]]['pi_mst_id']=$row[csf('pi_mst_id')];
						$result_arr[$row[csf('id')]]['btb_lc_id']=$row[csf('btb_lc_id')];
						$result_arr[$row[csf('id')]]['lc_type_id']=$row[csf('lc_type_id')];
						$result_arr[$row[csf('id')]]['issuing_bank_id']=$row[csf('issuing_bank_id')];
						$result_arr[$row[csf('id')]]['lc_number']=$row[csf('lc_number')];
						$result_arr[$row[csf('id')]]['lc_date']=$row[csf('lc_date')];
						$result_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
						$result_arr[$row[csf('id')]]['lca_no']=$row[csf('lca_no')];
						$result_arr[$row[csf('id')]]['item_category_id']=$row[csf('item_category_id')];
						$result_arr[$row[csf('id')]]['tenor']=$row[csf('tenor')];
						$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
						$result_arr[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
						$result_arr[$row[csf('id')]]['lc_expiry_date']=$row[csf('lc_expiry_date')];
						$result_arr[$row[csf('id')]]['etd_date']=$row[csf('etd_date')];
						$result_arr[$row[csf('id')]]['lc_category']=$row[csf('lc_category')];
						$result_arr[$row[csf('id')]]['payterm_id']=$row[csf('payterm_id')];
						$result_arr[$row[csf('id')]]['goods_rcv_status']=$row[csf('goods_rcv_status')];
						$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
						$pi_id_all.=$row[csf('pi_mst_id')].',';
						$btb_id_all[$row[csf('btb_lc_id')]]=$row[csf('btb_lc_id')];
					}
				}
			}

			// $cat_arr[$row[csf("item_category_id")]]=$row[csf("item_category_id")];
		}
		unset($sql_data);

		$uniqueArray = array_values(array_unique($cat_arr));
		$cat_id= implode(', ', $uniqueArray);
		$pi_id_all=array_unique(explode(",",chop($pi_id_all,',')));
		$pi_id_in=where_con_using_array($pi_id_all,0,'id');
		$piGoodRcvArr = return_library_array("select id,goods_rcv_status from com_pi_master_details where status_active=1 and is_deleted=0 $pi_id_in","id","goods_rcv_status"); 
		$companyArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name"); 
	
		$btb_id_in=where_con_using_array($btb_id_all,0,'a.import_mst_id');
		$lc_sc_sql="SELECT a.import_mst_id as BTB_ID, export_lc_no as LC_SC_NO from com_btb_export_lc_attachment a, com_export_lc b where a.lc_sc_id=b.id $btb_id_in and a.is_lc_sc=0 and a.status_active=1
		union all 
		SELECT a.import_mst_id as BTB_ID, contract_no as LC_SC_NO from com_btb_export_lc_attachment a, com_sales_contract b where a.lc_sc_id=b.id $btb_id_in and a.is_lc_sc=1 and a.status_active=1";
		// echo $lc_sc_sql;
		$lc_sc_data=sql_select($lc_sc_sql);
		$lc_sc_no=array();
		foreach($lc_sc_data as $row)
		{
			$lc_sc_no[$row["BTB_ID"]].=$row["LC_SC_NO"].', ';
		}
		unset($lc_sc_data);
		$all_mst_ids = ltrim(implode(",", array_unique(explode(",", chop($all_mst_id, ",")))), ','); 
		$appr_sql = "SELECT a.ID, a.INVOICE_NO ,b.APPROVED_BY 
		FROM com_import_invoice_mst a, approval_history b
		WHERE a.id=b.mst_id  and  a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and b.entry_form=38 and a.id in($all_mst_ids)";
		//echo $appr_sql;
		$apprv_data=sql_select($appr_sql);
		$apprv_by_arr= array();
		foreach($apprv_data as $row)
		{
			$apprv_by_arr[$row["ID"]]['APPROVED_BY']=$row["APPROVED_BY"];
		}

		$userData=sql_select( "select ID, USER_NAME, USER_FULL_NAME, designation from user_passwd");
		foreach($userData as $user_row)
		{
			$user_name_array[$user_row['ID']]['NAME']=$user_row['USER_NAME'];
			$user_name_array[$user_row['ID']]['FULL_NAME']=$user_row['USER_FULL_NAME'];
		}

		$receive_sql=sql_select("select a.receive_basis as RECEIVE_BASIS, b.booking_id as BOOKING_ID, a.order_qnty as ORDER_QNTY, a.order_amount as ORDER_AMOUNT 
		from inv_transaction a, inv_receive_master b 
		where a.mst_id=b.id and a.transaction_type=1 and a.ITEM_CATEGORY in($cat_id) and a.status_active=1 and a.company_id in($cbo_company)");
		
		$receive_data_arr=array();
		foreach($receive_sql as $row)
		{
			$receive_data_arr[$row["BOOKING_ID"]][$row["RECEIVE_BASIS"]]["cons_quantity"]+=$row["ORDER_QNTY"];
			$receive_data_arr[$row["BOOKING_ID"]][$row["RECEIVE_BASIS"]]["cons_amount"]+=$row["ORDER_AMOUNT"];
		}
		unset($receive_sql);

		ob_start();
		if($report_type==3)
		{
			?>
			<div id="" align="center" style="height:auto; width:3818px; margin:0 auto; padding:0;">
				<table width="4060px" align="center">
					<?
					$company_library=sql_select("SELECT id, COMPANY_NAME from lib_company where id in($cbo_company)");
					foreach( $company_library as $row)
					{
						$company_name.=$row["COMPANY_NAME"].", ";
					}
					?>
					<tr>
						<td colspan="37" align="center" style="font-size:22px"><center><strong><? echo rtrim($company_name,", ");?></strong></center></td>
					</tr>
					<tr>
						<td colspan="37" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?> Report</u></strong></center></td>
					</tr>
					<tr>
						<td colspan="37" align="center"> <div style="text-align:center;" class="search_type"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4,"","","2,3,4" ); ?></div></td>
						
					</tr>
				</table>
				<div style="width:4160px;">
					<table  cellspacing="0" width="4160px"  border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
						<thead>
							   <tr>
								<th rowspan="2" width="30">SL</th>
								<th rowspan="2" width="100" align="center">Invoice No</th>
								<th rowspan="2" width="75" align="center">Invoice Date</th>
								<th rowspan="2" width="80" align="center">Ready To Approved</th>
								<th rowspan="2" width="80" align="center">Approval Status</th>
								<th rowspan="2" width="100" align="center">Approved By</th>
								<th rowspan="2" width="100" align="center">File Ref. No.</th>
								<th rowspan="2" width="120" align="center">Issuing Bank</th>
								<th rowspan="2" width="100" align="center">LC No</th>
								<th rowspan="2" width="75" align="center">Lc Date</th>
								<th rowspan="2" width="70" align="center">Import Source</th>
								<th rowspan="2" width="50" align="center"><p>Suppl.</p></th>
								<th rowspan="2" width="50" align="center"><p>PI No & Date</p></th>
								<th rowspan="2" width="100" align="center">PI No</th>
								<th rowspan="2" width="100" align="center">Item Category</th>
								<th rowspan="2" width="90" align="center">LCA No</th>
								<th rowspan="2" width="50" align="center">Tenor</th>
								<th rowspan="2" width="80" align="center">LC value</th>
								<th rowspan="2" width="40" align="center"><p>Curr.</p></th>
								<th rowspan="2" width="70" align="center">Doc Recv. Date</th>
								<th rowspan="2" width="70" align="center">Local Doc Send Date</th>
								<th rowspan="2" width="70" align="center">Com. Accep. Date</th>
								<th rowspan="2" width="70" align="center">Bank Accep. Date</th>
								<th rowspan="2" width="70" align="center">Bank Ref</th>
								<th rowspan="2" width="80" align="center">Bill Value</th>
								<th rowspan="2" width="90" align="center">Paid Amount</th>
								<th rowspan="2" width="80" align="center">Out Standing</th>
								<th colspan="3">Bank Payment For At Sight</th>
								<th rowspan="2" width="70" align="center">Maturity Date</th>
								<th rowspan="2" width="70" align="center">Month</th>
								<th rowspan="2" width="70" align="center">Pay Date</th>
								<th rowspan="2" width="70" align="center"><p>Shipment Date</p></th>
								<th rowspan="2" width="70" align="center">Expiry Date</th>
								<th rowspan="2" width="80" align="center">Lc Type</th>
								<th rowspan="2" width="70" align="center">ETD</th>
								<th rowspan="2" width="70" align="center">ETA</th>
								<th rowspan="2" width="100" align="center">BL/Cargo No</th>
								<th rowspan="2" width="80" align="center"> BL/Cargo Date</th>
								<th rowspan="2" width="70" align="center">Feeder Vassel</th>
								<th rowspan="2" width="70" align="center">Mother Vassel</th>
								<th rowspan="2" width="60" align="center"><p>Continer No</p></th>
								<th rowspan="2" width="70" align="center">Pkg Qty</th>
								<th rowspan="2" width="80" align="center">Bill Of Entry No</th>
								<th rowspan="2" width="80" align="center">Total Qty</th>
								<th rowspan="2" width="80" align="center">Release Date</th>
								<th rowspan="2" width="70" align="center">NN Doc Received Date</th>
								<th rowspan="2" width="70" align="center">Doc Send to CNF</th>
								<th rowspan="2" width="100" align="center">LC/SC Number</th>
								<th rowspan="2" width="60" align="center">Goods in House Date</th>
								<th rowspan="2" width="80" align="center">Actual Received Value</th>
								<th rowspan="2" width="80"align="center">Balance Value</th>						   
								<th rowspan="2" align="center">Insert User</th>						   
							</tr>
							<tr>
								<th width="80" >IFDBC Liability</th>
								<th width="80" >Bank Charge Amount</th>
								<th width="80" >Interest Paid Amount</th>
							</tr>
						</thead>
					</table>
					</div>
					<div style="width:4160px; overflow-y: scroll; max-height:300px;" id="scroll_body">
					<table cellspacing="0" width="4160px"  border="1" rules="all" class="rpt_table" id="tbl_body" >
					<?
						$i=1;
						$lc_check=array();
						foreach( $result_arr as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
				
							$import_invoice_id=$row[('import_invoice_id')];
							$suppl_id=$row[('supplier_id')];
							$item_id=$row[('item_category_id')];
							$curr_id=$row[('currency_id')];
							$pi_id=$row[('pi_mst_id')];
				
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="100"><p><? echo $row[('invoice_no')]; ?></p></td>
								<td width="75"><p>&nbsp;<? if($row[('invoice_date')]!="0000-00-00") echo change_date_format($row[('invoice_date')]); else echo "";?></p></td>
								<td width="80" align="center"><? if($row['ready_to_approved']==1){ echo "Yes"; }else{ echo "No"; }?></td>
								<td width="80" align="center">
									<?
										if($row['approved']==1){ echo "Approved"; }
										else if($row['approved']==3){ echo "Partial Approved";  }
										else{ echo "Not Approved"; }
									?>
								</td>
								<td width="100"><?echo $user_name_array[$apprv_by_arr[$row["id"]]['APPROVED_BY']]['FULL_NAME']?></td>
								<td width="100"><p>
								<?
								$exp_lc_sc_id =$exp_lc_sc_data[$row[('btb_lc_id')]]["lc_sc_id"];
								$is_lc_sc =$exp_lc_sc_data[$row[('btb_lc_id')]]["is_lc_sc"];
								if($is_lc_sc==0)
								$file_reference_no=$file_reference_lc_arr[$row[('btb_lc_id')]];
								else
								$file_reference_no=$file_reference_sales_arr[$row[('btb_lc_id')]];
								echo $file_reference_no; 
								?></p></td>
								<td width="120"><p><? echo $issueBankrArr[$row[('issuing_bank_id')]]; ?></p></td>
								<td width="100"><p>&nbsp;<? echo trim($row[('lc_number')]); ?></p></td>
				
								<td width="75"><p>&nbsp;<? if($row[('lc_date')]!="0000-00-00") echo change_date_format($row[('lc_date')]); else echo ""; ?></p></td>
								<td width="70"><p><? echo $supply_source[$row[('lc_category')]*1]; ?></p></td>
								<td width="50"><p><? if($exportPiSuppArr[$row[('btb_lc_id')]]==1) echo $companyArr[$row[('supplier_id')]]; else echo $supplierArr[$row[('supplier_id')]];  ?></p></td>
								<td width="50" align="center"><p><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage_pi_date('$import_invoice_id','$suppl_id','$item_id','$pi_id','$curr_id','pi_details','PI Details');\">"."View"."</a>";//$row[("pi_id")]; ?></p></td>
								<td width="100"><p><? echo $row[('pi_number')];?></p></td>
								<td width="100"><p><? $itemCategory=""; echo $item_category[$item_id]; ?></p></td>
								<td width="90"><p><? echo $row[('lca_no')]; ?></p></td>
								<td width="50"><p><? echo $row[('tenor')]; ?></p></td>
								<?
								if($lc_check[$row[('btb_lc_id')]]=="")
								{
									$lc_check[$row[('btb_lc_id')]]=$row[('btb_lc_id')];
									?>
									<td width="80" align="right"><? echo number_format($row[('lc_value')],2); $tot_lc_value+=$row[('lc_value')];//echo number_format($row[('lc_value')],2); //btb_lc_id?></td>
									<?
									$cash_payment=$row[('lc_value')];
								}
								else
								{
									?>
									<td width="80" align="right"><? echo "<span style='color:white'>'</span>".number_format($row[('lc_value')],2)."<span style='color:white'>'</span>";?><!--<span style="color:white">_</span>--></td>
									<?
									$cash_payment==0;
								}
								?>
				
								<td width="40"><p><? echo $currency[$row[('currency_id')]]; ?></p></td>
								<td width="70"><p>&nbsp;<? if($row[('doc_rcv_date')]!="0000-00-00") echo change_date_format($row[('doc_rcv_date')]); else echo ""; ?></p></td>
								<td width="70"><p>&nbsp;<? if($row[('local_doc_send_date')]!="0000-00-00") echo change_date_format($row[('local_doc_send_date')]); ?></p></td>
								<td width="70"><p>&nbsp;<? if($row[('company_acc_date')]!="0000-00-00") echo change_date_format($row[('company_acc_date')]); else echo ""; ?></p></td>
								<td width="70"><p>&nbsp;<? if($row[('bank_acc_date')]!="0000-00-00") echo change_date_format($row[('bank_acc_date')]); ?></p></td>
								<td width="70"><p><? echo $row[('bank_ref')]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[('current_acceptance_value')],2); $tot_bill_value+=$row[('current_acceptance_value')]; ?></p></td>
								<?
								if($row[('payterm_id')]==3)
								{
									?>
									<td width="90" align="right"><p><a href="#report_details" onClick="paid_amount_dtls('<? echo $row[('id')];?>','payment_details','Payment Details');">
									<?
									echo number_format($cash_payment,2); 
									$out_standing=$row[('current_acceptance_value')]-$cash_payment;
									$gt_total_paid+=$cash_payment;
									?>
									</a></p></td>
									<?
								}
								else
								{
									?>
									<td width="90" align="right"><p><a href="#report_details" onClick="paid_amount_dtls('<? echo $row[('id')];?>','payment_details','Payment Details');">
									<?
									echo number_format($pay_data_arr[$row[('id')]]["accepted_ammount"],2); 
									$out_standing=$row[('current_acceptance_value')]-$pay_data_arr[$row['id']]["accepted_ammount"];
									$gt_total_paid+=$pay_data_arr[$row[('id')]]["accepted_ammount"];
									?>
									</a></p></td>
									<?
								}
								?>
								<td width="80" align="right"><p><? echo number_format($out_standing,2); $total_out_standing +=$out_standing ?></p></td>
	
								<td width="80" align="right"><p><? echo number_format($pay_data_atsite_arr[$import_invoice_id]["il"],2); $total_atsite_il +=$pay_data_atsite_arr[$import_invoice_id]["il"]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($pay_data_atsite_arr[$import_invoice_id]["bc"],2); $total_atsite_bc +=$pay_data_atsite_arr[$import_invoice_id]["bc"]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($pay_data_atsite_arr[$import_invoice_id]["int"],2); $total_atsite_int +=$pay_data_atsite_arr[$import_invoice_id]["int"]; ?></p></td>
	
								<td width="70"><p>&nbsp;<? if($row[('maturity_date')]!="0000-00-00") echo change_date_format($row[('maturity_date')]); else echo ""; ?></p></td>
								<td width="70"><p>
								<?
									if($row[('maturity_date')]=="00-00-0000")
									{
										$month="";
									}
									else
									{
										$month = date('F', strtotime($row[('maturity_date')]));
									}
									echo $month;
				
								 ?></p></td>
								<td width="70" ><p>&nbsp;<? if($pay_data_arr[$row[('id')]]["payment_date"]!="" && $pay_data_arr[$row[('id')]]["payment_date"]!="0000-00-00") echo change_date_format($pay_data_arr[$row[('id')]]["payment_date"]); ?></p></td>
								<td width="70"><p><? if($row[('shipment_date')]!="0000-00-00") echo change_date_format($row[('shipment_date')]); else echo ""; ?></p></td>
								<td width="70"><p>&nbsp;<? if($row[('lc_expiry_date')]!="0000-00-00")  echo change_date_format($row[('lc_expiry_date')]); else echo ""; ?></p></td>
								<td width="80"><p><? echo $lc_type[$row[('lc_type_id')]]; ?></p></td>
								<td width="70"><p>&nbsp;<? if($row[('etd_date')]!="0000-00-00") echo change_date_format($row[('etd_date')]); else echo ""; ?></p></td>
								<td width="70"><p>&nbsp;<? if($row[('eta_date')]!="0000-00-00") echo change_date_format($row[('eta_date')]); else echo ""; ?></p></td>
								<td width="100"><p><? echo $row[('bill_no')]; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[('bill_date')]); ?></p></td>
								<td width="70"><p><? echo $row[('feeder_vessel')]; ?></p></td>
								<td width="70"><p><? echo $row[('mother_vessel')]; ?></p></td>
								<td width="60"><p><? echo $row[('container_no')]; ?></p></td>
								<td width="70"><p><? echo $row[('pkg_quantity')]; ?></p></td>
								<td width="80"><p><? echo $row[('bill_of_entry_no')]; ?></p></td>
								<?
								//$receive_data_arr[$row["BOOKING_ID"]][$row["RECEIVE_BASIS"]]["cons_quantity"]+=$row["ORDER_QNTY"];
								//$receive_data_arr[$row["BOOKING_ID"]][$row["RECEIVE_BASIS"]]["cons_amount"]+=$row["ORDER_AMOUNT"];
								$receive_qnty=$receive_value=$receive_Return_qnty=$receive_Return_value=0;
								$all_wo_pi_id=$receive_basis="";
								$pi_id_all=explode(",",$row[('pi_mst_id')]);
								foreach($pi_id_all as $piid)
								{
									if($piGoodRcvArr[$piid]==1)
									{
										$pi_wo_ids[$row["ID"]].=$row["WORK_ORDER_ID"].",";
										$wo_id_arr=explode(",",chop($pi_wo_ids[$piid],","));
										foreach($wo_id_arr as $wo_id)
										{
											$receive_qnty += $receive_data_arr[$wo_id][2]["cons_quantity"];
											$receive_value += $receive_data_arr[$wo_id][2]["cons_amount"];
											$all_wo_pi_id.=$wo_id.",";
										}
										$receive_basis=2;
										
									}
									else
									{
										$receive_qnty += $receive_data_arr[$piid][1]["cons_quantity"];
										$receive_value += $receive_data_arr[$piid][1]["cons_amount"];
										$all_wo_pi_id.=$piid.",";
										$receive_basis=1;
									}
									
									$receive_Return_qnty += $receive_Return_data_arr[$piid]["cons_quantity"];
									$receive_Return_value += $receive_Return_data_arr[$piid]["cons_amount"];
								}
								$receive_actual_value=$receive_value-$receive_Return_value;
								$balance_value=$row[('lc_value')]-$receive_actual_value;
								?>
								<td width="80" align="right"><p><? echo number_format($receive_qnty,0,"",""); $total_receive_qnty+=$receive_qnty; ?></p></td>
								<td width="80" align="right"><p><? echo change_date_format($row[("release_date")]); ?></p></td>
								<td width="70"><p>&nbsp;<?  if($row[('copy_doc_receive_date')]!="0000-00-00")  echo change_date_format($row[('copy_doc_receive_date')]); else echo ""; ?></p></td>
								<td width="70"><p>&nbsp;<? if($row[('doc_to_cnf')]!="0000-00-00") echo change_date_format($row[('doc_to_cnf')]); else echo ""; ?></p></td>
								<td width="100"><p><? echo chop($lc_sc_no[$row[('btb_lc_id')]],", ");?></p></td>
								<td width="60" align="center"><p><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage_inHouse_date('".chop($all_wo_pi_id,",")."','".$receive_basis."','$receive_value','$receive_qnty','$item_id','pi_rec_details','PI Details','".$import_invoice_id."');\">"."View"."</a>"; //$row[("pi_id")]; ?></p></td>
								
								<td width="80" align="right" ><p><?   echo number_format($receive_actual_value,2); $total_receive_value+=$receive_actual_value; ?></p></td>
								<td width="80" align="right" ><p><?   echo  number_format($balance_value,2);  $total_balance_value+=$balance_value; ?></p></td>
								<td width="55"><p><? echo $user_arr[$row[('inserted_by')]];?></p></td>
								
							</tr>
							<?
							$i++;
						}
					?>
					</table>
					<table cellspacing="0" width="4160px"  border="1" rules="all" class="rpt_table" id="report_table_footer" >
						<tfoot>
							<th width="30">&nbsp;</th>
							<th width="100" >&nbsp;</th>
							<th width="75" >&nbsp;</th>
							<th width="80" >&nbsp;</th>
							<th width="80" >&nbsp;</th>
							<th width="100" >&nbsp;</th>
							<th width="100" >&nbsp;</th>
							<th width="120" >&nbsp;</th>
							<th width="100" >&nbsp;</th>
							<th width="75" >&nbsp;</th>
							<th width="70" >&nbsp;</th>
							<th width="50" ><p>&nbsp;</p></th>
							<th width="50" ><p>&nbsp;</p></th>
							<th width="100" >&nbsp;</th>
							<th width="100" >&nbsp;</th>
							<th width="90" >&nbsp;</th>
							<th width="50" align="right">Total : </th>
							<th width="80" align="right" id="value_tot_lc_value"><? echo number_format($tot_lc_value,2); ?></th>
							<th width="40" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="80" align="right" id="value_tot_bill_value"><? echo number_format($tot_bill_value,2); ?></th>
							<th width="90" align="right" id="value_gt_total_paid"><? echo number_format($gt_total_paid,2); ?></th>
							<th width="80" align="right" id="value_total_out_standing"><? echo number_format($total_out_standing,2); ?></th>
							<th width="80" align="right" id="value_atsite_il"><? echo number_format($total_atsite_il,2); ?></th>
							<th width="80" align="right" id="value_atsite_bc"><? echo number_format($total_atsite_bc,2); ?></th>
							<th width="80" align="right" id="value_atsite_int"><? echo number_format($total_atsite_int,2); ?></th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="80" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="100" align="center">&nbsp;</th>
							<th width="80" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="60" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="80" align="center">&nbsp;</th>
							<!-- <th align="right" id="total_qnty" width="80"><? echo number_format($total_receive_qnty,2); ?></th> -->
							<th align="right" width="80"><? echo number_format($total_receive_qnty,2); ?></th>
							<th width="80" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="100" align="center">&nbsp;</th>
							<th width="60" align="center">&nbsp;</th>
							<!-- <th width="80" align="right" id="value_total_receive"><? echo number_format($total_receive_value,2); ?></th> -->
							<th width="80" align="right" ><? echo number_format($total_receive_value,2); ?></th>
							<th width="80" align="right" id="value_balance_value"><? echo number_format($total_balance_value,2); ?></th>
							<th></th>
						</tfoot>
				</table>
					<div align="left" style="font-weight:bold; margin-left:30px;"><? echo "User Id : ". $user_arr[$user_id] ." , &nbsp; THIS IS SYSTEM GENERATED STATEMENT, NO SIGNATURE REQUIRED ."; ?></div>
				</div>
			</div>
			<?
		}
		else
		{
			?>
			<div id="" align="center" style="height:auto; width:1750px; margin:0 auto; padding:0;">
				<table width="1750px" align="center">
					<?
					$company_library=sql_select("SELECT id, COMPANY_NAME from lib_company where id in($cbo_company)");
					foreach( $company_library as $row)
					{
						$company_name.=$row["COMPANY_NAME"].", ";
					}
					?>
					<tr>
						<td colspan="37" align="center" style="font-size:22px"><center><strong><? echo rtrim($company_name,", ");?></strong></center></td>
					</tr>
					<tr>
						<td colspan="37" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?> Report</u></strong></center></td>
					</tr>
				</table>
				<div style="width:1750px;">
					<table  cellspacing="0" width="1750"  border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
					   <thead>
								<th width="30">SL</th>
								<th width="100" align="center">Invoice No</th>
								<th width="75" align="center">Invoice Date</th>
								<th width="120" align="center">Issuing Bank</th>
								<th width="100" align="center">LC No</th>
								<th width="75" align="center">Lc Date</th>
								<th width="70" align="center">Import Source</th>
								<th width="50" align="center"><p>Suppl.</p></th>
								<th width="100" align="center">Item Category</th>
								<th width="80" align="center">LC value</th>
								<th width="50" align="center">Currency</th>
								<th width="70" align="center">Bank Accep. Date</th>
								<th width="70" align="center">Bank Ref</th>
								<th width="90" align="center">Invoice Value</th>
								<th width="90" align="center">Margine</th>
								<th width="90" align="center">ERQ</th>
								<th width="90" align="center">STD</th>
								<th width="90" align="center">CD</th>
								<th width="70" align="center">Maturity Date</th>
								<th width="70" align="center">Month</th>
								<th width="70" align="center">Last Pay Date</th>
								<th align="center">Lc Type</th>
						</thead>
					</table>
					</div>
					<div style="width:1770px; overflow-y: scroll; max-height:300px;" id="scroll_body">
					<table cellspacing="0" width="1750px"  border="1" rules="all" class="rpt_table" id="tbl_body" >
					<?
						$i=1;
						$lc_check=array();
						foreach( $result_arr as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
				
							$import_invoice_id=$row[('import_invoice_id')];
							$suppl_id=$row[('supplier_id')];
							$item_id=$row[('item_category_id')];
							$curr_id=$row[('currency_id')];
							$pi_id=$row[('pi_mst_id')];
				
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30 "><p><? echo $i; ?></p></td>
								<td width="100"><p style=text-align:left;><? echo $row[('invoice_no')]; ?></p></td>
								<td width="75"><p><? if($row[('invoice_date')]!="0000-00-00") echo change_date_format($row[('invoice_date')]); else echo "";?></p></td>
								<td width="120"><p><? echo $issueBankrArr[$row[('issuing_bank_id')]]; ?></p></td>
								<td width="100"><p><? echo trim($row[('lc_number')]); ?></p>&nbsp;</td>
								<td width="75"><p><? if($row[('lc_date')]!="0000-00-00") echo change_date_format($row[('lc_date')]); else echo ""; ?></p></td>
								<td width="70" title="<?= $row[('lc_category')]."pay term id=".$row[('payterm_id')];?>"><p><? echo $supply_source[$row[('lc_category')]*1]; ?></p></td>
								<td width="50"><p><? if($exportPiSuppArr[$row[('btb_lc_id')]]==1) echo $companyArr[$row[('supplier_id')]]; else echo $supplierArr[$row[('supplier_id')]]; ?></p></td>
								<td width="100" title="<?= $item_id;?>"><p><? $itemCategory=""; echo $item_category[$item_id]; ?></p></td>
								<?
								if($lc_check[$row[('btb_lc_id')]]=="")
								{
									$lc_check[$row[('btb_lc_id')]]=$row[('btb_lc_id')];
									?>
									<td width="80" align="right"><? echo number_format($row[('lc_value')],2); $tot_lc_value+=$row[('lc_value')];//echo number_format($row[('lc_value')],2); //btb_lc_id?></td>
									<?
									$cash_payment=$row[('lc_value')];
								}
								else
								{
									?>
									<td width="80" align="right"><? echo "<span style='color:white'>'</span>".number_format($row[('lc_value')],2)."<span style='color:white'>'</span>";?><!--<span style="color:white">_</span>--></td>
									<?
									$cash_payment==0;
								}
								?>
								<td width="50"><p><? echo $currency[$row[('currency_id')]]; ?></p></td>
								<td width="70"><p><? if($row[('bank_acc_date')]!="0000-00-00") echo change_date_format($row[('bank_acc_date')]); ?></p></td>
								<td width="70"><p><? echo $row[('bank_ref')]; ?></p></td>
								<td width="90" align="right"><p><? echo number_format($row[('current_acceptance_value')],2); $tot_bill_value+=$row[('current_acceptance_value')]; ?></p></td>
								<?
								if($row[('payterm_id')]==1)
								{
									?>
									<td width="90" align="right"><p><?  echo number_format($pay_data_atsite_arr[$row[('id')]]["margin"],2);  $gt_total_margin+=$pay_data_atsite_arr[$row[('id')]]["margin"]; ?></td>
									<td width="90" align="right"><p><?  echo number_format($pay_data_atsite_arr[$row[('id')]]["erq"],2);  $gt_total_erq+=$pay_data_atsite_arr[$row[('id')]]["erq"]; ?></td>
									<td width="90" align="right"><p><?  echo number_format($pay_data_atsite_arr[$row[('id')]]["std"],2);  $gt_total_std+=$pay_data_atsite_arr[$row[('id')]]["std"]; ?></td>
									<td width="90" align="right"><p><?  echo number_format($pay_data_atsite_arr[$row[('id')]]["cd"],2);  $gt_total_cd+=$pay_data_atsite_arr[$row[('id')]]["cd"]; ?></td>
									<?
								}
								else
								{
									$pay_data_arr[$row[csf("invoice_id")]]["margin"]+=$row[csf("accepted_ammount")];
									$pay_data_arr[$row[csf("invoice_id")]]["erq"]+=$row[csf("accepted_ammount")];
									$pay_data_arr[$row[csf("invoice_id")]]["std"]+=$row[csf("accepted_ammount")];
									$pay_data_arr[$row[csf("invoice_id")]]["cd"]+=$row[csf("accepted_ammount")];
									?>
									<td width="90" align="right"><p><?  echo number_format($pay_data_arr[$row[('id')]]["margin"],2);  $gt_total_margin+=$pay_data_arr[$row[('id')]]["margin"]; ?></td>
									<td width="90" align="right"><p><?  echo number_format($pay_data_arr[$row[('id')]]["erq"],2);  $gt_total_erq+=$pay_data_arr[$row[('id')]]["erq"]; ?></td>
									<td width="90" align="right"><p><?  echo number_format($pay_data_arr[$row[('id')]]["std"],2);  $gt_total_std+=$pay_data_arr[$row[('id')]]["std"]; ?></td>
									<td width="90" align="right"><p><?  echo number_format($pay_data_arr[$row[('id')]]["cd"],2);  $gt_total_cd+=$pay_data_arr[$row[('id')]]["cd"]; ?></td>
									<?
								}
								?>
								<td width="70"><p><? if($row[('maturity_date')]!="0000-00-00") echo change_date_format($row[('maturity_date')]); else echo ""; ?></p></td>
								<td width="70"><p>
								<?
									if($row[('maturity_date')]=="00-00-0000")
									{
										$month="";
									}
									else
									{
										$month = date('F', strtotime($row[('maturity_date')]));
									}
									echo $month;
								 ?></p></td>
								<td width="70" ><p><? if($pay_data_arr[$row[('id')]]["payment_date"]!="" && $pay_data_arr[$row[('id')]]["payment_date"]!="0000-00-00") echo change_date_format($pay_data_arr[$row[('id')]]["payment_date"]); ?></p></td>
								<td><p><? echo $lc_type[$row[('lc_type_id')]]; ?></p></td>
							</tr>
							<?
							$i++;
						}
					?>
					</table>
					<table cellspacing="0" width="1750"  border="1" rules="all" class="rpt_table" id="report_table_footer" >
						<tfoot>
							<th width="30">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="75">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="75">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="100">Total:</th>
							<th width="80" align="right" id="value_tot_lc_value"><? echo number_format($tot_lc_value,2); ?></th>
							<th width="50" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>user
							<th width="70" align="center">&nbsp;</th>
							<th width="90" align="right" id="value_tot_bill_value"><? echo number_format($tot_bill_value,2); ?></th>
							<th width="90" align="right" id="value_gt_total_margin"><? echo number_format($gt_total_margin,2); ?></th>
							<th width="90" align="right" id="value_gt_total_erq"><? echo number_format($gt_total_erq,2); ?></th>
							<th width="90" align="right" id="value_gt_total_std"><? echo number_format($gt_total_std,2); ?></th>
							<th width="90" align="right" id="value_gt_total_paid"><? echo number_format($gt_total_paid,2); ?></th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th align="center">&nbsp;</th>
						</tfoot>
				</table>
					<div align="left" style="font-weight:bold; margin-left:30px;"><? echo "User Id : ". $user_arr[$user_id] ." , &nbsp; THIS IS SYSTEM GENERATED STATEMENT, NO SIGNATURE REQUIRED ."; ?></div>
				</div>
			</div>
			<?
		}
	}
	if($report_type==5)
	{
		$user_arr = return_library_array("SELECT id,user_name from user_passwd ","id","user_name");
		$companyArr = return_library_array("select id,company_short_name from lib_company ","id","company_short_name"); 
		$buyerArr = return_library_array("SELECT id,buyer_name from lib_buyer ","id","buyer_name");
		$issueBankrArr = return_library_array("SELECT id,bank_name from lib_bank ","id","bank_name");
		$supplierArr = return_library_array("SELECT id,supplier_name from lib_supplier where status_active=1","id","supplier_name");
		$item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name');
		$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
		//cbo_source_id
		$cbo_source_id = str_replace("'","",$cbo_source_id);
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
		if($txt_lc_sc!="")
		{
			$sql_btb="SELECT a.import_mst_id as IMPORT_MST_ID
			from com_btb_export_lc_attachment a, com_export_lc b 
			where a.lc_sc_id=b.id and a.is_lc_sc=0 and b.export_lc_no='$txt_lc_sc' and b.beneficiary_name in($cbo_company)
			union all
			select a.import_mst_id as IMPORT_MST_ID
			from com_btb_export_lc_attachment a, com_sales_contract b 
			where a.lc_sc_id=b.id and a.is_lc_sc=1 and b.contract_no='$txt_lc_sc' and b.beneficiary_name in ($cbo_company)";
			//echo $sql_lc_sc;die;
			$sql_btb_result=sql_select($sql_btb);
			if(count($sql_btb_result)==0)
			{
				echo "<div class='alert alert-danger' style='text-align: center; width:50%;'><b>Data Not Found</b></div>";
				die;
			}

			foreach($sql_btb_result as $row)
			{
				$btb_id.=$row["IMPORT_MST_ID"].",";
			}
			$btb_id=rtrim($btb_id,",");
			if($txt_lc_id!=""){ $txt_lc_id.=",".$btb_id; }else{ $txt_lc_id=$btb_id; }
			unset($sql_btb_result);
		}
		
		$pay_data_sql=sql_select("select invoice_id, payment_date, adj_source as payment_head, accepted_ammount from com_import_payment where status_active=1 order by payment_date");
		$pay_data_arr=array();
		foreach($pay_data_sql as $row)
		{
			$pay_data_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
			$pay_data_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
			if($row[csf("payment_head")]==5)
			{
				$pay_data_arr[$row[csf("invoice_id")]]["margin"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("payment_head")]==6)
			{
				$pay_data_arr[$row[csf("invoice_id")]]["erq"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("payment_head")]==11)
			{
				$pay_data_arr[$row[csf("invoice_id")]]["std"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("payment_head")]==10)
			{
				$pay_data_arr[$row[csf("invoice_id")]]["cd"]+=$row[csf("accepted_ammount")];
			}
		}
		//echo "<pre>";print_r($pay_data_arr);die;
		unset($pay_data_sql);
		$pay_data_atsite_sql=sql_select("select invoice_id, payment_date, payment_head, adj_source, accepted_ammount from com_import_payment_com where status_active=1 order by payment_date");
		$pay_data_atsite_arr=array();
		foreach($pay_data_atsite_sql as $row)
		{
			$pay_data_atsite_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
			$pay_data_atsite_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
			if($row[csf("adj_source")]==5)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["margin"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("adj_source")]==6)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["erq"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("adj_source")]==11)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["std"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("adj_source")]==10)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["cd"]+=$row[csf("accepted_ammount")];
			}

			if($row[csf("payment_head")]==40)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["il"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("payment_head")]==45)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["bc"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("payment_head")]==70)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["int"]+=$row[csf("accepted_ammount")];
			}
		}
		unset($pay_data_atsite_sql);
		
		$receive_sql=sql_select("select a.receive_basis as RECEIVE_BASIS, b.booking_id as BOOKING_ID, a.order_qnty as ORDER_QNTY, a.order_amount as ORDER_AMOUNT 
		from inv_transaction a, inv_receive_master b 
		where a.mst_id=b.id and a.transaction_type=1 and a.status_active=1 and a.company_id in($cbo_company)");
		$receive_data_arr=array();
		foreach($receive_sql as $row)
		{
			$receive_data_arr[$row["BOOKING_ID"]][$row["RECEIVE_BASIS"]]["cons_quantity"]+=$row["ORDER_QNTY"];
			$receive_data_arr[$row["BOOKING_ID"]][$row["RECEIVE_BASIS"]]["cons_amount"]+=$row["ORDER_AMOUNT"];
		}
		unset($receive_sql);

		$receive_Return_sql=sql_select("select pi_wo_batch_no,sum(cons_quantity) as cons_quantity, sum(cons_amount) as cons_amount from inv_transaction where transaction_type=3 and company_id in ($cbo_company) group by pi_wo_batch_no");
		$receive_Return_data_arr=array();
		foreach($receive_Return_sql as $row)
		{
			$receive_Return_data_arr[$row[csf("pi_wo_batch_no")]]["cons_quantity"]=$row[csf("cons_quantity")];
			$receive_Return_data_arr[$row[csf("pi_wo_batch_no")]]["cons_amount"]=$row[csf("cons_amount")];
		}
		unset($receive_Return_sql);

		
		$category_con='';
		if ($cbo_company=="") $company_id =""; else $company_id =" and d.importer_id in ($cbo_company) ";
		if ($cbo_issue==0) $issue_banking =""; else $issue_banking =" and d.issuing_bank_id=$cbo_issue ";
		if ($cbo_supplier==0) $supplier_id =""; else $supplier_id =" and d.supplier_id=$cbo_supplier ";
		if ($cbo_lc_type==0) $type_id =""; else $type_id =" and d.lc_type_id=$cbo_lc_type ";
		if ($cbo_currency==0) $currency_id =""; else $currency_id =" and d.currency_id=$cbo_currency ";  
		if ($txt_lc_id!="") $lc_sc_id =" and d.id in($txt_lc_id)";
		
		$item_category_id='';
		if($cbo_item_category!='')
		{
			$ids=$cbo_item_category;
			$ids_arr=explode(",",$ids);
			$id_all="";
			$entryForms=array();
			foreach($ids_arr as $values)
			{
				if($values==1 )
				{
					if(!in_array(165,$entryForms))
					{
						array_push($entryForms,"165");
					}
				}
				else if($values==2 || $values ==3 || $values==13 || $values ==14)
				{
					if(!in_array(166,$entryForms))
					{
						array_push($entryForms,"166");
					}
				}
				else if($values==5 || $values ==6 || $values==7 || $values ==23)
				{
					if(!in_array(227,$entryForms))
					{
						array_push($entryForms,"227");
					}
				}
				else if($values==4)
				{
					if(!in_array(167,$entryForms))
					{
						array_push($entryForms,"167");
					}
				}
				else if($values==12)
				{
					if(!in_array(168,$entryForms))
					{
						array_push($entryForms,"168");
					}
				}
				else if($values==24)
				{
					if(!in_array(169,$entryForms))
					{
						array_push($entryForms,"169");
					}
				}
				else if($values==25)
				{
					if(!in_array(170,$entryForms))
					{
						array_push($entryForms,"170");
					}
				}
				else if($values==30)
				{
					if(!in_array(197,$entryForms))
					{
						array_push($entryForms,"197");
					}
					//$item_category_id =" and d.pi_entry_form=197 ";
				}
				else if($values==31)
				{

					if(!in_array(171,$entryForms))
					{
						array_push($entryForms,"171");

					}
					//$item_category_id =" and d.pi_entry_form=171 ";
					//$entry_form =171;
				}
				else if($values!=1)
				{
					if(!in_array(172,$entryForms))
					{
						array_push($entryForms,"172");
					}
				}
			}
			foreach($entryForms as $value)
			{
				if($id_all=="") $id_all=$value;
				else $id_all.=",".$value;
			}
			if(!$id_all)$id_all=0;
			$item_category_id=" and d.pi_entry_form in($id_all)";
		}


		//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
		if($db_type==0) $txt_pending_date=change_date_format($txt_pending_date,"yyyy-mm-dd"); else if($db_type==2) $txt_pending_date=date("j-M-Y",strtotime($txt_pending_date));
		if($pending_type==0) $pending_cond="";
		if($pending_type==1) $pending_cond="";

		if($pending_type==2) { if($txt_pending_date!="") $pending_cond="and a.maturity_date>'$txt_pending_date'";}
		if($pending_type==3) { if($txt_pending_date!="")  $pending_cond="and a.maturity_date<='$txt_pending_date'";}
		//echo $pending_cond;die;

		if($db_type==2)
		{
			if( $from_date=="") $maturity_date=""; else $maturity_date= " and a.maturity_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
			if( $from_date_c=="" ) $com_accep_date=""; else $com_accep_date= " and a.company_acc_date between '".date("j-M-Y",strtotime($from_date_c))."' and '".date("j-M-Y",strtotime($to_date_c))."'";
			if( $from_date_b=="" ) $bank_accep_date=""; else $bank_accep_date= " and a.bank_acc_date between '".date("j-M-Y",strtotime($from_date_b))."' and '".date("j-M-Y",strtotime($to_date_b))."'";
			if($report_type==3)
			{
				if( $from_date_p=="" ) $payment_date=""; else $payment_date= " and e.payment_date between '".date("j-M-Y",strtotime($from_date_p))."' and '".date("j-M-Y",strtotime($to_date_p))."'";
			}
			else
			{
				if( $from_date_p=="" ) $payment_date=""; else $payment_date= " and (case when d.payterm_id<>1 and e.payment_date between '".date("j-M-Y",strtotime($from_date_p))."' and '".date("j-M-Y",strtotime($to_date_p))."' then 1 when d.payterm_id=1 then 1 else 0 end )=1 ";
			}
			
			if( $txt_date_from_btb!="" &&  $txt_date_to_btb!="") $btb_date_cond= " and d.lc_date between '".date("j-M-Y",strtotime($txt_date_from_btb))."' and '".date("j-M-Y",strtotime($txt_date_to_btb))."'";else  $btb_date_cond="";
		}
		else if($db_type==0)
		{
			if( $from_date=="") $maturity_date=""; else $maturity_date= " and a.maturity_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
			if( $from_date_c=="" ) $com_accep_date=""; else $com_accep_date= " and a.company_acc_date between '".change_date_format($from_date_c,'yyyy-mm-dd')."' and '".change_date_format($to_date_c,'yyyy-mm-dd')."'";
			if( $from_date_b=="" ) $bank_accep_date=""; else $bank_accep_date= " and a.bank_acc_date between '".change_date_format($from_date_b,'yyyy-mm-dd')."' and '".change_date_format($to_date_b,'yyyy-mm-dd')."'";

			if( $from_date_p=="" ) $payment_date=""; else $payment_date= " and e.payment_date between '".change_date_format($from_date_p,'yyyy-mm-dd')."' and '".change_date_format($to_date_p,'yyyy-mm-dd')."'";
			if( $txt_date_from_btb!="" &&  $txt_date_to_btb!="" ) $btb_date_cond= " and d.lc_date between '".change_date_format($txt_date_from_btb,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to_btb,'yyyy-mm-dd')."'";else $btb_date_cond="";
		}
		
		$wo_cat_cond="";
		if($cbo_item_category!='') $wo_cat_cond=" and a.item_category_id in($cbo_item_category)";
		$sql_wo_pi="select a.id as ID, b.work_order_id as WORK_ORDER_ID from com_pi_master_details a, com_pi_item_details b
		where a.id=b.pi_id and a.importer_id in($cbo_company) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id>0 and a.goods_rcv_status=1 $wo_cat_cond";
		$sql_wo_pi_result=sql_select($sql_wo_pi);
		$pi_wo_ids=array();
		foreach($sql_wo_pi_result as $row)
		{
			if($pi_wo_check[$row["ID"]][$row["WORK_ORDER_ID"]]=="")
			{
				$pi_wo_check[$row["ID"]][$row["WORK_ORDER_ID"]]=$row["WORK_ORDER_ID"];
				$pi_wo_ids[$row["ID"]].=$row["WORK_ORDER_ID"].",";
			}
		}
		unset($sql_wo_pi_result);
		if($addStringMulti!=""){ $import_source_cond = " and d.lc_category in($addStringMulti)";}else{ $import_source_cond = "";}
		$i=1;
		if($db_type==0)
		{
			if( $payment_date=="")
			{
				if($cbo_item_category==4)
				{
					$sql="SELECT a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id, group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( d.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value,d.importer_id,d.maturity_from_id
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id and d.item_category_id=4 $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $bank_accep_date $pending_cond  $lc_sc_id $btb_date_cond $import_source_cond
					GROUP BY
							a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,d.importer_id,d.maturity_from_id

					union all

					Select a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id,group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max(g.item_category) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value,d.importer_id,d.maturity_from_id
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d, com_pi_item_details f, wo_non_order_info_mst g
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id=c.id and c.id=f.pi_id and f.work_order_id=g.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and g.item_category=4 $company_id $issue_banking $supplier_id $type_id $currency_id $maturity_date  $com_accep_date $lc_sc_id $bank_accep_date $pending_cond  $btb_date_cond $import_source_cond
					GROUP BY
							a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,d.importer_id,d.maturity_from_id";
				}
				else if($cbo_item_category==11)
				{
					$sql="SELECT a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id, group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( d.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value,d.importer_id,d.maturity_from_id
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.item_category_id=11 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $bank_accep_date $pending_cond  $btb_date_cond $lc_sc_id $import_source_cond and d.id not in(Select a.id from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d  where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id and a.status_active=1 and a.is_deleted=0 and a.item_category_id=11 and  d.item_category=4 group by a.id)
					GROUP BY
							a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,d.importer_id,d.maturity_from_id";
				}
				else
				{
					if(!empty($cbo_item_category))
					{

						$category_con="and d.item_category_id in ($cbo_item_category)";
					}
					$sql="SELECT a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id, group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( d.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value,d.importer_id,d.maturity_from_id
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $category_con $lc_sc_id $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $bank_accep_date $pending_cond  $btb_date_cond $import_source_cond
					GROUP BY
							a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,d.importer_id,d.maturity_from_id";
				}

			}
			else
			{
				if($cbo_item_category==4)
				{
					$sql="SELECT a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id, group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( d.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, 1 as type, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value,d.importer_id,d.maturity_from_id
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d, com_import_payment e
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.item_category_id =4 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $bank_accep_date $lc_sc_id $payment_date $pending_cond  $btb_date_cond $import_source_cond
					GROUP BY
							a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,d.importer_id,d.maturity_from_id

					union all

					Select a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id, group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( g.item_category) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, 2 as type, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value,d.importer_id,d.maturity_from_id
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d, com_import_payment e,  com_pi_item_details f, wo_non_order_info_mst g
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id and c.id=f.pi_id and f.work_order_id=g.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and g.item_category=4 $company_id $issue_banking $supplier_id $type_id  $lc_sc_id $currency_id $maturity_date  $com_accep_date $bank_accep_date $payment_date $pending_cond  $btb_date_cond  $import_source_cond
					GROUP BY
							a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,d.importer_id,d.maturity_from_id";
				}
				elseif($cbo_item_category==11)
				{
					$sql="SELECT a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id, group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( d.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value,d.importer_id,d.maturity_from_id
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d, com_import_payment e
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.item_category_id=11 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $bank_accep_date $lc_sc_id  $payment_date $pending_cond  $btb_date_cond $import_source_cond and d.id not in(Select a.id from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d  where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id and a.status_active=1 and a.is_deleted=0 and a.item_category_id=11 and  d.item_category=4 group by a.id)
					GROUP BY
							a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,d.importer_id,d.maturity_from_id";
				}
				else
				{
					if(!empty($cbo_item_category))
					{

						$category_con=" and d.item_category_id in ($cbo_item_category)";
					}

					$sql="SELECT a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
					sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id, group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( d.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value,d.importer_id,d.maturity_from_id
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d, com_import_payment e
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $bank_accep_date $lc_sc_id $payment_date $pending_cond  $btb_date_cond $import_source_cond $category_con
					GROUP BY
							a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,d.importer_id,d.maturity_from_id";
				}

			}
		}
		else if($db_type==2)
		{
			if( $payment_date=="")
			{
				if($cbo_item_category==4)
				{
					$sql="SELECT a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.bank_acc_date, a.shipment_date, a.maturity_date, a.bank_ref,a.document_value,
					sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,
					LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_value) as lc_value,d.importer_id,d.maturity_from_id
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.item_category_id=4 $lc_sc_id $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date $com_accep_date $bank_accep_date $pending_cond $btb_date_cond $import_source_cond
					GROUP BY
							a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date,a.bank_acc_date,a.shipment_date, a.maturity_date, a.bank_ref,a.document_value,b.import_invoice_id,d.importer_id,d.maturity_from_id

					union all

					Select a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.bank_acc_date, a.shipment_date, a.maturity_date, a.bank_ref,a.document_value,
					sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,
					LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max( g.item_category) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_value) as lc_value,d.importer_id,d.maturity_from_id
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d, com_pi_item_details f, wo_non_order_info_mst g
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and c.id=f.pi_id and f.work_order_id=g.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and g.item_category=4 $company_id $issue_banking $supplier_id $type_id $currency_id $maturity_date $com_accep_date $lc_sc_id $bank_accep_date $pending_cond $btb_date_cond $import_source_cond
					GROUP BY
							a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.bank_acc_date,a.shipment_date, a.maturity_date, a.bank_ref,a.document_value,b.import_invoice_id,d.importer_id,d.maturity_from_id";
				}
				elseif($cbo_item_category==11)
				{
					$sql="SELECT a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.bank_acc_date, a.shipment_date, a.maturity_date, a.bank_ref,a.document_value,
					sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,
					LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_value) as lc_value,d.importer_id,d.maturity_from_id
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.item_category_id=11 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date $com_accep_date $bank_accep_date $pending_cond $btb_date_cond $import_source_cond and d.id not in(Select a.id from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d  where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id $lc_sc_id and a.status_active=1 and a.is_deleted=0 and c.item_category_id=11 and d.item_category=4 group by a.id)
					GROUP BY
							a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.bank_acc_date,a.shipment_date,a.maturity_date, a.bank_ref,a.document_value,b.import_invoice_id,d.importer_id,d.maturity_from_id";
				}
				else
				{
					if(!empty($cbo_item_category))
					{

						$category_con=" and c.item_category_id in ($cbo_item_category)";
					}

					$sql="SELECT a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.bank_acc_date, a.shipment_date, a.maturity_date, a.bank_ref,a.document_value,
					sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id, 
					LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_value) as lc_value,d.importer_id,d.maturity_from_id
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date $com_accep_date $bank_accep_date $lc_sc_id $pending_cond $btb_date_cond $import_source_cond $category_con
					GROUP BY
							a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.bank_acc_date, a.shipment_date, a.maturity_date, a.bank_ref,a.document_value,b.import_invoice_id,d.importer_id,d.maturity_from_id";
				}

			}
			else
			{
				if($cbo_item_category==4)
				{
					$sql="SELECT a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.bank_acc_date, a.shipment_date, a.maturity_date, a.bank_ref,a.document_value,
					sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id, 
					LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_value) as lc_value,d.importer_id,d.maturity_from_id
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d ,com_import_payment e
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.item_category_id=4 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $lc_sc_id $bank_accep_date  $payment_date $pending_cond $btb_date_cond $import_source_cond
					GROUP BY
							a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.bank_acc_date, a.shipment_date, a.maturity_date, a.bank_ref,a.document_value,b.import_invoice_id,d.importer_id,d.maturity_from_id

					union all

					Select a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.bank_acc_date, a.shipment_date, a.maturity_date, a.bank_ref,a.document_value,
					sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id, 
					LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max( g.item_category) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_value) as lc_value,d.importer_id,d.maturity_from_id
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d ,com_import_payment e, com_pi_item_details f, wo_non_order_info_mst g
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and c.id=f.pi_id and f.work_order_id=g.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and g.item_category=4 $company_id $issue_banking $supplier_id $type_id $currency_id $lc_sc_id $maturity_date  $com_accep_date $bank_accep_date  $payment_date $pending_cond $btb_date_cond $import_source_cond
					GROUP BY
							a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.bank_acc_date, a.shipment_date, a.maturity_date, a.bank_ref,a.document_value,b.import_invoice_id,d.importer_id,d.maturity_from_id";
				}
				elseif($cbo_item_category==11)
				{
					$sql="SELECT a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.bank_acc_date, a.shipment_date,a.maturity_date, a.bank_ref,a.document_value,
					sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,
					LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_value) as lc_value,d.importer_id,d.maturity_from_id
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d ,com_import_payment e
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.item_category_id=11 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $lc_sc_id $bank_accep_date  $payment_date $pending_cond $btb_date_cond $import_source_cond and d.id not in(Select a.id from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d  where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id and a.status_active=1 and a.is_deleted=0 and c.item_category_id=11 and d.item_category=4 group by a.id)
					GROUP BY
							a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.bank_acc_date, a.shipment_date, a.maturity_date, a.bank_ref,a.document_value,b.import_invoice_id,d.importer_id,d.maturity_from_id";
				}
				else
				{
					if(!empty($cbo_item_category))
					{

						$category_con=" and c.item_category_id in ($cbo_item_category)";
					}

					$sql="SELECT a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.bank_acc_date, a.shipment_date, a.maturity_date, a.bank_ref,a.document_value,
					sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id, 
					LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id,  max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_value) as lc_value,d.importer_id,d.maturity_from_id
					from
							com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d ,com_import_payment e
					where
							a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id $lc_sc_id $issue_banking $supplier_id $type_id $currency_id $maturity_date  $com_accep_date $bank_accep_date  $payment_date $pending_cond $btb_date_cond $import_source_cond $category_con
					GROUP BY
							a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.bank_acc_date,a.shipment_date, a.maturity_date, a.bank_ref,a.document_value,b.import_invoice_id,d.importer_id,d.maturity_from_id";
				}

			}
		}
		//echo $sql;//die;
		
		$pi_id_all='';
		$sql_data = sql_select($sql);
		foreach($sql_data as $row)
		{
			if($pending_type>0)
			{
				if($row[csf('current_acceptance_value')]>$pay_data_arr[$row[csf('id')]]["accepted_ammount"])
				{
					$result_arr[$row[csf('id')]]['id']=$row[csf('id')];
					$result_arr[$row[csf('id')]]['company_id']=$row[csf('importer_id')];
					$result_arr[$row[csf('id')]]['invoice_no']=$row[csf('invoice_no')];
					$result_arr[$row[csf('id')]]['invoice_date']=$row[csf('invoice_date')];
					$result_arr[$row[csf('id')]]['doc_rcv_date']=$row[csf('doc_rcv_date')];
					$result_arr[$row[csf('id')]]['bank_acc_date']=$row[csf('bank_acc_date')];
					$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
					$result_arr[$row[csf('id')]]['shipment_date']=$row[csf('shipment_date')];
					$result_arr[$row[csf('id')]]['maturity_date']=$row[csf('maturity_date')];
					$result_arr[$row[csf('id')]]['current_acceptance_value']=$row[csf('current_acceptance_value')];
					$result_arr[$row[csf('id')]]['import_invoice_id']=$row[csf('import_invoice_id')];
					$result_arr[$row[csf('id')]]['pi_number']=$row[csf('pi_number')];
					$result_arr[$row[csf('id')]]['pi_date']=$row[csf('pi_date')];
					$result_arr[$row[csf('id')]]['pi_mst_id']=$row[csf('pi_mst_id')];
					$result_arr[$row[csf('id')]]['btb_lc_id']=$row[csf('btb_lc_id')];
					$result_arr[$row[csf('id')]]['lc_type_id']=$row[csf('lc_type_id')];
					$result_arr[$row[csf('id')]]['issuing_bank_id']=$row[csf('issuing_bank_id')];
					$result_arr[$row[csf('id')]]['lc_number']=$row[csf('lc_number')];
					$result_arr[$row[csf('id')]]['lc_date']=$row[csf('lc_date')];
					$result_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
					$result_arr[$row[csf('id')]]['item_category_id']=$row[csf('item_category_id')];
					$result_arr[$row[csf('id')]]['tenor']=$row[csf('tenor')];
					$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
					$result_arr[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
					$result_arr[$row[csf('id')]]['goods_rcv_status']=$row[csf('goods_rcv_status')];
					$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
					$result_arr[$row[csf('id')]]['maturity_from_id']=$row[csf('maturity_from_id')];
					$result_arr[$row[csf('id')]]['document_value']=$row[csf('document_value')];
					$pi_id_all.=$row[csf('pi_mst_id')].',';
					$btb_id_all[$row[csf('btb_lc_id')]]=$row[csf('btb_lc_id')];
				}
			}
			else
			{
				$result_arr[$row[csf('id')]]['id']=$row[csf('id')];
				$result_arr[$row[csf('id')]]['company_id']=$row[csf('importer_id')];
				$result_arr[$row[csf('id')]]['invoice_no']=$row[csf('invoice_no')];
				$result_arr[$row[csf('id')]]['invoice_date']=$row[csf('invoice_date')];
				$result_arr[$row[csf('id')]]['doc_rcv_date']=$row[csf('doc_rcv_date')];
				$result_arr[$row[csf('id')]]['bank_acc_date']=$row[csf('bank_acc_date')];
				$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
				$result_arr[$row[csf('id')]]['shipment_date']=$row[csf('shipment_date')];
				$result_arr[$row[csf('id')]]['maturity_date']=$row[csf('maturity_date')];
				$result_arr[$row[csf('id')]]['current_acceptance_value']=$row[csf('current_acceptance_value')];
				$result_arr[$row[csf('id')]]['import_invoice_id']=$row[csf('import_invoice_id')];
				$result_arr[$row[csf('id')]]['pi_number']=$row[csf('pi_number')];
				$result_arr[$row[csf('id')]]['pi_date']=$row[csf('pi_date')];
				$result_arr[$row[csf('id')]]['pi_mst_id']=$row[csf('pi_mst_id')];
				$result_arr[$row[csf('id')]]['btb_lc_id']=$row[csf('btb_lc_id')];
				$result_arr[$row[csf('id')]]['issuing_bank_id']=$row[csf('issuing_bank_id')];
				$result_arr[$row[csf('id')]]['lc_number']=$row[csf('lc_number')];
				$result_arr[$row[csf('id')]]['lc_date']=$row[csf('lc_date')];
				$result_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
				$result_arr[$row[csf('id')]]['item_category_id']=$row[csf('item_category_id')];
				$result_arr[$row[csf('id')]]['tenor']=$row[csf('tenor')];
				$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
				$result_arr[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
				$result_arr[$row[csf('id')]]['goods_rcv_status']=$row[csf('goods_rcv_status')];
				$result_arr[$row[csf('id')]]['maturity_from_id']=$row[csf('maturity_from_id')];
				$result_arr[$row[csf('id')]]['document_value']=$row[csf('document_value')];
				$pi_id_all.=$row[csf('pi_mst_id')].',';
				$btb_id_all[$row[csf('btb_lc_id')]]=$row[csf('btb_lc_id')];

			}
		}
		unset($sql_data);

		$pi_id_all=array_unique(explode(",",chop($pi_id_all,',')));
		$pi_id_in=where_con_using_array($pi_id_all,0,'c.id');
		$pi_sql = "SELECT a.ID, c.id as PI_ID, c.IMPORT_PI, c.GOODS_RCV_STATUS, c.ITEM_CATEGORY_ID, d.QUANTITY, d.RATE, d.UOM, d.ITEM_GROUP, d.COUNT_NAME, d.YARN_COMPOSITION_ITEM1, d.FABRIC_CONSTRUCTION, d.FABRIC_COMPOSITION
		from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c, com_pi_item_details d
		where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id and c.id=d.pi_id $pi_id_in";
		// echo $pi_sql;
		$pi_result=sql_select($pi_sql);
		foreach ($pi_result as $value)
		{
			$exportPiSuppArr[$value["ID"]] = $value["IMPORT_PI"];
			$piGoodRcvArr[$value["PI_ID"]] = $value["GOODS_RCV_STATUS"];
			$pi_data_arr[$value["ID"]]['pi_qnty']+=$value["QUANTITY"];
			$pi_data_arr[$value["ID"]]['pi_rate'].=number_format($value["RATE"],2).", ";
			$pi_data_arr[$value["ID"]]['pi_uom'].=$value["UOM"].",";

			if($value["ITEM_CATEGORY_ID"]==1)
			{
				$pi_data_arr[$value["ID"]]['goods_description'].=$yarn_count[$value['COUNT_NAME']]." ".$yarn_count[$value['COUNT_NAME']].", ";
			}
			else if($value["ITEM_CATEGORY_ID"]==2 || $value["ITEM_CATEGORY_ID"]==3 || $value["ITEM_CATEGORY_ID"]==13 || $value["ITEM_CATEGORY_ID"]==14)
			{
				$pi_data_arr[$value["ID"]]['goods_description'].=$value['FABRIC_CONSTRUCTION']." ".$value['FABRIC_COMPOSITION'].", ";
			}
			else
			{
				$pi_data_arr[$value["ID"]]['goods_description'].=$item_group_library[$value["ITEM_GROUP"]].", ";
			}
		}
		unset($pi_result);

		$btb_id_in=where_con_using_array($btb_id_all,0,'a.import_mst_id');
		$lc_sc_sql="SELECT a.import_mst_id as BTB_ID, export_lc_no as LC_SC_NO, a.lc_sc_id as LC_SC_ID, a.is_lc_sc as IS_LC_SC, b.internal_file_no as INTERNAL_FILE_NO, b.BUYER_NAME 
		from com_btb_export_lc_attachment a, com_export_lc b 
		where a.lc_sc_id=b.id $btb_id_in and a.is_lc_sc=0 and a.status_active=1 and b.beneficiary_name in($cbo_company)
		union all 
		SELECT a.import_mst_id as BTB_ID, contract_no as LC_SC_NO, a.lc_sc_id as LC_SC_ID, a.is_lc_sc as IS_LC_SC, b.internal_file_no as INTERNAL_FILE_NO , b.BUYER_NAME 
		from com_btb_export_lc_attachment a, com_sales_contract b 
		where a.lc_sc_id=b.id $btb_id_in and a.is_lc_sc=1 and a.status_active=1 and b.beneficiary_name in($cbo_company)";
		// echo $lc_sc_sql;
		$lc_sc_data=sql_select($lc_sc_sql);
		$lc_sc_no=array();
		foreach($lc_sc_data as $row)
		{
			$exp_lc_sc_data[$row["BTB_ID"]]["lc_sc_id"]=$row["LC_SC_ID"];
			$exp_lc_sc_data[$row["BTB_ID"]]["is_lc_sc"]=$row["IS_LC_SC"];
			$exp_lc_sc_data[$row["BTB_ID"]]["lc_sc_no"].=$row["LC_SC_NO"].', ';
			$exp_lc_sc_data[$row["BTB_ID"]]["buyer_name"].=$row["BUYER_NAME"].', ';
			if($row["IS_LC_SC"]==0)
			{
				$file_reference_lc_arr[$row["BTB_ID"]]=$row["INTERNAL_FILE_NO"];
			}
			else
			{
				$file_reference_sales_arr[$row["BTB_ID"]]=$row["INTERNAL_FILE_NO"];
			}
		}
		unset($lc_sc_data);
		ob_start();		
		?>
		<style>
			.wrd_brk{word-break: break-all;}
			.center{text-align: center;}
			.right{text-align: right;}
		</style>
		<div align="center" style="height:auto; width:2518px; margin:0 auto; padding:0;">
			<table width="2600px" align="center">
				<?
				$company_library=sql_select("SELECT id, COMPANY_NAME from lib_company where id in($cbo_company)");
				foreach( $company_library as $row)
				{
					$company_name.=$row["COMPANY_NAME"].", ";
				}
				?>
				<tr>
					<td colspan="30" align="center" style="font-size:22px"><center><strong><? echo rtrim($company_name,", ");?></strong></center></td>
				</tr>
				<tr>
					<td colspan="30" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?> Report</u></strong></center></td>
				</tr>
			</table>
			<div style="width:2518px;">
				<table cellspacing="0" width="2600px" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="70" >COMPANY</th>
							<th width="100" >BANK</th>
							<th width="150" >LC/SC</th>
							<th width="100" >BUYER</th>
							<th width="100" >PI NO.</th>
							<th width="70" >PI DATE</th>
							<th width="70" >TENOR</th>
							<th width="70" >LC MODE</th>
							<th width="100" >SUPPLIER</th>
							<th width="150" >BTB LC</th>
							<th width="70" >L/C DATE</th>
							<th width="70" >BTB Value[$]</th>
							<th width="100" >ITEM CATEGORY</th>
							<th width="70" >QUANTITY</th>
							<th width="70" >UOM</th>
							<th width="70" >PRICE</th>
							<th width="150" >GOODS DESCRIPTION</th>
							<th width="70" >SHIPMENT DATE</th>
							<th width="70" >GOOD REC. DATE</th>
							<th width="70" >DOC RCV. DATE</th>
							<th width="100" >INVOICE NO.</th>
							<th width="70" >INVOICE DATE</th>
							<th width="70" >INVOICE VALUE</th>					
							<th width="70" >BANK RECEIVED DATE</th>
							<th width="70" >IFDBC NO.</th>
							<th width="70" >IFDBC VALUE</th>
							<th width="70" >ACCEPTANCE DATE</th>
							<th width="70" >MATURITY DATE</th>
							<th >PAYMENT DATE</th>					   
						</tr>
					</thead>
				</table>
				</div>
				<div style="width:2618px; overflow-y: scroll; max-height:300px;" id="scroll_body">
				<table cellspacing="0" width="2600px" border="1" rules="all" class="rpt_table" id="tbl_body" >
				<?
					$i=1;
					$lc_check=array();
					foreach( $result_arr as $row)
					{
						if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
			
						$import_invoice_id=$row[('import_invoice_id')];
						$item_id=$row[('item_category_id')];
						$curr_id=$row[('currency_id')];
						$pi_id=$row[('pi_mst_id')];

						$buyer_arr=array_unique(explode(",",chop($exp_lc_sc_data[$row[('btb_lc_id')]]["buyer_name"],', ')));
						$buyer_name='';
						foreach($buyer_arr as $val){ $buyer_name.=$buyerArr[$val].", "; }

						$receive_qnty=$receive_value=0;$all_wo_pi_id=$receive_basis="";
						$pi_id_all=explode(",",$row[('pi_mst_id')]);
						foreach($pi_id_all as $piid)
						{
							if($piGoodRcvArr[$piid]==1)
							{
								$pi_wo_ids[$row["ID"]].=$row["WORK_ORDER_ID"].",";
								$wo_id_arr=explode(",",chop($pi_wo_ids[$piid],","));
								foreach($wo_id_arr as $wo_id)
								{
									$receive_qnty += $receive_data_arr[$wo_id][2]["cons_quantity"];
									$receive_value += $receive_data_arr[$wo_id][2]["cons_amount"];
									$all_wo_pi_id.=$wo_id.",";
								}
								$receive_basis=2;
							}
							else
							{
								$receive_qnty += $receive_data_arr[$piid][1]["cons_quantity"];
								$receive_value += $receive_data_arr[$piid][1]["cons_amount"];
								$all_wo_pi_id.=$piid.",";
								$receive_basis=1;
							}							
							$receive_Return_qnty += $receive_Return_data_arr[$piid]["cons_quantity"];
							$receive_Return_value += $receive_Return_data_arr[$piid]["cons_amount"];
						}
						// $receive_actual_value=$receive_value-$receive_Return_value;
						// $balance_value=$row[('lc_value')]-$receive_actual_value;

						$uom_arr=array_unique(explode(",",chop($pi_data_arr[$row['btb_lc_id']]["pi_uom"],',')));
						$pi_uom="";
						foreach($uom_arr as $val){ $pi_uom.=$unit_of_measurement[$val].", "; }
			
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" class="center"><? echo $i; ?></td>
							<td width="70" class="center"><p><? echo $companyArr[$row['company_id']]; ?></p></td>
							<td width="100"><p><? echo $issueBankrArr[$row['issuing_bank_id']]; ?></p></td>
							<td width="150"><p><? echo chop($exp_lc_sc_data[$row['btb_lc_id']]["lc_sc_no"],", ");?></p></td>
							<td width="100"><p><? echo chop($buyer_name,", ");?></p></td>
							<td width="100"><p><? echo $row['pi_number'];?></p></td>
							<td width="70" class="center"><p><? echo $row['pi_date'];?></p></td>
							<td width="70" class="center"><p><? if($row['tenor']){ echo $row['tenor'].' Days'; }?></p></td>
							<td width="70" class="center"><p><? echo $maturity_from[$row['maturity_from_id']];?></p></td>
							<td width="100"><p><? if($exportPiSuppArr[$row[('btb_lc_id')]]==1) echo $companyArr[$row[('supplier_id')]]; else echo $supplierArr[$row[('supplier_id')]];?></p></td>
							<td width="150"><p><? echo trim($row['lc_number']); ?></p></td>
							<td width="70" class="center"><p><? echo $row['lc_date']; ?></p></td>
							<td width="70" class="right"><p><? echo number_format($row['lc_value'],2); $tot_lc_value+=$row['lc_value'];?></p></td>
							<td width="100"><p><? echo $item_category[$row['item_category_id']]; ?></p></td>
							<td width="70" class="right"><p><? echo $pi_data_arr[$row['btb_lc_id']]["pi_qnty"]; ?></p></td>
							<td width="70"><p><? echo chop($pi_uom,", "); ?></p></td>
							<td width="70"><p><? echo chop($pi_data_arr[$row['btb_lc_id']]["pi_rate"],", "); ?></p></td>
							<td width="150"><p><? echo chop($pi_data_arr[$row['btb_lc_id']]["goods_description"],", ");?></p></td>
							<td width="70" class="center"><p><? echo $row['shipment_date']; ?></p></td>
							<td width="70" align="center"><p><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage_inHouse_date('".chop($all_wo_pi_id,",")."','".$receive_basis."','$receive_value','$receive_qnty','$item_id','pi_rec_details','PI Details','".$import_invoice_id."');\">"."View"."</a>"; ?></p></td>
							<td width="70"></td>
							<td width="100"><p><? echo $row['invoice_no']; ?></p></td>
							<td width="70" class="center"><p><? echo $row['invoice_date'];?></p></td>    
							<td width="70"><p><? echo number_format($row[('current_acceptance_value')],2); $tot_bill_value+=$row[('current_acceptance_value')];?></p></td>    
							<td width="70" class="center"><p><? echo $row['doc_rcv_date'];?></p></td>    
							<td width="70"><p><? echo $row[('bank_ref')]; ?></p></td>
							<td width="70" class="right"><p><? echo number_format($row[('document_value')],2); $tot_document_value+=$row[('document_value')];?></p></td>
							<td width="70" class="center"><p><?echo $row[('bank_acc_date')]; ?></p></td>
							<td width="70" class="center"><p><? echo $row[('maturity_date')]; ?></p></td>
							<td  class="center"><p><? if($pay_data_arr[$row[('id')]]["payment_date"]!="" && $pay_data_arr[$row[('id')]]["payment_date"]!="0000-00-00") echo change_date_format($pay_data_arr[$row[('id')]]["payment_date"]); ?></p></td>
						</tr>
						<?
						$i++;
					}
				?>
				</table>
			</div>
		</div>
		<?

	}
	if( $report_type==6)
	{
		$user_arr = return_library_array("select id,user_name from user_passwd ","id","user_name");
		$issueBankrArr = return_library_array("select id,bank_name from lib_bank ","id","bank_name");
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		if($txt_lc_sc!="")
		{
			$sql_btb="SELECT a.import_mst_id as IMPORT_MST_ID
			from com_btb_export_lc_attachment a, com_export_lc b 
			where a.lc_sc_id=b.id and a.is_lc_sc=0 and b.export_lc_no='$txt_lc_sc' and b.beneficiary_name in($cbo_company)
			union all
			select a.import_mst_id as IMPORT_MST_ID
			from com_btb_export_lc_attachment a, com_sales_contract b 
			where a.lc_sc_id=b.id and a.is_lc_sc=1 and b.contract_no='$txt_lc_sc' and b.beneficiary_name in ($cbo_company)";
			//echo $sql_lc_sc;die;
			$sql_btb_result=sql_select($sql_btb);
			if(count($sql_btb_result)==0)
			{
				echo "<div class='alert alert-danger' style='text-align: center; width:50%;'><b>Data Not Found</b></div>";
				die;
			}

			foreach($sql_btb_result as $row)
			{
				$btb_id.=$row["IMPORT_MST_ID"].",";
			}
			$btb_id=rtrim($btb_id,",");
			if($txt_lc_id!=""){ $txt_lc_id.=",".$btb_id; }else{ $txt_lc_id=$btb_id; }
			unset($sql_btb_result);
		}
		//cbo_source_id
		$cbo_source_id = str_replace("'","",$cbo_source_id);
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

		
		$sql_lc_sc="select a.import_mst_id as IMPORT_MST_ID, a.lc_sc_id as LC_SC_ID, a.is_lc_sc as IS_LC_SC, a.import_mst_id as IMPORT_MST_ID, b.internal_file_no as INTERNAL_FILE_NO 
		from com_btb_export_lc_attachment a, com_export_lc b 
		where a.lc_sc_id=b.id and a.is_lc_sc=0 and b.beneficiary_name in($cbo_company)
		union all
		select a.import_mst_id as IMPORT_MST_ID, a.lc_sc_id as LC_SC_ID, a.is_lc_sc as IS_LC_SC, a.import_mst_id as IMPORT_MST_ID, b.internal_file_no as INTERNAL_FILE_NO 
		from com_btb_export_lc_attachment a, com_sales_contract b 
		where a.lc_sc_id=b.id and a.is_lc_sc=1 and b.beneficiary_name in($cbo_company)";
		//echo $sql_lc_sc;die;
		$sql_lc_sc_result=sql_select($sql_lc_sc);
		foreach($sql_lc_sc_result as $row)
		{
			$exp_lc_sc_data[$row["IMPORT_MST_ID"]]["lc_sc_id"]=$row["LC_SC_ID"];
			$exp_lc_sc_data[$row["IMPORT_MST_ID"]]["is_lc_sc"]=$row["IS_LC_SC"];
			if($row["IS_LC_SC"]==0)
			{
				$file_reference_lc_arr[$row["IMPORT_MST_ID"]]=$row["INTERNAL_FILE_NO"];
			}
			else
			{
				$file_reference_sales_arr[$row["IMPORT_MST_ID"]]=$row["INTERNAL_FILE_NO"];
			}
		}
		unset($sql_lc_sc_result);
		
		
		$pay_data_sql=sql_select("select invoice_id, payment_date, adj_source as payment_head, accepted_ammount from com_import_payment where status_active=1 order by payment_date");
		$pay_data_arr=array();
		foreach($pay_data_sql as $row)
		{
			$pay_data_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
			$pay_data_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
			if($row[csf("payment_head")]==5)
			{
				$pay_data_arr[$row[csf("invoice_id")]]["margin"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("payment_head")]==6)
			{
				$pay_data_arr[$row[csf("invoice_id")]]["erq"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("payment_head")]==11)
			{
				$pay_data_arr[$row[csf("invoice_id")]]["std"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("payment_head")]==10)
			{
				$pay_data_arr[$row[csf("invoice_id")]]["cd"]+=$row[csf("accepted_ammount")];
			}
		}
		//echo "<pre>";print_r($pay_data_arr);die;
		unset($pay_data_sql);
		$pay_data_atsite_sql=sql_select("select invoice_id, payment_date, payment_head, adj_source, accepted_ammount from com_import_payment_com where status_active=1 order by payment_date");
		$pay_data_atsite_arr=array();
		foreach($pay_data_atsite_sql as $row)
		{
			$pay_data_atsite_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
			$pay_data_atsite_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
			if($row[csf("adj_source")]==5)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["margin"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("adj_source")]==6)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["erq"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("adj_source")]==11)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["std"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("adj_source")]==10)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["cd"]+=$row[csf("accepted_ammount")];
			}

			if($row[csf("payment_head")]==40)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["il"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("payment_head")]==45)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["bc"]+=$row[csf("accepted_ammount")];
			}
			elseif($row[csf("payment_head")]==70)
			{
				$pay_data_atsite_arr[$row[csf("invoice_id")]]["int"]+=$row[csf("accepted_ammount")];
			}
		}
		unset($pay_data_atsite_sql);
		
		$receive_sql=sql_select("select a.receive_basis as RECEIVE_BASIS, b.booking_id as BOOKING_ID, a.order_qnty as ORDER_QNTY, a.order_amount as ORDER_AMOUNT 
		from inv_transaction a, inv_receive_master b 
		where a.mst_id=b.id and a.transaction_type=1 and a.status_active=1 and a.company_id in($cbo_company)");
		$receive_data_arr=array();
		foreach($receive_sql as $row)
		{
			$receive_data_arr[$row["BOOKING_ID"]][$row["RECEIVE_BASIS"]]["cons_quantity"]+=$row["ORDER_QNTY"];
			$receive_data_arr[$row["BOOKING_ID"]][$row["RECEIVE_BASIS"]]["cons_amount"]+=$row["ORDER_AMOUNT"];
		}
		unset($receive_sql);

		$receive_Return_sql=sql_select("select pi_wo_batch_no,sum(cons_quantity) as cons_quantity, sum(cons_amount) as cons_amount from inv_transaction where transaction_type=3 and company_id in($cbo_company) group by pi_wo_batch_no");
		$receive_Return_data_arr=array();
		foreach($receive_Return_sql as $row)
		{
			$receive_Return_data_arr[$row[csf("pi_wo_batch_no")]]["cons_quantity"]=$row[csf("cons_quantity")];
			$receive_Return_data_arr[$row[csf("pi_wo_batch_no")]]["cons_amount"]=$row[csf("cons_amount")];
		}
		unset($receive_Return_sql);

		
		$category_con='';
		if ($cbo_company=='') $company_id =""; else $company_id =" and d.importer_id in($cbo_company) ";
		if ($cbo_issue==0) $issue_banking =""; else $issue_banking =" and d.issuing_bank_id=$cbo_issue ";
		if ($cbo_supplier==0) $supplier_id =""; else $supplier_id =" and d.supplier_id=$cbo_supplier ";
		if ($cbo_lc_type==0) $type_id =""; else $type_id =" and d.lc_type_id=$cbo_lc_type ";
		if ($cbo_currency==0) $currency_id =""; else $currency_id =" and d.currency_id=$cbo_currency ";  

		if ($txt_lc_id!="") $lc_sc_id =" and d.id in($txt_lc_id)";

		$item_category_id='';
		if($cbo_item_category!='')
		{
			$ids=$cbo_item_category;
			$ids_arr=explode(",",$ids);
			$id_all="";
			$entryForms=array();
			foreach($ids_arr as $values)
			{
				if($values==1 )
				{
					if(!in_array(165,$entryForms))
					{
						array_push($entryForms,"165");
					}
				}
				else if($values==2 || $values ==3 || $values==13 || $values ==14)
				{
					if(!in_array(166,$entryForms))
					{
						array_push($entryForms,"166");
					}
				}
				else if($values==5 || $values ==6 || $values==7 || $values ==23)
				{
					if(!in_array(227,$entryForms))
					{
						array_push($entryForms,"227");
					}
				}
				else if($values==4)
				{
					if(!in_array(167,$entryForms))
					{
						array_push($entryForms,"167");
					}
				}
				else if($values==12)
				{
					if(!in_array(168,$entryForms))
					{
						array_push($entryForms,"168");
					}
				}
				else if($values==24)
				{
					if(!in_array(169,$entryForms))
					{
						array_push($entryForms,"169");
					}
				}
				else if($values==25)
				{
					if(!in_array(170,$entryForms))
					{
						array_push($entryForms,"170");
					}
				}
				else if($values==30)
				{
					if(!in_array(197,$entryForms))
					{
						array_push($entryForms,"197");
					}
					//$item_category_id =" and d.pi_entry_form=197 ";
				}
				else if($values==31)
				{

					if(!in_array(171,$entryForms))
					{
						array_push($entryForms,"171");

					}
					//$item_category_id =" and d.pi_entry_form=171 ";
					//$entry_form =171;
				}
				else if($values!=1)
				{
					if(!in_array(172,$entryForms))
					{
						array_push($entryForms,"172");
					}
				}
			}
			foreach($entryForms as $value)
			{
				if($id_all=="") $id_all=$value;
				else $id_all.=",".$value;
			}
			if(!$id_all)$id_all=0;
			$item_category_id=" and d.pi_entry_form in($id_all)";
		}


		//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
		if($db_type==0) $txt_pending_date=change_date_format($txt_pending_date,"yyyy-mm-dd"); else if($db_type==2) $txt_pending_date=date("j-M-Y",strtotime($txt_pending_date));
		if($pending_type==0) $pending_cond="";
		if($pending_type==1) $pending_cond="";

		if($pending_type==2) { if($txt_pending_date!="") $pending_cond="and a.maturity_date>'$txt_pending_date'";}
		if($pending_type==3) { if($txt_pending_date!="")  $pending_cond="and a.maturity_date<='$txt_pending_date'";}
		//echo $pending_cond;die;

		if($db_type==2)
		{
			if( $from_date=="") $maturity_date=""; else $maturity_date= " and a.maturity_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
			if( $from_date_c=="" ) $com_accep_date=""; else $com_accep_date= " and a.company_acc_date between '".date("j-M-Y",strtotime($from_date_c))."' and '".date("j-M-Y",strtotime($to_date_c))."'";
			if( $from_date_b=="" ) $bank_accep_date=""; else $bank_accep_date= " and a.bank_acc_date between '".date("j-M-Y",strtotime($from_date_b))."' and '".date("j-M-Y",strtotime($to_date_b))."'";

			$payment_date=$payment_date2="";
			if( $from_date_p !="" && $to_date_p !="")  
			{
				$payment_date= " and e.payment_date between '".date("j-M-Y",strtotime($from_date_p))."' and '".date("j-M-Y",strtotime($to_date_p))."'";
				$payment_date2= " and a.bank_acc_date between '".date("j-M-Y",strtotime($from_date_p))."' and '".date("j-M-Y",strtotime($to_date_p))."'";
			}
			
			if( $txt_date_from_btb!="" &&  $txt_date_to_btb!="") $btb_date_cond= " and d.lc_date between '".date("j-M-Y",strtotime($txt_date_from_btb))."' and '".date("j-M-Y",strtotime($txt_date_to_btb))."'";else  $btb_date_cond="";
		}
		
		
		$wo_cat_cond="";
		if($cbo_item_category!='') $wo_cat_cond=" and a.item_category_id in($cbo_item_category)";
		$sql_wo_pi="SELECT a.id as ID, b.work_order_id as WORK_ORDER_ID from com_pi_master_details a, com_pi_item_details b
		where a.id=b.pi_id and a.importer_id in($cbo_company) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id>0 and a.goods_rcv_status=1 $wo_cat_cond";
		$sql_wo_pi_result=sql_select($sql_wo_pi);
		$pi_wo_ids=array();
		foreach($sql_wo_pi_result as $row)
		{
			if($pi_wo_check[$row["ID"]][$row["WORK_ORDER_ID"]]=="")
			{
				$pi_wo_check[$row["ID"]][$row["WORK_ORDER_ID"]]=$row["WORK_ORDER_ID"];
				$pi_wo_ids[$row["ID"]].=$row["WORK_ORDER_ID"].",";
			}
		}
		unset($sql_wo_pi_result);
		if($addStringMulti!=""){ $import_source_cond = " and d.lc_category in($addStringMulti)";}else{ $import_source_cond = "";}
		$i=1;

		if( $payment_date=="")
		{
			if($cbo_item_category==4)
			{
				$sql="SELECT a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date,  a.company_acc_date,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,
				LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date, a.inserted_by, b.is_lc
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
				where a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.item_category_id=4 and b.is_lc=1 $lc_sc_id $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date $com_accep_date $bank_accep_date $pending_cond $btb_date_cond $import_source_cond
				GROUP BY a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date, a.inserted_by, b.is_lc
				union all
				Select a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date, a.company_acc_date, a.bank_acc_date, a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,
				LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( g.item_category) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by, b.is_lc
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d, com_pi_item_details f, wo_non_order_info_mst g
				where a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and c.id=f.pi_id and f.work_order_id=g.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and g.item_category=4 and b.is_lc=1 $company_id $issue_banking $supplier_id $type_id $currency_id $maturity_date $com_accep_date $lc_sc_id $bank_accep_date $pending_cond $btb_date_cond $import_source_cond
				GROUP BY a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date, a.inserted_by, b.is_lc
				union all
				SELECT a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date,  a.company_acc_date,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,
				LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date, a.inserted_by, b.is_lc
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
				where a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.item_category_id=4 and b.is_lc=2 $lc_sc_id $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date $com_accep_date $bank_accep_date $pending_cond $btb_date_cond $import_source_cond
				GROUP BY a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date, a.inserted_by, b.is_lc";
			}
			elseif($cbo_item_category==11)
			{
				$sql="Select a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date, a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,
				LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date, a.inserted_by
				from
						com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
				where
						a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.item_category_id=11 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date $com_accep_date $bank_accep_date $pending_cond $btb_date_cond $import_source_cond and d.id not in(Select a.id from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d  where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id $lc_sc_id and a.status_active=1 and a.is_deleted=0 and c.item_category_id=11 and d.item_category=4 group by a.id)
				GROUP BY
						a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date,a.inserted_by";
			}
			else
			{
				if(!empty($cbo_item_category))
				{

					$category_con=" and c.item_category_id in ($cbo_item_category)";
				}

			    $sql="SELECT a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date, a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id, 
				LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by, b.is_lc
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
				where a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.is_lc=1 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date $com_accep_date $bank_accep_date $lc_sc_id $pending_cond $btb_date_cond $import_source_cond $category_con
				GROUP BY a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date,a.inserted_by, b.is_lc
				union all 
				SELECT a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date, a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id, 
				LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by, b.is_lc
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
				where a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.is_lc=2  $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date $com_accep_date $bank_accep_date $lc_sc_id $pending_cond $btb_date_cond $import_source_cond $category_con
				GROUP BY a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date,a.inserted_by, b.is_lc";
			}

		}
		else
		{
			if($cbo_item_category==4)
			{
				$sql="Select a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date, a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id, 
				LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by, b.is_lc
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d ,com_import_payment e
				where a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.item_category_id=4 and b.is_lc=1 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $lc_sc_id $bank_accep_date  $payment_date $pending_cond $btb_date_cond $import_source_cond
				GROUP BY a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date,a.inserted_by, b.is_lc
				union all
				Select a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date, a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id, 
				LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( g.item_category) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by, b.is_lc
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d ,com_import_payment e, com_pi_item_details f, wo_non_order_info_mst g
				where a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and c.id=f.pi_id and f.work_order_id=g.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and g.item_category=4 and b.is_lc=1 $company_id $issue_banking $supplier_id $type_id $currency_id $lc_sc_id $maturity_date  $com_accep_date $bank_accep_date  $payment_date $pending_cond $btb_date_cond $import_source_cond
				GROUP BY a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date,a.inserted_by, b.is_lc
				union all
				Select a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date, a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id, 
				LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by, b.is_lc
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
				where a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.item_category_id=4 and b.is_lc=2 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $lc_sc_id $bank_accep_date  $payment_date2 $pending_cond $btb_date_cond $import_source_cond
				GROUP BY a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date,a.inserted_by, b.is_lc";
			}
			elseif($cbo_item_category==11)
			{
				$sql="SELECT a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date, a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,
				LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by, b.is_lc
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d ,com_import_payment e 
				where
				a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.item_category_id=11 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $lc_sc_id $bank_accep_date  $payment_date $pending_cond $btb_date_cond $import_source_cond and b.is_lc=1 and d.id not in(Select a.id from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d  where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id and a.status_active=1 and a.is_deleted=0 and c.item_category_id=11 and d.item_category=4 group by a.id)
				GROUP BY a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date,a.inserted_by, b.is_lc

				union all
				Select a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date, a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,
				LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date,a.inserted_by, b.is_lc
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d 
				where a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.item_category_id=11 $company_id $issue_banking $supplier_id $type_id $currency_id $item_category_id $maturity_date  $com_accep_date $lc_sc_id $bank_accep_date  $payment_date2 $pending_cond $btb_date_cond $import_source_cond and b.is_lc=2 and d.id not in(Select a.id from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d  where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id and a.status_active=1 and a.is_deleted=0 and c.item_category_id=11 and d.item_category=4 group by a.id)
				GROUP BY a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date,a.inserted_by, b.is_lc";
			}
			else
			{
				if(!empty($cbo_item_category))
				{

					$category_con=" and c.item_category_id in ($cbo_item_category)";
				}

				$sql="SELECT a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id, 
				LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date, a.inserted_by, b.is_lc
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d ,com_import_payment e
				where a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.is_lc=1 $company_id $lc_sc_id $issue_banking $supplier_id $type_id $currency_id $maturity_date  $com_accep_date $bank_accep_date  $payment_date $pending_cond $btb_date_cond $import_source_cond $category_con
				GROUP BY a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date, a.inserted_by,b.is_lc
				union all

				SELECT a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id, 
				LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date, a.inserted_by, b.is_lc
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d ,com_import_payment_com e
				where a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.is_lc=1 $company_id $lc_sc_id $issue_banking $supplier_id $type_id $currency_id $maturity_date  $com_accep_date $bank_accep_date  $payment_date $pending_cond $btb_date_cond $import_source_cond $category_con
				GROUP BY a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date, a.inserted_by,b.is_lc
				union all 
				
				Select a.id,  a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id, 
				LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( c.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category, max(d.payterm_id) as payterm_id, max(d.lc_value) as lc_value, a.release_date, a.inserted_by,b.is_lc
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d 
				where a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.is_lc=2 $company_id $lc_sc_id $issue_banking $supplier_id $type_id $currency_id $maturity_date  $com_accep_date $bank_accep_date  $payment_date2 $pending_cond $btb_date_cond $import_source_cond $category_con
				GROUP BY a.id,a.invoice_no, a.invoice_date, a.doc_rcv_date, a.local_doc_send_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.ready_to_approved, a.approved, a.bank_ref,b.import_invoice_id, a.release_date, a.inserted_by, b.is_lc";
			}

		}

		// echo $sql;die;		
		$exportPiSupp = sql_select("select c.import_pi, a.id from com_btb_lc_master_details a , com_btb_lc_pi b , com_pi_master_details c where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id");
		foreach ($exportPiSupp as $value)
		{
			$exportPiSuppArr[$value[csf("id")]] = $value[csf("import_pi")];
		}
		
		$category_con='';
		if(!empty($cbo_item_category))
		{
			$category_con=" and c.item_category_id in ($cbo_item_category)";
		}
	
		if($db_type==2)
		{
			$lc_item_category_sql=sql_select("Select  LISTAGG( c.item_category_id, ',') WITHIN GROUP (ORDER BY c.item_category_id) as item_category_id , d.id from  com_btb_lc_master_details d,com_btb_lc_pi b, com_pi_item_details c where d.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and d.status_active=1 and b.status_active=1 and c.status_active=1 $category_con $company_id group by d.id");
		}
		else if($db_type==0)
		{
			$lc_item_category_sql=sql_select("Select  group_concat( c.item_category_id) as item_category_id , d.id from  com_btb_lc_master_details d,com_btb_lc_pi b, com_pi_item_details c where d.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and d.status_active=1 and b.status_active=1 and c.status_active=1 $category_con $company_id group by d.id");
		}
		$item_category_data=array();
		foreach($lc_item_category_sql as $row)
		{
			$item_category_data[$row[csf("id")]]['item_category_id']=$row[csf("item_category_id")];
		}
		//print_r($item_category_data);, a.doc_rcv_date, a.local_doc_send_date
		$pi_id_all='';
		$sql_data = sql_select($sql);
		foreach($sql_data as $row)
		{
			if($row[csf('is_lc')]==1)
			{
				if($pending_type>0)
				{
					if($row[csf('current_acceptance_value')]>$pay_data_arr[$row[csf('id')]]["accepted_ammount"])
					{
						$result_arr[$row[csf('id')]]['id']=$row[csf('id')];
						$result_arr[$row[csf('id')]]['invoice_no']=$row[csf('invoice_no')];
						$result_arr[$row[csf('id')]]['invoice_date']=$row[csf('invoice_date')];
						$result_arr[$row[csf('id')]]['doc_rcv_date']=$row[csf('doc_rcv_date')];
						$result_arr[$row[csf('id')]]['local_doc_send_date']=$row[csf('local_doc_send_date')];
						$result_arr[$row[csf('id')]]['company_acc_date']=$row[csf('company_acc_date')];
						$result_arr[$row[csf('id')]]['bank_acc_date']=$row[csf('bank_acc_date')];
						$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
						$result_arr[$row[csf('id')]]['shipment_date']=$row[csf('shipment_date')];
						$result_arr[$row[csf('id')]]['eta_date']=$row[csf('eta_date')];
						$result_arr[$row[csf('id')]]['bill_no']=$row[csf('bill_no')];
						$result_arr[$row[csf('id')]]['feeder_vessel']=$row[csf('feeder_vessel')];
						$result_arr[$row[csf('id')]]['mother_vessel']=$row[csf('mother_vessel')];
						$result_arr[$row[csf('id')]]['container_no']=$row[csf('container_no')];
						$result_arr[$row[csf('id')]]['pkg_quantity']=$row[csf('pkg_quantity')];
						$result_arr[$row[csf('id')]]['bill_of_entry_no']=$row[csf('bill_of_entry_no')];
						$result_arr[$row[csf('id')]]['maturity_date']=$row[csf('maturity_date')];
						$result_arr[$row[csf('id')]]['acceptance_time']=$row[csf('acceptance_time')];
						$result_arr[$row[csf('id')]]['copy_doc_receive_date']=$row[csf('copy_doc_receive_date')];
						$result_arr[$row[csf('id')]]['doc_to_cnf']=$row[csf('doc_to_cnf')];
						$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
						$result_arr[$row[csf('id')]]['current_acceptance_value']=$row[csf('current_acceptance_value')];
						$result_arr[$row[csf('id')]]['import_invoice_id']=$row[csf('import_invoice_id')];
						$result_arr[$row[csf('id')]]['pi_number']=$row[csf('pi_number')];
						$result_arr[$row[csf('id')]]['pi_date']=$row[csf('pi_date')];
						$result_arr[$row[csf('id')]]['pi_mst_id']=$row[csf('pi_mst_id')];
						$result_arr[$row[csf('id')]]['btb_lc_id']=$row[csf('btb_lc_id')];
						$result_arr[$row[csf('id')]]['lc_type_id']=$row[csf('lc_type_id')];
						$result_arr[$row[csf('id')]]['issuing_bank_id']=$row[csf('issuing_bank_id')];
						$result_arr[$row[csf('id')]]['lc_number']=$row[csf('lc_number')];
						$result_arr[$row[csf('id')]]['lc_date']=$row[csf('lc_date')];
						$result_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
						$result_arr[$row[csf('id')]]['lca_no']=$row[csf('lca_no')];
						$result_arr[$row[csf('id')]]['item_category_id']=$row[csf('item_category_id')];
						$result_arr[$row[csf('id')]]['tenor']=$row[csf('tenor')];
						$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
						$result_arr[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
						$result_arr[$row[csf('id')]]['lc_expiry_date']=$row[csf('lc_expiry_date')];
						$result_arr[$row[csf('id')]]['etd_date']=$row[csf('etd_date')];
						$result_arr[$row[csf('id')]]['lc_category']=$row[csf('lc_category')];
						$result_arr[$row[csf('id')]]['payterm_id']=$row[csf('payterm_id')];
						$result_arr[$row[csf('id')]]['goods_rcv_status']=$row[csf('goods_rcv_status')];
						$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
						$result_arr[$row[csf('id')]]['ready_to_approved']=$row[csf('ready_to_approved')];
						$result_arr[$row[csf('id')]]['approved']=$row[csf('approved')];
						$result_arr[$row[csf('id')]]['release_date']=$row[csf('release_date')];
						$result_arr[$row[csf('id')]]['inserted_by']=$row[csf('inserted_by')];
						$pi_id_all.=$row[csf('pi_mst_id')].',';
						$btb_id_all[$row[csf('btb_lc_id')]]=$row[csf('btb_lc_id')];
					}
				}
				else
				{
					$result_arr[$row[csf('id')]]['id']=$row[csf('id')];
					$result_arr[$row[csf('id')]]['invoice_no']=$row[csf('invoice_no')];
					$result_arr[$row[csf('id')]]['invoice_date']=$row[csf('invoice_date')];
					$result_arr[$row[csf('id')]]['doc_rcv_date']=$row[csf('doc_rcv_date')];
					$result_arr[$row[csf('id')]]['local_doc_send_date']=$row[csf('local_doc_send_date')];
					$result_arr[$row[csf('id')]]['company_acc_date']=$row[csf('company_acc_date')];
					$result_arr[$row[csf('id')]]['bank_acc_date']=$row[csf('bank_acc_date')];
					$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
					$result_arr[$row[csf('id')]]['shipment_date']=$row[csf('shipment_date')];
					$result_arr[$row[csf('id')]]['eta_date']=$row[csf('eta_date')];
					$result_arr[$row[csf('id')]]['bill_no']=$row[csf('bill_no')];
					$result_arr[$row[csf('id')]]['feeder_vessel']=$row[csf('feeder_vessel')];
					$result_arr[$row[csf('id')]]['mother_vessel']=$row[csf('mother_vessel')];
					$result_arr[$row[csf('id')]]['container_no']=$row[csf('container_no')];
					$result_arr[$row[csf('id')]]['pkg_quantity']=$row[csf('pkg_quantity')];
					$result_arr[$row[csf('id')]]['bill_of_entry_no']=$row[csf('bill_of_entry_no')];
					$result_arr[$row[csf('id')]]['maturity_date']=$row[csf('maturity_date')];
					$result_arr[$row[csf('id')]]['acceptance_time']=$row[csf('acceptance_time')];
					$result_arr[$row[csf('id')]]['copy_doc_receive_date']=$row[csf('copy_doc_receive_date')];
					$result_arr[$row[csf('id')]]['doc_to_cnf']=$row[csf('doc_to_cnf')];
					$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
					$result_arr[$row[csf('id')]]['current_acceptance_value']=$row[csf('current_acceptance_value')];
					$result_arr[$row[csf('id')]]['import_invoice_id']=$row[csf('import_invoice_id')];
					$result_arr[$row[csf('id')]]['pi_number']=$row[csf('pi_number')];
					$result_arr[$row[csf('id')]]['pi_date']=$row[csf('pi_date')];
					$result_arr[$row[csf('id')]]['pi_mst_id']=$row[csf('pi_mst_id')];
					$result_arr[$row[csf('id')]]['btb_lc_id']=$row[csf('btb_lc_id')];
					$result_arr[$row[csf('id')]]['lc_type_id']=$row[csf('lc_type_id')];
					$result_arr[$row[csf('id')]]['issuing_bank_id']=$row[csf('issuing_bank_id')];
					$result_arr[$row[csf('id')]]['lc_number']=$row[csf('lc_number')];
					$result_arr[$row[csf('id')]]['lc_date']=$row[csf('lc_date')];
					$result_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
					$result_arr[$row[csf('id')]]['lca_no']=$row[csf('lca_no')];
					$result_arr[$row[csf('id')]]['item_category_id']=$row[csf('item_category_id')];
					$result_arr[$row[csf('id')]]['tenor']=$row[csf('tenor')];
					$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
					$result_arr[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
					$result_arr[$row[csf('id')]]['lc_expiry_date']=$row[csf('lc_expiry_date')];
					$result_arr[$row[csf('id')]]['etd_date']=$row[csf('etd_date')];
					$result_arr[$row[csf('id')]]['lc_category']=$row[csf('lc_category')];
					$result_arr[$row[csf('id')]]['payterm_id']=$row[csf('payterm_id')];
					$result_arr[$row[csf('id')]]['goods_rcv_status']=$row[csf('goods_rcv_status')];
					$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
					$result_arr[$row[csf('id')]]['ready_to_approved']=$row[csf('ready_to_approved')];
					$result_arr[$row[csf('id')]]['approved']=$row[csf('approved')];
					$result_arr[$row[csf('id')]]['release_date']=$row[csf('release_date')];
					$result_arr[$row[csf('id')]]['inserted_by']=$row[csf('inserted_by')];
					$pi_id_all.=$row[csf('pi_mst_id')].',';
					$btb_id_all[$row[csf('btb_lc_id')]]=$row[csf('btb_lc_id')];
				}
			}
			else{

				if($pending_type>0)
				{
					if($row[csf('current_acceptance_value')]>$pay_data_arr[$row[csf('id')]]["accepted_ammount"])
					{
						$result_case_arr[$row[csf('id')]]['id']=$row[csf('id')];
						$result_case_arr[$row[csf('id')]]['invoice_no']=$row[csf('invoice_no')];
						$result_case_arr[$row[csf('id')]]['invoice_date']=$row[csf('invoice_date')];
						$result_case_arr[$row[csf('id')]]['doc_rcv_date']=$row[csf('doc_rcv_date')];
						$result_case_arr[$row[csf('id')]]['local_doc_send_date']=$row[csf('local_doc_send_date')];
						$result_case_arr[$row[csf('id')]]['company_acc_date']=$row[csf('company_acc_date')];
						$result_case_arr[$row[csf('id')]]['bank_acc_date']=$row[csf('bank_acc_date')];
						$result_case_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
						$result_case_arr[$row[csf('id')]]['shipment_date']=$row[csf('shipment_date')];
						$result_case_arr[$row[csf('id')]]['eta_date']=$row[csf('eta_date')];
						$result_case_arr[$row[csf('id')]]['bill_no']=$row[csf('bill_no')];
						$result_case_arr[$row[csf('id')]]['feeder_vessel']=$row[csf('feeder_vessel')];
						$result_case_arr[$row[csf('id')]]['mother_vessel']=$row[csf('mother_vessel')];
						$result_case_arr[$row[csf('id')]]['container_no']=$row[csf('container_no')];
						$result_case_arr[$row[csf('id')]]['pkg_quantity']=$row[csf('pkg_quantity')];
						$result_case_arr[$row[csf('id')]]['bill_of_entry_no']=$row[csf('bill_of_entry_no')];
						$result_case_arr[$row[csf('id')]]['maturity_date']=$row[csf('maturity_date')];
						$result_case_arr[$row[csf('id')]]['acceptance_time']=$row[csf('acceptance_time')];
						$result_case_arr[$row[csf('id')]]['copy_doc_receive_date']=$row[csf('copy_doc_receive_date')];
						$result_case_arr[$row[csf('id')]]['doc_to_cnf']=$row[csf('doc_to_cnf')];
						$result_case_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
						$result_case_arr[$row[csf('id')]]['current_acceptance_value']=$row[csf('current_acceptance_value')];
						$result_case_arr[$row[csf('id')]]['import_invoice_id']=$row[csf('import_invoice_id')];
						$result_case_arr[$row[csf('id')]]['pi_number']=$row[csf('pi_number')];
						$result_case_arr[$row[csf('id')]]['pi_date']=$row[csf('pi_date')];
						$result_case_arr[$row[csf('id')]]['pi_mst_id']=$row[csf('pi_mst_id')];
						$result_case_arr[$row[csf('id')]]['btb_lc_id']=$row[csf('btb_lc_id')];
						$result_case_arr[$row[csf('id')]]['lc_type_id']=$row[csf('lc_type_id')];
						$result_case_arr[$row[csf('id')]]['issuing_bank_id']=$row[csf('issuing_bank_id')];
						$result_case_arr[$row[csf('id')]]['lc_number']=$row[csf('lc_number')];
						$result_case_arr[$row[csf('id')]]['lc_date']=$row[csf('lc_date')];
						$result_case_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
						$result_case_arr[$row[csf('id')]]['lca_no']=$row[csf('lca_no')];
						$result_case_arr[$row[csf('id')]]['item_category_id']=$row[csf('item_category_id')];
						$result_case_arr[$row[csf('id')]]['tenor']=$row[csf('tenor')];
						$result_case_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
						$result_case_arr[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
						$result_case_arr[$row[csf('id')]]['lc_expiry_date']=$row[csf('lc_expiry_date')];
						$result_case_arr[$row[csf('id')]]['etd_date']=$row[csf('etd_date')];
						$result_case_arr[$row[csf('id')]]['lc_category']=$row[csf('lc_category')];
						$result_case_arr[$row[csf('id')]]['payterm_id']=$row[csf('payterm_id')];
						$result_case_arr[$row[csf('id')]]['goods_rcv_status']=$row[csf('goods_rcv_status')];
						$result_case_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
						$result_case_arr[$row[csf('id')]]['ready_to_approved']=$row[csf('ready_to_approved')];
						$result_case_arr[$row[csf('id')]]['approved']=$row[csf('approved')];
						$result_case_arr[$row[csf('id')]]['release_date']=$row[csf('release_date')];
						$result_case_arr[$row[csf('id')]]['inserted_by']=$row[csf('inserted_by')];
						$pi_id_all.=$row[csf('pi_mst_id')].',';
						$btb_id_all[$row[csf('btb_lc_id')]]=$row[csf('btb_lc_id')];
					}
				}
				else
				{
					$result_case_arr[$row[csf('id')]]['id']=$row[csf('id')];
					$result_case_arr[$row[csf('id')]]['invoice_no']=$row[csf('invoice_no')];
					$result_case_arr[$row[csf('id')]]['invoice_date']=$row[csf('invoice_date')];
					$result_case_arr[$row[csf('id')]]['doc_rcv_date']=$row[csf('doc_rcv_date')];
					$result_case_arr[$row[csf('id')]]['local_doc_send_date']=$row[csf('local_doc_send_date')];
					$result_case_arr[$row[csf('id')]]['company_acc_date']=$row[csf('company_acc_date')];
					$result_case_arr[$row[csf('id')]]['bank_acc_date']=$row[csf('bank_acc_date')];
					$result_case_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
					$result_case_arr[$row[csf('id')]]['shipment_date']=$row[csf('shipment_date')];
					$result_case_arr[$row[csf('id')]]['eta_date']=$row[csf('eta_date')];
					$result_case_arr[$row[csf('id')]]['bill_no']=$row[csf('bill_no')];
					$result_case_arr[$row[csf('id')]]['feeder_vessel']=$row[csf('feeder_vessel')];
					$result_case_arr[$row[csf('id')]]['mother_vessel']=$row[csf('mother_vessel')];
					$result_case_arr[$row[csf('id')]]['container_no']=$row[csf('container_no')];
					$result_case_arr[$row[csf('id')]]['pkg_quantity']=$row[csf('pkg_quantity')];
					$result_case_arr[$row[csf('id')]]['bill_of_entry_no']=$row[csf('bill_of_entry_no')];
					$result_case_arr[$row[csf('id')]]['maturity_date']=$row[csf('maturity_date')];
					$result_case_arr[$row[csf('id')]]['acceptance_time']=$row[csf('acceptance_time')];
					$result_case_arr[$row[csf('id')]]['copy_doc_receive_date']=$row[csf('copy_doc_receive_date')];
					$result_case_arr[$row[csf('id')]]['doc_to_cnf']=$row[csf('doc_to_cnf')];
					$result_case_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
					$result_case_arr[$row[csf('id')]]['current_acceptance_value']=$row[csf('current_acceptance_value')];
					$result_case_arr[$row[csf('id')]]['import_invoice_id']=$row[csf('import_invoice_id')];
					$result_case_arr[$row[csf('id')]]['pi_number']=$row[csf('pi_number')];
					$result_case_arr[$row[csf('id')]]['pi_date']=$row[csf('pi_date')];
					$result_case_arr[$row[csf('id')]]['pi_mst_id']=$row[csf('pi_mst_id')];
					$result_case_arr[$row[csf('id')]]['btb_lc_id']=$row[csf('btb_lc_id')];
					$result_case_arr[$row[csf('id')]]['lc_type_id']=$row[csf('lc_type_id')];
					$result_case_arr[$row[csf('id')]]['issuing_bank_id']=$row[csf('issuing_bank_id')];
					$result_case_arr[$row[csf('id')]]['lc_number']=$row[csf('lc_number')];
					$result_case_arr[$row[csf('id')]]['lc_date']=$row[csf('lc_date')];
					$result_case_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
					$result_case_arr[$row[csf('id')]]['lca_no']=$row[csf('lca_no')];
					$result_case_arr[$row[csf('id')]]['item_category_id']=$row[csf('item_category_id')];
					$result_case_arr[$row[csf('id')]]['tenor']=$row[csf('tenor')];
					$result_case_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
					$result_case_arr[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
					$result_case_arr[$row[csf('id')]]['lc_expiry_date']=$row[csf('lc_expiry_date')];
					$result_case_arr[$row[csf('id')]]['etd_date']=$row[csf('etd_date')];
					$result_case_arr[$row[csf('id')]]['lc_category']=$row[csf('lc_category')];
					$result_case_arr[$row[csf('id')]]['payterm_id']=$row[csf('payterm_id')];
					$result_case_arr[$row[csf('id')]]['goods_rcv_status']=$row[csf('goods_rcv_status')];
					$result_case_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
					$result_case_arr[$row[csf('id')]]['ready_to_approved']=$row[csf('ready_to_approved')];
					$result_case_arr[$row[csf('id')]]['approved']=$row[csf('approved')];
					$result_case_arr[$row[csf('id')]]['release_date']=$row[csf('release_date')];
					$result_case_arr[$row[csf('id')]]['inserted_by']=$row[csf('inserted_by')];
					$pi_id_all.=$row[csf('pi_mst_id')].',';
					$btb_id_all[$row[csf('btb_lc_id')]]=$row[csf('btb_lc_id')];
				}

			}								
		}
		unset($sql_data);
	
		$pi_id_all=array_unique(explode(",",chop($pi_id_all,',')));
		$pi_id_in=where_con_using_array($pi_id_all,0,'id');
		$piGoodRcvArr = return_library_array("select id,goods_rcv_status from com_pi_master_details where status_active=1 and is_deleted=0 $pi_id_in","id","goods_rcv_status"); 
		$companyArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name"); 
	
		$btb_id_in=where_con_using_array($btb_id_all,0,'a.import_mst_id');
		$lc_sc_sql="SELECT a.import_mst_id as BTB_ID, export_lc_no as LC_SC_NO from com_btb_export_lc_attachment a, com_export_lc b where a.lc_sc_id=b.id $btb_id_in and a.is_lc_sc=0 and a.status_active=1
		union all 
		SELECT a.import_mst_id as BTB_ID, contract_no as LC_SC_NO from com_btb_export_lc_attachment a, com_sales_contract b where a.lc_sc_id=b.id $btb_id_in and a.is_lc_sc=1 and a.status_active=1";
		// echo $lc_sc_sql;
		$lc_sc_data=sql_select($lc_sc_sql);
		$lc_sc_no=array();
		foreach($lc_sc_data as $row)
		{
			$lc_sc_no[$row["BTB_ID"]].=$row["LC_SC_NO"].', ';
		}
		unset($lc_sc_data);
		ob_start();
	
		?>
		<div id="" align="center" style="height:auto; width:3718px; margin:0 auto; padding:0;">
			<table width="3990px" align="center">
				<?
				$company_library=sql_select("SELECT id, COMPANY_NAME from lib_company where id in($cbo_company)");
				foreach( $company_library as $row)
				{
					$company_name.=$row["COMPANY_NAME"].", ";
				}
				?>
				<tr>
					<td colspan="37" align="center" style="font-size:22px"><center><strong><? echo rtrim($company_name,", ");?></strong></center></td>
				</tr>
				<tr>
					<td colspan="37" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?> Report</u></strong></center></td>
				</tr>
				<tr>
					<td colspan="37" align="left" style="font-size:18px"><strong><u>LC CI Details</u></strong></td>
				</tr>					
			</table>
			<div style="width:3990px;">
				<table  cellspacing="0" width="3990px"  border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
					<thead>
							<tr>
							<th rowspan="2" width="30">SL</th>
							<th rowspan="2" width="100" align="center">Invoice No</th>
							<th rowspan="2" width="75" align="center">Invoice Date</th>
							<th rowspan="2" width="80" align="center">Ready To Approved</th>
							<th rowspan="2" width="80" align="center">Approval Status</th>
							<th rowspan="2" width="100" align="center">File Ref. No.</th>
							<th rowspan="2" width="120" align="center">Issuing Bank</th>
							<th rowspan="2" width="100" align="center">LC No</th>
							<th rowspan="2" width="75" align="center">Lc Date</th>
							<th rowspan="2" width="70" align="center">Import Source</th>
							<th rowspan="2" width="50" align="center"><p>Suppl.</p></th>
							<th rowspan="2" width="50" align="center"><p>PI No & Date</p></th>
							<th rowspan="2" width="100" align="center">PI No</th>
							<th rowspan="2" width="100" align="center">Item Category</th>
							<th rowspan="2" width="90" align="center">LCA No</th>
							<th rowspan="2" width="50" align="center">Tenor</th>
							<th rowspan="2" width="80" align="center">LC value</th>
							<th rowspan="2" width="40" align="center"><p>Curr.</p></th>
							<th rowspan="2" width="70" align="center">Doc Recv. Date</th>
							<th rowspan="2" width="70" align="center">Local Doc Send Date</th>
							<th rowspan="2" width="70" align="center">Com. Accep. Date</th>
							<th rowspan="2" width="70" align="center">Bank Accep. Date</th>
							<th rowspan="2" width="70" align="center">Bank Ref</th>
							<th rowspan="2" width="80" align="center">Bill Value</th>
							<th rowspan="2" width="90" align="center">Paid Amount</th>
							<th rowspan="2" width="80" align="center">Out Standing</th>
							<th colspan="3">Bank Payment For At Sight</th>
							<th rowspan="2" width="70" align="center">Maturity Date</th>
							<th rowspan="2" width="70" align="center">Month</th>
							<th rowspan="2" width="70" align="center">Pay Date</th>
							<th rowspan="2" width="70" align="center"><p>Shipment Date</p></th>
							<th rowspan="2" width="70" align="center">Expiry Date</th>
							<th rowspan="2" width="80" align="center">Lc Type</th>
							<th rowspan="2" width="70" align="center">ETD</th>
							<th rowspan="2" width="70" align="center">ETA</th>
							<th rowspan="2" width="100" align="center">BL/Cargo No</th>
							<th rowspan="2" width="70" align="center">Feeder Vassel</th>
							<th rowspan="2" width="70" align="center">Mother Vassel</th>
							<th rowspan="2" width="60" align="center"><p>Continer No</p></th>
							<th rowspan="2" width="70" align="center">Pkg Qty</th>
							<th rowspan="2" width="80" align="center">Bill Of Entry No</th>
							<th rowspan="2" width="80" align="center">Total Qty</th>
							<th rowspan="2" width="80" align="center">Release Date</th>
							<th rowspan="2" width="70" align="center">NN Doc Received Date</th>
							<th rowspan="2" width="70" align="center">Doc Send to CNF</th>
							<th rowspan="2" width="100" align="center">LC/SC Number</th>
							<th rowspan="2" width="60" align="center">Goods in House Date</th>
							<th rowspan="2" width="80" align="center">Actual Received Value</th>
							<th rowspan="2" width="80"align="center">Balance Value</th>						   
							<th rowspan="2" align="center">Insert User</th>						   
						</tr>
						<tr>
							<th width="80" >IFDBC Liability</th>
							<th width="80" >Bank Charge Amount</th>
							<th width="80" >Interest Paid Amount</th>
						</tr>
					</thead>
				</table>
				</div>
				<div style="width:3990px; overflow-y: scroll; max-height:300px;" id="scroll_body">
				<table cellspacing="0" width="3990px"  border="1" rules="all" class="rpt_table" id="tbl_body" >
				<?
					$i=1;
					$lc_check=array();
					foreach( $result_arr as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
			
						$import_invoice_id=$row[('import_invoice_id')];
						$suppl_id=$row[('supplier_id')];
						$item_id=$row[('item_category_id')];
						$curr_id=$row[('currency_id')];
						$pi_id=$row[('pi_mst_id')];
			
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="100"><p><? echo $row[('invoice_no')]; ?></p></td>
							<td width="75"><p>&nbsp;<? if($row[('invoice_date')]!="0000-00-00") echo change_date_format($row[('invoice_date')]); else echo "";?></p></td>
							<td width="80" align="center"><? if($row['ready_to_approved']==1){ echo "Yes"; }else{ echo "No"; }?></td>
							<td width="80" align="center">
								<?
									if($row['approved']==1){ echo "Approved"; }
									else if($row['approved']==3){ echo "Partial Approved";  }
									else{ echo "Not Approved"; }
								?>
							</td>
							<td width="100"><p>
							<?
							$exp_lc_sc_id =$exp_lc_sc_data[$row[('btb_lc_id')]]["lc_sc_id"];
							$is_lc_sc =$exp_lc_sc_data[$row[('btb_lc_id')]]["is_lc_sc"];
							if($is_lc_sc==0)
							$file_reference_no=$file_reference_lc_arr[$row[('btb_lc_id')]];
							else
							$file_reference_no=$file_reference_sales_arr[$row[('btb_lc_id')]];
							echo $file_reference_no; 
							?></p></td>
							<td width="120"><p><? echo $issueBankrArr[$row[('issuing_bank_id')]]; ?></p></td>
							<td width="100"><p>&nbsp;<? echo trim($row[('lc_number')]); ?></p></td>
			
							<td width="75"><p>&nbsp;<? if($row[('lc_date')]!="0000-00-00") echo change_date_format($row[('lc_date')]); else echo ""; ?></p></td>
							<td width="70"><p><? echo $supply_source[$row[('lc_category')]*1]; ?></p></td>
							<td width="50"><p><? if($exportPiSuppArr[$row[('btb_lc_id')]]==1) echo $companyArr[$row[('supplier_id')]]; else echo $supplierArr[$row[('supplier_id')]];  ?></p></td>
							<td width="50" align="center"><p><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage_pi_date('$import_invoice_id','$suppl_id','$item_id','$pi_id','$curr_id','pi_details','PI Details');\">"."View"."</a>";//$row[("pi_id")]; ?></p></td>
							<td width="100"><p><? echo $row[('pi_number')];?></p></td>
							<td width="100"><p><? $itemCategory=""; echo $item_category[$item_id]; ?></p></td>
							<td width="90"><p><? echo $row[('lca_no')]; ?></p></td>
							<td width="50"><p><? echo $row[('tenor')]; ?></p></td>
							<?
							if($lc_check[$row[('btb_lc_id')]]=="")
							{
								$lc_check[$row[('btb_lc_id')]]=$row[('btb_lc_id')];
								?>
								<td width="80" align="right"><? echo number_format($row[('lc_value')],2); $tot_lc_value+=$row[('lc_value')];$tot_lc_value_sub+=$row[('lc_value')]//echo number_format($row[('lc_value')],2); //btb_lc_id?></td>
								<?
								$cash_payment=$row[('lc_value')];
							}
							else
							{
								?>
								<td width="80" align="right"><? echo "<span style='color:white'>'</span>".number_format($row[('lc_value')],2)."<span style='color:white'>'</span>";?><!--<span style="color:white">_</span>--></td>
								<?
								$cash_payment==0;
							}
							?>
			
							<td width="40"><p><? echo $currency[$row[('currency_id')]]; ?></p></td>
							<td width="70"><p>&nbsp;<? if($row[('doc_rcv_date')]!="0000-00-00") echo change_date_format($row[('doc_rcv_date')]); else echo ""; ?></p></td>
							<td width="70"><p>&nbsp;<? if($row[('local_doc_send_date')]!="0000-00-00") echo change_date_format($row[('local_doc_send_date')]); ?></p></td>
							<td width="70"><p>&nbsp;<? if($row[('company_acc_date')]!="0000-00-00") echo change_date_format($row[('company_acc_date')]); else echo ""; ?></p></td>
							<td width="70"><p>&nbsp;<? if($row[('bank_acc_date')]!="0000-00-00") echo change_date_format($row[('bank_acc_date')]); ?></p></td>
							<td width="70"><p><? echo $row[('bank_ref')]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[('current_acceptance_value')],2); $tot_bill_value+=$row[('current_acceptance_value')]; $tot_bill_value_sub_ci+=$row[('current_acceptance_value')]; ?></p></td>
							<?
							if($row[('payterm_id')]==3)
							{
								?>
								<td width="90" align="right"><p><a href="#report_details" onClick="paid_amount_dtls('<? echo $row[('id')];?>','payment_details','Payment Details');">
								<?
								echo number_format($cash_payment,2); 
								$out_standing=$row[('current_acceptance_value')]-$cash_payment;
								$gt_total_paid+=$cash_payment;
								$gt_total_paid_sub_ci+=$cash_payment;
								?>
								</a></p></td>
								<?
							}
							elseif($row[('payterm_id')]==1)
							{
								?>
								<td width="90" title="sssssssss" align="right"><p><a href="#report_details" onClick="paid_amount_dtls('<? echo $row[('id')];?>','payment_details','Payment Details');">
								<?
								echo number_format($cash_payment,2); 
								$out_standing=$row[('current_acceptance_value')]-$cash_payment;
								$gt_total_paid+=$cash_payment;
								$gt_total_paid_sub_ci+=$cash_payment;
								?>
								</a></p></td>
								<?
							}
							else
							{
								?>
								<td width="90" align="right"><p><a href="#report_details" onClick="paid_amount_dtls('<? echo $row[('id')];?>','payment_details','Payment Details');">
								<?
								echo number_format($pay_data_arr[$row[('id')]]["accepted_ammount"],2); 
								$out_standing=$row[('current_acceptance_value')]-$pay_data_arr[$row['id']]["accepted_ammount"];
								$gt_total_paid+=$pay_data_arr[$row[('id')]]["accepted_ammount"];
								$gt_total_paid_sub_ci+=$pay_data_arr[$row[('id')]]["accepted_ammount"];
								?>
								</a></p></td>
								<?
							}
							?>
							<td width="80" align="right"><p><? echo number_format($out_standing,2); $total_out_standing +=$out_standing;  $total_out_standing_sub_ci +=$out_standing ?></p></td>

							<td width="80" align="right"><p><? echo number_format($pay_data_atsite_arr[$import_invoice_id]["il"],2); $total_atsite_il +=$pay_data_atsite_arr[$import_invoice_id]["il"]; $total_atsite_il_sub_ci +=$pay_data_atsite_arr[$import_invoice_id]["il"]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($pay_data_atsite_arr[$import_invoice_id]["bc"],2); $total_atsite_bc +=$pay_data_atsite_arr[$import_invoice_id]["bc"];$total_atsite_bc_sub_ci +=$pay_data_atsite_arr[$import_invoice_id]["bc"]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($pay_data_atsite_arr[$import_invoice_id]["int"],2); $total_atsite_int +=$pay_data_atsite_arr[$import_invoice_id]["int"];$total_atsite_int_sub_ci +=$pay_data_atsite_arr[$import_invoice_id]["int"]; ?></p></td>

							<td width="70"><p>&nbsp;<? if($row[('maturity_date')]!="0000-00-00") echo change_date_format($row[('maturity_date')]); else echo ""; ?></p></td>
							<td width="70"><p>
							<?
								if($row[('maturity_date')]=="00-00-0000")
								{
									$month="";
								}
								else
								{
									$month = date('F', strtotime($row[('maturity_date')]));
								}
								echo $month;
			
								?></p></td>
							<td width="70" ><p>&nbsp;<? if($pay_data_arr[$row[('id')]]["payment_date"]!="" && $pay_data_arr[$row[('id')]]["payment_date"]!="0000-00-00") echo change_date_format($pay_data_arr[$row[('id')]]["payment_date"]); ?></p></td>
							<td width="70"><p><? if($row[('shipment_date')]!="0000-00-00") echo change_date_format($row[('shipment_date')]); else echo ""; ?></p></td>
							<td width="70"><p>&nbsp;<? if($row[('lc_expiry_date')]!="0000-00-00")  echo change_date_format($row[('lc_expiry_date')]); else echo ""; ?></p></td>
							<td width="80"><p><? echo $lc_type[$row[('lc_type_id')]]; ?></p></td>
							<td width="70"><p>&nbsp;<? if($row[('etd_date')]!="0000-00-00") echo change_date_format($row[('etd_date')]); else echo ""; ?></p></td>
							<td width="70"><p>&nbsp;<? if($row[('eta_date')]!="0000-00-00") echo change_date_format($row[('eta_date')]); else echo ""; ?></p></td>
							<td width="100"><p><? echo $row[('bill_no')]; ?></p></td>
							<td width="70"><p><? echo $row[('feeder_vessel')]; ?></p></td>
							<td width="70"><p><? echo $row[('mother_vessel')]; ?></p></td>
							<td width="60"><p><? echo $row[('container_no')]; ?></p></td>
							<td width="70"><p><? echo $row[('pkg_quantity')]; ?></p></td>
							<td width="80"><p><? echo $row[('bill_of_entry_no')]; ?></p></td>
							<?
							//$receive_data_arr[$row["BOOKING_ID"]][$row["RECEIVE_BASIS"]]["cons_quantity"]+=$row["ORDER_QNTY"];
							//$receive_data_arr[$row["BOOKING_ID"]][$row["RECEIVE_BASIS"]]["cons_amount"]+=$row["ORDER_AMOUNT"];
							$receive_qnty=$receive_value=$receive_Return_qnty=$receive_Return_value=0;
							$all_wo_pi_id=$receive_basis="";
							$pi_id_all=explode(",",$row[('pi_mst_id')]);
							foreach($pi_id_all as $piid)
							{
								if($piGoodRcvArr[$piid]==1)
								{
									$pi_wo_ids[$row["ID"]].=$row["WORK_ORDER_ID"].",";
									$wo_id_arr=explode(",",chop($pi_wo_ids[$piid],","));
									foreach($wo_id_arr as $wo_id)
									{
										$receive_qnty += $receive_data_arr[$wo_id][2]["cons_quantity"];
										$receive_value += $receive_data_arr[$wo_id][2]["cons_amount"];
										$all_wo_pi_id.=$wo_id.",";
									}
									$receive_basis=2;
									
								}
								else
								{
									$receive_qnty += $receive_data_arr[$piid][1]["cons_quantity"];
									$receive_value += $receive_data_arr[$piid][1]["cons_amount"];
									$all_wo_pi_id.=$piid.",";
									$receive_basis=1;
								}
								
								$receive_Return_qnty += $receive_Return_data_arr[$piid]["cons_quantity"];
								$receive_Return_value += $receive_Return_data_arr[$piid]["cons_amount"];
							}
							$receive_actual_value=$receive_value-$receive_Return_value;
							$balance_value=$row[('lc_value')]-$receive_actual_value;
							?>
							<td width="80" align="right"><p><? echo number_format($receive_qnty,0,"",""); $total_receive_qnty+=$receive_qnty;$total_receive_qnty_sub_ci+=$receive_qnty; ?></p></td>
							<td width="80" align="right"><p><? echo change_date_format($row[("release_date")]); ?></p></td>
							<td width="70"><p>&nbsp;<?  if($row[('copy_doc_receive_date')]!="0000-00-00")  echo change_date_format($row[('copy_doc_receive_date')]); else echo ""; ?></p></td>
							<td width="70"><p>&nbsp;<? if($row[('doc_to_cnf')]!="0000-00-00") echo change_date_format($row[('doc_to_cnf')]); else echo ""; ?></p></td>
							<td width="100"><p><? echo chop($lc_sc_no[$row[('btb_lc_id')]],", ");?></p></td>
							<td width="60" align="center"><p><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage_inHouse_date('".chop($all_wo_pi_id,",")."','".$receive_basis."','$receive_value','$receive_qnty','$item_id','pi_rec_details','PI Details','".$import_invoice_id."');\">"."View"."</a>"; //$row[("pi_id")]; ?></p></td>
							
							<td width="80" align="right" ><p><?   echo number_format($receive_actual_value,2); $total_receive_value+=$receive_actual_value;$total_receive_value_sub_ci+=$receive_actual_value; ?></p></td>
							<td width="80" align="right" ><p><?   echo  number_format($balance_value,2);  $total_balance_value+=$balance_value; $total_balance_value_sub_ci+=$balance_value; ?></p></td>
							<td ><p><? echo $user_arr[$row[('inserted_by')]];?></p></td>
							
						</tr>
						<?
						$i++;
					}
				?>
				</table>
				<table cellspacing="0" width="3990px"  border="1" rules="all" class="rpt_table" id="report_table_footer" >
					<tfoot>
						<th width="30">&nbsp;</th>
						<th width="100" >&nbsp;</th>
						<th width="75" >&nbsp;</th>
						<th width="80" >&nbsp;</th>
						<th width="80" >&nbsp;</th>
						<th width="100" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="100" >&nbsp;</th>
						<th width="75" >&nbsp;</th>
						<th width="70" >&nbsp;</th>
						<th width="50" ><p>&nbsp;</p></th>
						<th width="50" ><p>&nbsp;</p></th>
						<th width="100" >&nbsp;</th>
						<th width="100" >&nbsp;</th>
						<th width="90" >&nbsp;</th>
						<th width="50" align="right">Total : </th>
						<th width="80" align="right" id="value_tot_lc_value"><? echo number_format($tot_lc_value_sub,2); ?></th>
						<th width="40" align="center">&nbsp;</th>
						<th width="70" align="center">&nbsp;</th>
						<th width="70" align="center">&nbsp;</th>
						<th width="70" align="center">&nbsp;</th>
						<th width="70" align="center">&nbsp;</th>
						<th width="70" align="center">&nbsp;</th>
						<th width="80" align="right" id="value_tot_bill_value_ww"><? echo number_format($tot_bill_value_sub_ci,2); ?></th>
						<th width="90" align="right" id="value_gt_total_paid_ww"><? echo number_format($gt_total_paid_sub_ci,2); ?></th>
						<th width="80" align="right" id="value_total_out_standing_ww"><? echo number_format($total_out_standing_sub_ci,2); ?></th>
						<th width="80" align="right" id="value_atsite_il_ww"><? echo number_format($total_atsite_il_sub_ci,2); ?></th>
						<th width="80" align="right" id="value_atsite_bc_ww"><? echo number_format($total_atsite_bc_sub_ci,2); ?></th>
						<th width="80" align="right" id="value_atsite_int_ww"><? echo number_format($total_atsite_int_sub_ci,2); ?></th>
						<th width="70" align="center">&nbsp;</th>
						<th width="70" align="center">&nbsp;</th>
						<th width="70" align="center">&nbsp;</th>
						<th width="70" align="center">&nbsp;</th>
						<th width="70" align="center">&nbsp;</th>
						<th width="80" align="center">&nbsp;</th>
						<th width="70" align="center">&nbsp;</th>
						<th width="70" align="center">&nbsp;</th>
						<th width="100" align="center">&nbsp;</th>
						<th width="70" align="center">&nbsp;</th>
						<th width="70" align="center">&nbsp;</th>
						<th width="60" align="center">&nbsp;</th>
						<th width="70" align="center">&nbsp;</th>
						<th width="80" align="center">&nbsp;</th>
						<th align="right" id="total_qnty" width="80"><? echo number_format($total_receive_qnty_sub_ci,2); ?></th>
						<th width="80" align="center">&nbsp;</th>
						<th width="70" align="center">&nbsp;</th>
						<th width="70" align="center">&nbsp;</th>
						<th width="100" align="center">&nbsp;</th>
						<th width="60" align="center">&nbsp;</th>
						<th width="80" align="right" id="value_total_receive"><? echo number_format($total_receive_value_sub_ci,2); ?></th>
						<th width="80" align="right" id="value_balance_value"><? echo number_format($total_balance_value_sub_ci,2); ?></th>
						<th></th>
					</tfoot>
			</table>					
			</div>

			<br><br><br>
			<table width="3990px" align="center">
				
				<tr>
					<td colspan="37" align="left" style="font-size:18px"><strong><u>Cash In Advance CI Details</u></strong></td>
				</tr>
			</table>
			<div style="width:3960px;">
				<table  cellspacing="0" width="3990px"  border="1" rules="all" class="rpt_table" id="table_header_12" align="left">
					<thead>
							<tr>
							<th rowspan="2" width="30">SL</th>
							<th rowspan="2" width="100" align="center">Invoice No</th>
							<th rowspan="2" width="75" align="center">Invoice Date</th>
							<th rowspan="2" width="80" align="center">Ready To Approved</th>
							<th rowspan="2" width="80" align="center">Approval Status</th>
							<th rowspan="2" width="100" align="center">File Ref. No.</th>
							<th rowspan="2" width="120" align="center">Issuing Bank</th>
							<th rowspan="2" width="100" align="center">LC No</th>
							<th rowspan="2" width="75" align="center">Lc Date</th>
							<th rowspan="2" width="70" align="center">Import Source</th>
							<th rowspan="2" width="50" align="center"><p>Suppl.</p></th>
							<th rowspan="2" width="50" align="center"><p>PI No & Date</p></th>
							<th rowspan="2" width="100" align="center">PI No</th>
							<th rowspan="2" width="100" align="center">Item Category</th>
							<th rowspan="2" width="90" align="center">LCA No</th>
							<th rowspan="2" width="50" align="center">Tenor</th>
							<th rowspan="2" width="80" align="center">LC value</th>
							<th rowspan="2" width="40" align="center"><p>Curr.</p></th>
							<th rowspan="2" width="70" align="center">Doc Recv. Date</th>
							<th rowspan="2" width="70" align="center">Local Doc Send Date</th>
							<th rowspan="2" width="70" align="center">Com. Accep. Date</th>
							<th rowspan="2" width="70" align="center">Bank Accep. Date</th>
							<th rowspan="2" width="70" align="center">Bank Ref</th>
							<th rowspan="2" width="80" align="center">Bill Value</th>
							<th rowspan="2" width="90" align="center">Paid Amount</th>
							<th rowspan="2" width="80" align="center">Out Standing</th>
							<th colspan="3">Bank Payment For At Sight</th>
							<th rowspan="2" width="70" align="center">Maturity Date</th>
							<th rowspan="2" width="70" align="center">Month</th>
							<th rowspan="2" width="70" align="center">Pay Date</th>
							<th rowspan="2" width="70" align="center"><p>Shipment Date</p></th>
							<th rowspan="2" width="70" align="center">Expiry Date</th>
							<th rowspan="2" width="80" align="center">Lc Type</th>
							<th rowspan="2" width="70" align="center">ETD</th>
							<th rowspan="2" width="70" align="center">ETA</th>
							<th rowspan="2" width="100" align="center">BL/Cargo No</th>
							<th rowspan="2" width="70" align="center">Feeder Vassel</th>
							<th rowspan="2" width="70" align="center">Mother Vassel</th>
							<th rowspan="2" width="60" align="center"><p>Continer No</p></th>
							<th rowspan="2" width="70" align="center">Pkg Qty</th>
							<th rowspan="2" width="80" align="center">Bill Of Entry No</th>
							<th rowspan="2" width="80" align="center">Total Qty</th>
							<th rowspan="2" width="80" align="center">Release Date</th>
							<th rowspan="2" width="70" align="center">NN Doc Received Date</th>
							<th rowspan="2" width="70" align="center">Doc Send to CNF</th>
							<th rowspan="2" width="100" align="center">LC/SC Number</th>
							<th rowspan="2" width="60" align="center">Goods in House Date</th>
							<th rowspan="2" width="80" align="center">Actual Received Value</th>
							<th rowspan="2" width="80"align="center">Balance Value</th>						   
							<th rowspan="2" align="center">Insert User</th>						   
						</tr>
						<tr>
							<th width="80" >IFDBC Liability</th>
							<th width="80" >Bank Charge Amount</th>
							<th width="80" >Interest Paid Amount</th>
						</tr>
					</thead>
				</table>
				</div>
				<div style="width:3990px; overflow-y: scroll; max-height:300px;" id="scroll_body">
				<table cellspacing="0" width="3990px"  border="1" rules="all" class="rpt_table" id="tbl_body_ss" >
				<?
					$i=1;
					// $lc_check=array();
					// $tot_lc_value=0;
					foreach($result_case_arr as $row)
					{
						// $tot_lc_value=0;
						$tot_lc_value_cash_sub+=$row[('lc_value')];
						$tot_bill_value_sub+=$row[('current_acceptance_value')]; 
						$total_atsite_il_sub +=$pay_data_atsite_arr[$import_invoice_id]["il"];
						$total_atsite_bc_sub +=$pay_data_atsite_arr[$import_invoice_id]["bc"];
						$total_atsite_intsub +=$pay_data_atsite_arr[$import_invoice_id]["int"];
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
			
						$import_invoice_id=$row[('import_invoice_id')];
						$suppl_id=$row[('supplier_id')];
						$item_id=$row[('item_category_id')];
						$curr_id=$row[('currency_id')];
						$pi_id=$row[('pi_mst_id')];
			
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="100"><p><? echo $row[('invoice_no')]; ?></p></td>
							<td width="75"><p>&nbsp;<? if($row[('invoice_date')]!="0000-00-00") echo change_date_format($row[('invoice_date')]); else echo "";?></p></td>
							<td width="80" align="center"><? if($row['ready_to_approved']==1){ echo "Yes"; }else{ echo "No"; }?></td>
							<td width="80" align="center">
								<?
									if($row['approved']==1){ echo "Approved"; }
									else if($row['approved']==3){ echo "Partial Approved";  }
									else{ echo "Not Approved"; }
								?>
							</td>
							<td width="100"><p>
							<?
							$exp_lc_sc_id =$exp_lc_sc_data[$row[('btb_lc_id')]]["lc_sc_id"];
							$is_lc_sc =$exp_lc_sc_data[$row[('btb_lc_id')]]["is_lc_sc"];
							if($is_lc_sc==0)
							$file_reference_no=$file_reference_lc_arr[$row[('btb_lc_id')]];
							else
							$file_reference_no=$file_reference_sales_arr[$row[('btb_lc_id')]];
							echo $file_reference_no; 
							?></p></td>
							<td width="120"><p><? echo $issueBankrArr[$row[('issuing_bank_id')]]; ?></p></td>
							<td width="100"><p>&nbsp;<? echo trim($row[('lc_number')]); ?></p></td>
			
							<td width="75"><p>&nbsp;<? if($row[('lc_date')]!="0000-00-00") echo change_date_format($row[('lc_date')]); else echo ""; ?></p></td>
							<td width="70"><p><? echo $supply_source[$row[('lc_category')]*1]; ?></p></td>
							<td width="50"><p><? if($exportPiSuppArr[$row[('btb_lc_id')]]==1) echo $companyArr[$row[('supplier_id')]]; else echo $supplierArr[$row[('supplier_id')]];  ?></p></td>
							<td width="50" align="center"><p><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage_pi_date('$import_invoice_id','$suppl_id','$item_id','$pi_id','$curr_id','pi_details','PI Details');\">"."View"."</a>";//$row[("pi_id")]; ?></p></td>
							<td width="100"><p><? echo $row[('pi_number')];?></p></td>
							<td width="100"><p><? $itemCategory=""; echo $item_category[$item_id]; ?></p></td>
							<td width="90"><p><? echo $row[('lca_no')]; ?></p></td>
							<td width="50"><p><? echo $row[('tenor')]; ?></p></td>
							<?
							if($lc_check[$row[('btb_lc_id')]]=="")
							{
								$lc_check[$row[('btb_lc_id')]]=$row[('btb_lc_id')];
								?>
								<td width="80" align="right"><? echo number_format($row[('lc_value')],2); $tot_lc_value+=$row[('lc_value')];//echo number_format($row[('lc_value')],2); //btb_lc_id?></td>
								<?
								$cash_payment=$row[('lc_value')];
							}
							else
							{
								?>
								<td width="80" align="right"><? echo "<span style='color:white'>'</span>".number_format($row[('lc_value')],2)."<span style='color:white'>'</span>";?><!--<span style="color:white">_</span>--></td>
								<?
								$cash_payment==0;
							}
							?>
			
							<td width="40"><p><? echo $currency[$row[('currency_id')]]; ?></p></td>
							<td width="70"><p>&nbsp;<? if($row[('doc_rcv_date')]!="0000-00-00") echo change_date_format($row[('doc_rcv_date')]); else echo ""; ?></p></td>
							<td width="70"><p>&nbsp;<? if($row[('local_doc_send_date')]!="0000-00-00") echo change_date_format($row[('local_doc_send_date')]); ?></p></td>
							<td width="70"><p>&nbsp;<? if($row[('company_acc_date')]!="0000-00-00") echo change_date_format($row[('company_acc_date')]); else echo ""; ?></p></td>
							<td width="70"><p>&nbsp;<? if($row[('bank_acc_date')]!="0000-00-00") echo change_date_format($row[('bank_acc_date')]); ?></p></td>
							<td width="70"><p><? echo $row[('bank_ref')]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[('current_acceptance_value')],2); $tot_bill_value+=$row[('current_acceptance_value')]; ?></p></td>
							<?
							if($row[('payterm_id')]==3)
							{
								?>
								<td width="90" align="right"><p><a href="#report_details" onClick="paid_amount_dtls('<? echo $row[('id')];?>','payment_details','Payment Details');">
								<?
								echo number_format($cash_payment,2); 
								$out_standing=$row[('current_acceptance_value')]-$cash_payment;
								$gt_total_paid+=$cash_payment;
								$gt_total_paid_sub+=$cash_payment;
								?>
								</a></p></td>
								<?
							}
							else
							{
								?>
								<td width="90" align="right"><p><a href="#report_details" onClick="paid_amount_dtls('<? echo $row[('id')];?>','payment_details','Payment Details');">
								<?
								echo number_format($pay_data_arr[$row[('id')]]["accepted_ammount"],2); 
								$out_standing=$row[('current_acceptance_value')]-$pay_data_arr[$row['id']]["accepted_ammount"];
								$gt_total_paid+=$pay_data_arr[$row[('id')]]["accepted_ammount"];
								$gt_total_paid_sub+=$pay_data_arr[$row[('id')]]["accepted_ammount"];
								?>
								</a></p></td>
								<?
							}
							?>
							<td width="80" align="right"><p><? echo number_format($out_standing,2); $total_out_standing +=$out_standing; $total_out_standing_sub +=$out_standing ?></p></td>

							<td width="80" align="right"><p><? echo number_format($pay_data_atsite_arr[$import_invoice_id]["il"],2); $total_atsite_il +=$pay_data_atsite_arr[$import_invoice_id]["il"]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($pay_data_atsite_arr[$import_invoice_id]["bc"],2); $total_atsite_bc +=$pay_data_atsite_arr[$import_invoice_id]["bc"]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($pay_data_atsite_arr[$import_invoice_id]["int"],2); $total_atsite_int +=$pay_data_atsite_arr[$import_invoice_id]["int"]; ?></p></td>

							<td width="70"><p>&nbsp;<? if($row[('maturity_date')]!="0000-00-00") echo change_date_format($row[('maturity_date')]); else echo ""; ?></p></td>
							<td width="70"><p>
							<?
								if($row[('maturity_date')]=="00-00-0000")
								{
									$month="";
								}
								else
								{
									$month = date('F', strtotime($row[('maturity_date')]));
								}
								echo $month;
			
								?></p></td>
							<td width="70" ><p>&nbsp;<? if($pay_data_arr[$row[('id')]]["payment_date"]!="" && $pay_data_arr[$row[('id')]]["payment_date"]!="0000-00-00") echo change_date_format($pay_data_arr[$row[('id')]]["payment_date"]); ?></p></td>
							<td width="70"><p><? if($row[('shipment_date')]!="0000-00-00") echo change_date_format($row[('shipment_date')]); else echo ""; ?></p></td>
							<td width="70"><p>&nbsp;<? if($row[('lc_expiry_date')]!="0000-00-00")  echo change_date_format($row[('lc_expiry_date')]); else echo ""; ?></p></td>
							<td width="80"><p><? echo $lc_type[$row[('lc_type_id')]]; ?></p></td>
							<td width="70"><p>&nbsp;<? if($row[('etd_date')]!="0000-00-00") echo change_date_format($row[('etd_date')]); else echo ""; ?></p></td>
							<td width="70"><p>&nbsp;<? if($row[('eta_date')]!="0000-00-00") echo change_date_format($row[('eta_date')]); else echo ""; ?></p></td>
							<td width="100"><p><? echo $row[('bill_no')]; ?></p></td>
							<td width="70"><p><? echo $row[('feeder_vessel')]; ?></p></td>
							<td width="70"><p><? echo $row[('mother_vessel')]; ?></p></td>
							<td width="60"><p><? echo $row[('container_no')]; ?></p></td>
							<td width="70"><p><? echo $row[('pkg_quantity')]; ?></p></td>
							<td width="80"><p><? echo $row[('bill_of_entry_no')]; ?></p></td>
							<?
							//$receive_data_arr[$row["BOOKING_ID"]][$row["RECEIVE_BASIS"]]["cons_quantity"]+=$row["ORDER_QNTY"];
							//$receive_data_arr[$row["BOOKING_ID"]][$row["RECEIVE_BASIS"]]["cons_amount"]+=$row["ORDER_AMOUNT"];
							$receive_qnty=$receive_value=$receive_Return_qnty=$receive_Return_value=0;
							$all_wo_pi_id=$receive_basis="";
							$pi_id_all=explode(",",$row[('pi_mst_id')]);
							foreach($pi_id_all as $piid)
							{
								if($piGoodRcvArr[$piid]==1)
								{
									$pi_wo_ids[$row["ID"]].=$row["WORK_ORDER_ID"].",";
									$wo_id_arr=explode(",",chop($pi_wo_ids[$piid],","));
									foreach($wo_id_arr as $wo_id)
									{
										$receive_qnty += $receive_data_arr[$wo_id][2]["cons_quantity"];
										$receive_value += $receive_data_arr[$wo_id][2]["cons_amount"];
										$all_wo_pi_id.=$wo_id.",";
									}
									$receive_basis=2;
									
								}
								else
								{
									$receive_qnty += $receive_data_arr[$piid][1]["cons_quantity"];
									$receive_value += $receive_data_arr[$piid][1]["cons_amount"];
									$all_wo_pi_id.=$piid.",";
									$receive_basis=1;
								}
								
								$receive_Return_qnty += $receive_Return_data_arr[$piid]["cons_quantity"];
								$receive_Return_value += $receive_Return_data_arr[$piid]["cons_amount"];
							}
							$receive_actual_value=$receive_value-$receive_Return_value;
							$balance_value=$row[('lc_value')]-$receive_actual_value;
							?>
							<td width="80" align="right"><p><? echo number_format($receive_qnty,0,"",""); $total_receive_qnty+=$receive_qnty;  $total_receive_qnty_sub+=$receive_qnty;  ?></p></td>
							<td width="80" align="right"><p><? echo change_date_format($row[("release_date")]); ?></p></td>
							<td width="70"><p>&nbsp;<?  if($row[('copy_doc_receive_date')]!="0000-00-00")  echo change_date_format($row[('copy_doc_receive_date')]); else echo ""; ?></p></td>
							<td width="70"><p>&nbsp;<? if($row[('doc_to_cnf')]!="0000-00-00") echo change_date_format($row[('doc_to_cnf')]); else echo ""; ?></p></td>
							<td width="100"><p><? echo chop($lc_sc_no[$row[('btb_lc_id')]],", ");?></p></td>
							<td width="60" align="center"><p><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage_inHouse_date('".chop($all_wo_pi_id,",")."','".$receive_basis."','$receive_value','$receive_qnty','$item_id','pi_rec_details','PI Details','".$import_invoice_id."');\">"."View"."</a>"; //$row[("pi_id")]; ?></p></td>
							
							<td width="80" align="right" ><p><?   echo number_format($receive_actual_value,2); $total_receive_value+=$receive_actual_value; $total_receive_value_sub+=$receive_actual_value; ?></p></td>
							<td width="80" align="right" ><p><?   echo  number_format($balance_value,2);  $total_balance_value+=$balance_value;  $total_balance_value_sub+=$balance_value; ?></p></td>
							<td ><p><? echo $user_arr[$row[('inserted_by')]];?></p></td>
							
						</tr>
						<?
						$i++;
					}
				?>
				</table>
				<table cellspacing="0" width="3990px"  border="1" rules="all" class="rpt_table" id="report_table_footer" >
					<tfoot>
						<tr>
							<th width="30">&nbsp;</th>
							<th width="100" >&nbsp;</th>
							<th width="75" >&nbsp;</th>
							<th width="80" >&nbsp;</th>
							<th width="80" >&nbsp;</th>
							<th width="100" >&nbsp;</th>
							<th width="120" >&nbsp;</th>
							<th width="100" >&nbsp;</th>
							<th width="75" >&nbsp;</th>
							<th width="70" >&nbsp;</th>
							<th width="50" ><p>&nbsp;</p></th>
							<th width="50" ><p>&nbsp;</p></th>
							<th width="100" >&nbsp;</th>
							<th width="100" >&nbsp;</th>
							<th width="90" >&nbsp;</th>
							<th width="50" align="right">Total : </th>
							<th width="80" align="right" id="value_tot_lc_value_ww"><? echo number_format($tot_lc_value_cash_sub,2); ?></th>
							<th width="40" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="80" align="right" id="value_tot_bill_value_ww"><? echo number_format($tot_bill_value_sub,2); ?></th>
							<th width="90" align="right" id="value_gt_total_paid_ww"><? echo number_format($gt_total_paid_sub,2); ?></th>
							<th width="80" align="right" id="value_total_out_standing_ww"><? echo number_format($total_out_standing_sub,2); ?></th>
							<th width="80" align="right" id="value_atsite_il_ww"><? echo number_format($total_atsite_il_sub,2); ?></th>
							<th width="80" align="right" id="value_atsite_bc_ww"><? echo number_format($total_atsite_bc_sub,2); ?></th>
							<th width="80" align="right" id="value_atsite_int_ww"><? echo number_format($total_atsite_intsub,2); ?></th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="80" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="100" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="60" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="80" align="center">&nbsp;</th>
							<th align="right" id="total_qnty" width="80"><? echo number_format($total_receive_qnty_sub,2); ?></th>
							<th width="80" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="100" align="center">&nbsp;</th>
							<th width="60" align="center">&nbsp;</th>
							<th width="80" align="right" id="value_total_receive_ww_ww"><? echo number_format($total_receive_value_sub,2); ?></th>
							<th width="80" align="right" id="value_balance_value_ww_ww"><? echo number_format($total_balance_value_sub,2); ?></th>
							<th></th>
						</tr>
						<tr>
						    <th width="30">&nbsp;</th>
							<th width="100" >&nbsp;</th>
							<th width="75" >&nbsp;</th>
							<th width="80" >&nbsp;</th>
							<th width="80" >&nbsp;</th>
							<th width="100" >&nbsp;</th>
							<th width="120" >&nbsp;</th>
							<th width="100" >&nbsp;</th>
							<th width="75" >&nbsp;</th>
							<th width="70" >&nbsp;</th>
							<th width="50" ><p>&nbsp;</p></th>
							<th width="50" ><p>&nbsp;</p></th>					
							<th width="340" colspan="4"  align="right">LC CI Details & Cash In Advance CI Details Grand Total : </th>
							<th width="80" align="right" id="value_tot_lc_value_ww"><? echo number_format($tot_lc_value,2); ?></th>
							<th width="40" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="80" align="right" id="value_tot_bill_value_ww"><? echo number_format($tot_bill_value,2); ?></th>
							<th width="90" align="right" id="value_gt_total_paid_ww"><? echo number_format($gt_total_paid,2); ?></th>
							<th width="80" align="right" id="value_total_out_standing_ww"><? echo number_format($total_out_standing,2); ?></th>
							<th width="80" align="right" id="value_atsite_il_ww"><? echo number_format($total_atsite_il,2); ?></th>
							<th width="80" align="right" id="value_atsite_bc_ww"><? echo number_format($total_atsite_bc,2); ?></th>
							<th width="80" align="right" id="value_atsite_int_ww"><? echo number_format($total_atsite_int,2); ?></th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="80" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="100" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="60" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="80" align="center">&nbsp;</th>
							<th align="right" id="total_qnty" width="80"><? echo number_format($total_receive_qnty,2); ?></th>
							<th width="80" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="70" align="center">&nbsp;</th>
							<th width="100" align="center">&nbsp;</th>
							<th width="60" align="center">&nbsp;</th>
							<th width="80" align="right" id="value_total_receive_ww"><? echo number_format($total_receive_value,2); ?></th>
							<th width="80" align="right" id="value_balance_value_ww"><? echo number_format($total_balance_value,2); ?></th>
							<th></th>
						</tr>
					</tfoot>
			</table>
				<div align="left" style="font-weight:bold; margin-left:30px;"><? echo "User Id : ". $user_arr[$user_id] ." , &nbsp; THIS IS SYSTEM GENERATED STATEMENT, NO SIGNATURE REQUIRED ."; ?></div>
			</div>

		</div>
		<?
					
	}
	
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
    echo "$html****$filename****$report_type";
    exit();
}

if($action=="pi_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//print_r ($import_invoice_id);

	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	//$composition;
	//$yarn_type;
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
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
			document.getElementById('scroll_body').style.overflowY="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
		}

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<div style="width:800px" align="center" id="scroll_body" >
	<fieldset style="width:100%; margin-left:10px" >
	<!--<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;&nbsp;&nbsp;
	--><input type="button" value="Close" onClick="window_close()" style="width:100px"  class="formbutton"/>
			<div id="report_container" align="center" style="width:100%" >
				<div style="width:780px">
					<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
						<thead>
							<tr>
								<th colspan="4" align="center"><? echo $companyArr[$company_name]; ?></th>
							</tr>
							<tr>
								<th width="150"><strong>Supplier</strong></th>
								<th width="150"><strong>Item Category</strong></th>
								<th width="150"><strong>Currency</strong></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><? echo $supplierArr[$suppl_id]; ?></td>
								<td><? echo $item_category[$item_id]; ?></td>
								<td><? echo $currency[$curr_id]; ?></td>
							</tr>
						</tbody>
					</table>
					<br />
					<table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
						<thead bgcolor="#dddddd">
							<tr>
								<th width="30">SL</th>
								<th width="70">PI No.</th>
								<th width="70">PI Date</th>
								<th width="100">Item Group</th>
								<th width="130">Item Description</th>
								<th width="80">Qnty</th>
								<th width="70">Rate</th>
								<th width="80">Amount</th>
							</tr>
						</thead>
						<tbody>
					<?
			$i=1;
			//if ($pi_id==0) $piId =""; else $piId =" and a.id in ($pi_id)";
			$sql="Select a.id, a.pi_number,a.pi_date, b.item_prod_id, b.determination_id, b.item_group, b.item_description,a.item_category_id, b.size_id,b.color_id,b.count_name,b.yarn_type,b.yarn_composition_item1, b.yarn_composition_percentage1, b.quantity, b.rate, b.amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.id in ($pi_id) order by a.pi_number";
			//echo $sql;
			$result=sql_select($sql);

			$pi_arr=array();
			foreach( $result as $row)
			{
				$total_qnt+=$row[csf("quantity")];

				$total_amount+=$row[csf("amount")];

				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor ; ?>">
					<td><? echo $i; ?></td>
					<td><? echo $row[csf("pi_number")]; ?></td>
					<td><? echo change_date_format($row[csf("pi_date")]); ?></td>
					<td><? echo $itemgroupArr[$row[csf("item_group")]]; ?></td>
					<?
						if($row[csf("item_category_id")]==1)
						{
							$description = $yarn_count_arr[$row[csf("count_name")]]." ".$composition[$row[csf("yarn_composition_item1")]]." ".$row[csf("yarn_composition_percentage1")]."% ".$yarn_type[$row[csf("yarn_type")]]." ".$color_name_arr[$row[csf("color_id")]];
						}
						else
						{
							$description = $row[csf("item_description")];
						}
					?>
					<td><? echo $description; ?></td>

					<td align="right"><? echo $row[csf("quantity")]; ?></td>
					<td align="right"><? echo $row[csf("rate")]; ?></td>
					<td align="right"><? echo number_format($row[csf("amount")],2); ?></td>
				</tr>
			</tbody>
			<?
			$i++;
			}
			?>
				<tfoot>
					<th colspan="5" align="right">Total : </th>
					<th align="right"><? echo number_format($total_qnt,0); ?></th>
					<th>&nbsp;</th>
					<th align="right"><? echo number_format($total_amount,2); ?></th>
				</tfoot>
			</table>
			</div>
			</div>
		</fieldset>
	</div>
	<?
	exit();
}

if($action=="pi_rec_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//print_r ($pi_id);
	//echo "st".$invoice_id;die;

	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	
	
	
	
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
			document.getElementById('scroll_body').style.overflowY="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
		}

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<?
	if($category_id==25)
	{
		$i=1;
		$pi_id=str_replace("'","",$pi_id);
		$sql="SELECT a.pi_id,c.id as RCV_ID, c.challan_no as CHALLAN_NO,c.production_quantity as PRODUCTION_QUANTITY, c.production_date as PRODUCTION_DATE
		from com_pi_item_details a, wo_booking_dtls b, pro_garments_production_mst c where a.pi_id in ($pi_id) and a.work_order_dtls_id=b.id and b.po_break_down_id=c.po_break_down_id and c.production_type=3 order by a.id"; 
		// echo $sql;
		$result=sql_select($sql);
		$recIdArr=array();
		foreach( $result as $row)
		{
			$recIdArr[$row["RCV_ID"]]=$row["RCV_ID"];
		}
		
		$fileSql="select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='embellishment_receive_entry' ".where_con_using_array($recIdArr,1,'MASTER_TBLE_ID');
		$fileSqlRes=sql_select($fileSql);
		foreach( $fileSqlRes as $row)
		{
			$fileArr[$row["MASTER_TBLE_ID"]][$row["IMAGE_LOCATION"]]=$row["IMAGE_LOCATION"];
		}				
		
		?>
		<div style="width:600px" align="center" id="scroll_body" >
		<fieldset style="width:100%; margin-left:10px" >
			<input type="button" value="Close" onClick="window_close()" style="width:100px"  class="formbutton"/>
				<div id="report_container" align="center" style="width:100%" >
					<div style="width:480px">
						<table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
							<thead bgcolor="#dddddd">
								<tr>
									<th width="150">Rcv No</th>
									<th width="80">Rcv Date</th>
									<th width="80">Rcv Qty</th>
									<th>File</th>
								</tr>
							</thead>
							<tbody>
							<?
							$total_qnt=0;
							foreach( $result as $row)
							{
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor ; ?>">
									<td><? echo $row["CHALLAN_NO"]; ?></td>
									<td align="center"><? echo change_date_format($row["PRODUCTION_DATE"]); ?></td>
									<td align="right"><? echo number_format($row["PRODUCTION_QUANTITY"],2); ?></td>
									<td>
									<? foreach($fileArr[$row["RCV_ID"]] as $fileName){?>
										<a target="_blank" href="../../../<?=$fileName;?>">Download</a>&nbsp;
									<? } ?>
									</td>	
								</tr>
							<?
							$total_qnt+=$row["PRODUCTION_QUANTITY"];
							$i++;
							}
							?>
							</tbody>
							<tfoot>
								<th colspan="2" align="right">Total : </th>
								<th align="right"><? echo number_format($total_qnt,0); ?></th>
								<th>&nbsp;</th>
							</tfoot>
						</table>
					</div>
				</div>
			</fieldset>
		</div>
		<?
	}
	else
	{
		?>
		<div style="width:680px" align="center" id="scroll_body" >
		<fieldset style="width:100%; margin-left:10px" >
		<!--<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;&nbsp;&nbsp;
		--><input type="button" value="Close" onClick="window_close()" style="width:100px"  class="formbutton"/>
				<div id="report_container" align="center" style="width:100%" >
					<div style="width:660px">
						<table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
							<thead bgcolor="#dddddd">
								<tr>
									<th width="30">PO No</th>
									<th width="80">Challan No</th>
									<th width="120">MRR No</th>
									<th width="80">Received Date</th>
									<th width="80">Received Qty</th>
									<th width="80">Rate</th>
									<th width="80">Received Value</th>
									<th>File</th>
								</tr>
							</thead>
							<tbody>
						<?
				$i=1;
				//if ($pi_id==0) $piId =""; else $piId =" and a.id in ($pi_id)";
				//if($db_type==0) $grp_con=" group_concat(distinct b.cons_rate) as cons_rate";
			//	else  $grp_con="LISTAGG(po_break_down_id, ',') WITHIN GROUP (ORDER BY po_break_down_id) as po_break_down_id";
	
				$pi_id=str_replace("'","",$pi_id);
				$rcv_basis=str_replace("'","",$rcv_basis);
	
				$sql="SELECT a.id, a.recv_number, a.receive_date, a.challan_no, sum(b.order_qnty) as cons_quantity, sum(b.order_amount) as cons_amount 
				from com_import_invoice_dtls_mrr p, inv_receive_master a, inv_transaction b 
				where p.MRR_ID=a.id and a.id=b.mst_id and a.booking_id in ($pi_id) and a.receive_basis=$rcv_basis and b.receive_basis=$rcv_basis and b.transaction_type=1 and p.IMPORT_INVOICE_ID=$invoice_id group by a.recv_number,a.id,a.receive_date,a.challan_no order by a.id"; 
				//echo $sql;
				$result=sql_select($sql);
				$recIdArr=array();
				if($category_id==2)
				{
					foreach( $result as $row)
					{
						$recIdArr[$row[csf("id")]]=$row[csf("id")];
					}	
				}
				else
				{
					foreach( $result as $row)
					{
						$recIdArr[$row[csf("recv_number")]]=$row[csf("recv_number")];
					}
				}

				if($category_id==4){$formName=" FORM_NAME='trims_receive_entry' ";}
				elseif($category_id==1 || $category_id==24){$formName=" FORM_NAME='yarn_receive' ";}
				elseif($category_id==2){$formName=" FORM_NAME='knit_finish_fabric_receive_by_garments' ";}
				elseif($category_id==3){$formName=" FORM_NAME='woven_finish_fabric_receive' ";}

				$fileSql="select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where $formName ".where_con_using_array($recIdArr,1,'MASTER_TBLE_ID');
				// echo $fileSql;
				$fileSqlRes=sql_select($fileSql);
				foreach( $fileSqlRes as $row)
				{
					$fileArr[$row["MASTER_TBLE_ID"]][$row["IMAGE_LOCATION"]]=$row["IMAGE_LOCATION"];
				}
  
				 
				$pi_arr=array();
				foreach( $result as $row)
				{
					$total_qnt+=$row[csf("cons_quantity")];
					$total_amount+=$row[csf("cons_amount")];
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor ; ?>">
						<td><? echo $pi_id.'***'.$category_id; ?></td>
						<td><? echo $row[csf("challan_no")];; ?></td>
						<td><? echo $row[csf("recv_number")]; ?></td>
						<td align="center"><? echo change_date_format($row[csf("receive_date")]); ?></td>
						<td align="right"><? echo $row[csf("cons_quantity")];//$receive_qnty; ?></td>
						<td align="right"><? echo number_format($row[csf("cons_amount")]/$row[csf("cons_quantity")],2); ?></td>
						<td align="right"><? echo number_format($row[csf("cons_amount")],2);//number_format($receive_value,2); ?></td>
						<td>
						<? 
						if($category_id==2){
							foreach($fileArr[$row[csf("id")]] as $fileName)
							{?>
								<a target="_blank" href="../../../<?=$fileName;?>">Download</a>&nbsp;
							<? 
							} 
						}
						else
						{
							foreach($fileArr[$row[csf("recv_number")]] as $fileName)
							{?>
								<a target="_blank" href="../../../<?=$fileName;?>">Download</a>&nbsp;
							<? 
							} 
						}

						?>
						</td>	
					</tr>
				</tbody>
				<?
				$i++;
				}
				?>
				<tfoot>
					<th colspan="3" align="right">Total : </th>
					<th align="right"><? echo number_format($total_qnt,0); ?></th>
					<th>&nbsp;</th>
					<th align="right"><? echo number_format($total_amount,2); ?></th>
				</tfoot>
			</table>
			</div>
			</div>
			</fieldset>
		</div>
		<?
	}
	exit();
}

if($action=="payment_details")
{

	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//print_r ($pi_id);

	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	?>
	<script>

	/*	function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
		}
	*/
		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<div style="width:600px" align="center" id="scroll_body" >
	<fieldset style="width:100%; margin-left:10px" >
	<!--<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;&nbsp;&nbsp;
	--><input type="button" value="Close" onClick="window_close()" style="width:100px"  class="formbutton"/>
		<div id="report_container" align="center" style="width:100%" >
		<table class="rpt_table" border="1" rules="all" width="590" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="120" title="Acceptance Bill Payment">ABP</th>
					<th width="120">Adj. source</th>
					<th width="80">Pay Date</th>
					<th width="80">Conversion Rate</th>
					<th width="80">Accepted Amount</th>
					<th >Domistic Currency</th>
				</tr>
			</thead>
		<tbody>
			<?
			$i=1;
			//if ($pi_id==0) $piId =""; else $piId =" and a.id in ($pi_id)";
			$sql="SELECT id, payment_head, payment_date, adj_source, conversion_rate, sum(accepted_ammount) as accepted_ammount,sum(domistic_currency) as domistic_currency from com_import_payment where invoice_id=$invoice_id and status_active=1 group by id, payment_head, adj_source, conversion_rate,payment_date
			union all
			SELECT id, payment_head, payment_date, adj_source, conversion_rate, sum(accepted_ammount) as accepted_ammount,sum(domistic_currency) as domistic_currency from com_import_payment_com where invoice_id=$invoice_id and status_active=1 group by id, payment_head, adj_source, conversion_rate,payment_date
			";
			//echo $sql;
			$result=sql_select($sql);
			foreach( $result as $row)
			{
				$total_accept+=$row[csf("accepted_ammount")];
				$total_document+=$row[csf("domistic_currency")];

				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor ; ?>">
					<td><? echo $i; ?></td>
					<td><? echo $commercial_head[$row[csf("payment_head")]]; ?></td>
					<td><? echo $commercial_head[$row[csf("adj_source")]]; ?></td>
					<td align="center"><? echo change_date_format($row[csf("payment_date")]); ?></td>
					<td align="right"><? echo $row[csf("conversion_rate")];//$receive_qnty; ?></td>
					<td align="right"><? echo number_format($row[csf("accepted_ammount")],2); ?></td>
					<td align="right"><? echo number_format($row[csf("domistic_currency")],2);//number_format($receive_value,2); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</tbody>
		<tfoot>
			<th colspan="5" align="right">Total : </th>
			<th align="right"><? echo number_format($total_accept,2); ?></th>
			<th align="right"><? echo number_format($total_document,2); ?></th>
		</tfoot>
		</table>
		</div>
		</fieldset>
	</div>
	<?
	exit();
}
?>
