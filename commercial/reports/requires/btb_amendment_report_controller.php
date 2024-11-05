<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_supplier_dropdown")
{
	//echo $data;
	$data = explode('_',$data);
	
	if ($data[1]==0) 
	{
		//echo create_drop_down( "cbo_supplier_id", 165, $blank_array,'', 1, '----Select----',0,0,0);
		echo create_drop_down( "cbo_supplier_id",165,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==1)
	{
		echo create_drop_down( "cbo_supplier_id",165,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);		
	}
	else if($data[1]==2 || $data[1]==3 || $data[1]==13 || $data[1]==14)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name, c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =9 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==4)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
		
	}
	else if($data[1]==5 || $data[1]==6 || $data[1]==7)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type=3 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==8)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 7 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==9 || $data[1]==10)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 6 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	} 
	else if($data[1]==11)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 8 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==12 || $data[1]==24 || $data[1]==25)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(20,21,22,23,24,30,31,32,35,36,37,38,39) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==32)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(92) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==110)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '----Select----',0,0,0);
	}
	else
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 7 and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	} 
	
	exit(); 
}

if($action=="lc_sc_search")
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
			//alert(strCon);
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];

			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			var isLcSc = splitSTR[3];
			
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
			$('#txt_selected_no').val( num );
			$('#is_lc_or_sc').val( isLcSc );
		}
		
		function fnClosed_lc() 
		{   
			parent.emailwindow.hide();
		}
    </script>
    <body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="980" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
                    	<th>Company</th>
                    	<th>Item Category</th>
                        <th>Supplier</th>
                        <th>L/C Date</th>
                        <th>System Id</th>
                        <th>LC No</th>
						<th>
                       		<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
                            <input type='hidden' id='txt_selected_id' />
							<input type='hidden' id='txt_selected' />
							<input type='hidden' id='txt_selected_no' />
                            <input type='hidden' id='is_lc_or_sc' />
                        </th>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<td>
                           <? 
								echo create_drop_down( "txt_company_id",150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',  $company  ,"load_drop_down( 'btb_amendment_report_controller',this.value+'_'+document.getElementById('cbo_item_category_id').value,'load_supplier_dropdown','supplier_td' );",0); 
							?>  
                         </td>
						<td> 
                             <? echo create_drop_down( "cbo_item_category_id", 140, $item_category,'', 1, '--Select--',0,"load_drop_down( 'btb_amendment_report_controller',document.getElementById('txt_company_id').value+'_'+this.value,'load_supplier_dropdown','supplier_td' );",0,'','','','74,72,79,73,71,77,78,75,76'); ?>  
                        </td>
                         <td align="center" id="supplier_td">
                          <? echo create_drop_down( "cbo_supplier_id", 165,$blank_array,'', 1, '----Select----',0,0,0); ?>       
                         </td>            
						<td> 
                        	 <input type="text" name="btb_start_date" id="btb_start_date" class="datepicker" style="width:70px;" />To
                             <input type="text" name="btb_end_date" id="btb_end_date" class="datepicker" style="width:70px;" />
                        </td>						
						<td id="search_by_td">
							<input type="text" style="width:90px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                            <input type="hidden" id="hidden_btb_id" />
            			</td>
                        <td >
							<input type="text" style="width:90px" class="text_boxes"  name="txt_lc_no" id="txt_lc_no" />
            			</td> 
						
						<td>
                 		  	<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_company_id').value+'**'+document.getElementById('cbo_item_category_id').value+'**'+document.getElementById('cbo_supplier_id').value+'**'+document.getElementById('btb_start_date').value+'**'+document.getElementById('btb_end_date').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_lc_no').value, 'create_btb_search_list_view', 'search_div', 'btb_amendment_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
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

if($action=="create_btb_search_list_view")
{
	$data=explode('**',$data);
	$company_id = $data[0];
	$item_category_id = $data[1];
	$supplier_id = $data[2];
	$lc_start_date = $data[3];
	$lc_end_date = $data[4];
	$system_id = $data[5];
	$lc_num = trim($data[6]);
		
	if($company_id==0)
	{
		echo 'Select Importer';die;
	}
	
	if ($company_id!=0) $company=$company_id;
	if ($item_category_id==0) $item_category_cond="%%"; else $item_category_cond=$item_category_id;
	if ($supplier_id!=0) $supplier=$supplier_id; else $supplier='%%';
	if ($system_id!='') $system_number=$system_id; else $system_number='%';
	if ($lc_num!='') $lc_number_cond=" and lc_number like '%".$lc_num."'"; else $lc_number_cond='';

	if($lc_start_date!='' && $lc_end_date!='')
	{
		if($db_type==0)
		{
			$date = "and application_date between '".change_date_format($lc_start_date,'yyyy-mm-dd')."' and '".change_date_format($lc_end_date,'yyyy-mm-dd')."'";
		}
		else if($db_type==2)
		{
			$date = "and application_date between '".change_date_format($lc_start_date,'','',1)."' and '".change_date_format($lc_end_date,'','',1)."'";
		}
	}
	else
	{
		$date = "";
	}
	
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	$sql = "SELECT id, $year_field btb_prefix_number, btb_system_id, lc_number, supplier_id, application_date, last_shipment_date, lc_date, lc_value, item_category_id, importer_id FROM com_btb_lc_master_details WHERE btb_system_id like '%".$system_number."' and importer_id = '".$company."' and supplier_id like '".$supplier."' and item_category_id like '".$item_category_cond."' $date $lc_number_cond and is_deleted = 0 order by item_category_id, id";
	//echo $sql;
	
	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name'); 
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$exportPiSuppArr = array();
	$exportPiSupp = sql_select("select c.import_pi, a.id from com_btb_lc_master_details a , com_btb_lc_pi b , com_pi_master_details c where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id");
	foreach ($exportPiSupp as $value) 
	{
		$exportPiSuppArr[$value[csf("id")]] = $value[csf("import_pi")];
	}
	unset($exportPiSupp);
	//$arr=array(0=>$item_category,3=>$supplier_lib);COM_PI_MASTER_DETAILS
	//echo create_list_view("list_view", "Item Category,Year,System Id,Supplier,L/C Number,L/C Date,L/C Value,Application Date,Last Ship Date", "110,55,65,150,150,80,100,100,100","980","320",0, $sql , "js_set_value", "id", "",1,"item_category_id,0,0,supplier_id,0,0,0,0,0", $arr , "item_category_id,year,btb_prefix_number,supplier_id,lc_number,lc_date,lc_value,application_date,last_shipment_date","",'','0,0,0,0,0,3,2,3,3') ;
	?>
	<table width="990" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead> 
            <th width="40">SL</th>
            <th width="110">Item Category</th>
            <th width="55">Year</th>
            <th width="65">System Id</th>
            <th width="150">Supplier</th>
            <th width="150">L/C Number</th>
            <th width="80">L/C Date</th>
            <th width="100">L/C Value</th>
            <th width="100">Application Date</th>
            <th>Last Ship Date</th>
        </thead>
     </table>
     <div>
	     <div style="width:990px; overflow-y:scroll; max-height:280px">  
	     	<table width="970" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view"> 
			<?
				$data_array=sql_select($sql); $i = 1; 
	            foreach($data_array as $row)
	            { 
	                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$supplier='';
					if($row[csf('item_category_id')]==110)
					{
						$supplier=$comp[$row[csf('supplier_id')]];
					}
					else
					{
						$supplier=$supplier_lib[$row[csf('supplier_id')]];
					}

					if($exportPiSuppArr[$row[csf('id')]] == 1){
						$supplier=$comp[$row[csf('supplier_id')]];
					}
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="tr_<? echo $i;?>" onClick="js_set_value( '<? echo $i."_".$row[csf('id')]."_".$row[csf('lc_number')]."_"."1"; ?>');">
	                	<td width="40"><? echo $i; ?></td>
						<td width="110"><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
						<td width="55" align="center"><? echo $row[csf('year')]; ?></td>
	                    <td width="65"><? echo $row[csf('btb_prefix_number')]; ?></td>
						<td width="150"><p><? echo $supplier; ?></p></td>
	                    <td width="150"><p><? echo $row[csf('lc_number')]; ?></p></td>
	                    <td width="80" align="center"><p><? echo change_date_format($row[csf('lc_date')]); ?></p></td>
	                    <td width="100" align="right"><? echo number_format($row[csf('lc_value')],2); ?>&nbsp;</td>
						<td width="100" align="center"><? echo change_date_format($row[csf('application_date')]); ?>&nbsp;</td>
	                    <td align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?>&nbsp;</td>
					</tr>
	            <?
					$i++;
	            }		
				?>
			</table>
		</div>
		<table width="980">
				<tr>
					<td align="center">
						<input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fnClosed_lc()" />
						
					</td>
				</tr>
			</table>
	    </div>
<?	
	exit();	 
}

if($action=="create_lcSc_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$lien_bank = str_replace("'","",$ex_data[3]);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$lien_bank_arr=return_library_array( "select id,bank_name from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1",'id','bank_name');
	if($txt_search_by==1)
	{
		$sql_cond="";
		if($txt_search_common!="") $sql_cond=" and export_lc_no LIKE '%$txt_search_common%'";
		if($lien_bank>0)  $sql_cond.=" and lien_bank=$lien_bank";
		$sql = "select id,beneficiary_name,buyer_name,lien_bank,export_lc_no,lc_value, 1 as lc_sc from com_export_lc where beneficiary_name=$company and status_active=1 and is_deleted=0 $sql_cond"; 
		
	}
	else
	{
		$sql_cond="";
		if($txt_search_common!="") $sql_cond=" and contract_no LIKE '%$txt_search_common%'";
		if($lien_bank>0)  $sql_cond.=" and lien_bank=$lien_bank";
		$sql = "select id,beneficiary_name,buyer_name,lien_bank,contract_no as export_lc_no,contract_value as lc_value, 2 as lc_sc from  com_sales_contract where beneficiary_name=$company and status_active=1 and is_deleted=0 $sql_cond"; 
	}
	//echo $sql;die;
	$arr=array(0=>$company_arr,1=>$buyer_arr,2=>$lien_bank_arr);
	//function create_list_view( $table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr,  $show_sl, $field_printed_from_array_arr,  $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all ,$new_conn )
	echo create_list_view("list_view", "Company,Buyer,Lien Bank,Lc/Sc No,Value","120,120,120,100","600","260",0, $sql , "js_set_value", "id,export_lc_no,lc_sc", "", 1, "beneficiary_name,buyer_name,lien_bank,0,0", $arr, "beneficiary_name,buyer_name,lien_bank,export_lc_no,lc_value", "","","0,0,0,0,2","",1) ;
	exit();	
}

if($action=="pi_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//print_r ($pi_id);
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
<div style="width:800px" align="center" id="scroll_body" >
<fieldset style="width:100%; margin-left:10px" >
<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="Close" onClick="window_close()" style="width:100px"  class="formbutton"/>
         <div id="report_container" align="center" style="width:100%" > 
             <div style="width:780px">
                <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th colspan="4" align="center"><? echo $companyArr[$company_name]; ?></th>
                    </thead>
                    	<tr>
                            <td width="150"><strong>LC Number : </strong></td> <td width="150"><strong><?  echo $lc_number; ?></strong></td>
                            <td><strong>Last Ship Date :</strong></td><td><strong><?  echo change_date_format($ship_date); ?></strong></td>
                        </tr>
                    	<tr>
                            <td width="150"><strong>Supplier : </strong></td> <td width="150"><strong><?  echo $supplierArr[$supplier_id]; ?></strong></td>
                            <td><strong>Expiry Date :</strong></td><td><strong><?  echo change_date_format($exp_date); ?></strong></td>
                        </tr>
                    	<tr>
                            <td width="150"><strong>LC Date : </strong></td> <td width="150"><strong><?  echo change_date_format($lc_date); ?></strong></td>
                            <td><strong>Pay Term :</strong></td><td><strong><?  echo $pay_term[$payterm]; ?></strong></td>
                        </tr>
                </table>
                <br />
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                	 <thead bgcolor="#dddddd">
                     	<tr>
                            <th width="30">SL</th>
                            <th width="80">PI NO.</th>
                            <th width="100">Item Group</th>
                            <th width="130">Item Description</th>
                            <th width="80">Qnty</th>
                            <th width="70">Rate</th>
                            <th width="90">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                <?
		$i=1;
		//if ($pi_id==0) $piId =""; else $piId =" and a.id in ($pi_id)";
		$yarncountArr = return_library_array("SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0","id","yarn_count"); 

		$sql="Select a.item_category_id,a.id, a.pi_number, b.item_prod_id, b.determination_id, b.item_group, b.item_description, b.size_id, b.quantity, b.rate, b.amount,b.count_name ,b.yarn_composition_item1 ,b.yarn_type,b.yarn_composition_percentage1 from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.id in ($pi_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.pi_number";
		$result=sql_select($sql);
		
		$pi_arr=array();
		foreach( $result as $row)
		{
/*			if (!in_array($row[csf("pi_number")],$pi_arr) )
			{
				$pi_arr[]=$row[csf('pi_number')];
*/			?>
                   
<!--                        <tr>
                            <td colspan="6" align="left">PI No : <? //echo $row[csf('pi_number')]; ?></td>
                        </tr>
-->                    	
					<?
					$total_qnt+=$row[csf("quantity")];
					$total_amount+=$row[csf("amount")];
					
             // }
				if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
            <tr bgcolor="<? echo $bgcolor ; ?>">
            	<td><? echo $i; ?></td>
                 <td><? echo $row[csf("pi_number")]; ?></td>
                <td><? echo $itemgroupArr[$row[csf("item_group")]]; ?></td>
                <td>
            		<? 
            		if($row[csf("item_category_id")]==1)
            		{
            			echo $yarncountArr[$row[csf("count_name")]].' '.$composition[$row[csf("yarn_composition_item1")]].' '.$row[csf("yarn_composition_percentage1")].'% '.$yarn_type[$row[csf("yarn_type")]]; 
            		}
            		else
            		{ 
            			echo $row[csf("item_description")]; 
            		}
            		 ?>
            		 	
            	</td>
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
                <th colspan="4" align="right">Total : </th>
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

if ($action=="report_generate")
{
	extract($_REQUEST);
	//ob_start();
	//echo $cbo_company_id;
	$btb_lc_val_arr = return_library_array("select id, lc_value as lc_value from com_btb_lc_master_details where  status_active=1 and is_deleted=0","id","lc_value");
	$buyer_name_library = return_library_array("SELECT id,buyer_name FROM lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
	$companyArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name"); 
	$supplierArr = return_library_array("select id,short_name from lib_supplier where status_active=1 and is_deleted=0","id","short_name");
	$issueBankrArr = return_library_array("select id,bank_name from lib_bank ","id","bank_name"); 
	$hscodeArr = return_library_array("select id,hs_code from com_pi_master_details ","id","hs_code"); 
	$lc_num_arr = return_library_array("select id,export_lc_no from com_export_lc ","id","export_lc_no"); 
	$sc_num_arr = return_library_array("select id,contract_no from com_sales_contract ","id","contract_no"); 

	//cbo_company_id*cbo_issue_banking*txt_lc_sc_id*txt_lc_sc*txt_amendment_no*cbo_based_on*txt_date_from*txt_date_to
	$cbo_company=str_replace("'","",$cbo_company_id);
	$issue_banking_id=str_replace("'","",$cbo_issue_banking);
	$lc_sc_no=str_replace("'","",$txt_lc_sc);
	$hdn_lc_ids=str_replace("'","",$txt_lc_sc_id);
	$amendment_no=str_replace("'","",$txt_amendment_no);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);

	if ($cbo_company==0) $company_id =""; else $company_id =" and a.importer_id=$cbo_company ";
	if ($issue_banking_id==0) $issue_banking =""; else $issue_banking =" and a.issuing_bank_id=$issue_banking_id ";
	if ($amendment_no=='') $amendment =""; else $amendment =" and b.amendment_no like '%$lc_sc_no%'";
	
	if ($hdn_lc_ids=='' && $lc_sc_no!='') 
	{
		$lc_no=" and a.lc_number like '%$lc_sc_no%' ";
	}
	else if($hdn_lc_ids!='')
	{
		$lc_no=" and a.id in ($hdn_lc_ids) ";
	}
	else{
		$lc_no="";
	}

	//$issue_banking =""; else $issue_banking =" and a.issuing_bank_id=$cbo_issue ";

	$file_sql_all=sql_select("select b.id, a.internal_file_no, 1 as type from com_export_lc a, com_btb_lc_master_details b, com_btb_export_lc_attachment c where a.id=c.lc_sc_id and c.import_mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.is_lc_sc=0
	union all
	select b.id, a.internal_file_no, 2 as type from com_sales_contract a, com_btb_lc_master_details b, com_btb_export_lc_attachment c where a.id=c.lc_sc_id and c.import_mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.is_lc_sc=1");
	$file_no_arr=array();
	foreach($file_sql_all as $row)
	{
		$file_no_arr[$row[csf("id")]]=$row[csf("internal_file_no")];
	}
	
	if($db_type==2)
	{
		$lc_sc_sql=sql_select("Select a.id,b.is_lc_sc ,LISTAGG(CAST(b.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.lc_sc_id) as lc_sc_id
				from com_btb_lc_master_details a, com_btb_export_lc_attachment b
				where a.id=b.import_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id 
				group by a.id,b.is_lc_sc");

		
	}
	else if($db_type==0)
	{
		$lc_sc_sql=sql_select("Select a.id,b.is_lc_sc ,group_concat(b.lc_sc_id) as lc_sc_id
				from com_btb_lc_master_details a, com_btb_export_lc_attachment b
				where a.id=b.import_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id 
				group by a.id,b.is_lc_sc");
	}
	
	foreach($lc_sc_sql as $row)
	{
		$ls_sc_data[$row[csf("id")]]['is_lc_sc']=$row[csf("is_lc_sc")];
		$ls_sc_data[$row[csf("id")]]['lc_sc_id']=$row[csf("lc_sc_id")];
	}

	if($db_type==2)
	{
		if($cbo_based_on==1)
		{
				if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and b.amendment_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		}
		else if($cbo_based_on==2)
		{
				if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and a.lc_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		}
		else
		{
			if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and a.insert_date between '".date("j-M-Y",strtotime($from_date)) ." 12:00:01 AM"."' and '".date("j-M-Y",strtotime($to_date)). " 11:59:59 PM"."'";
		}
		$select_insert_date=" to_char(a.insert_date,'DD-MM-YYYY') as  insert_date";
		
	}
	else if($db_type==0)
	{
		if($cbo_based_on==1)
		{
			if( $from_date=='' && $to_date=='' ) $lc_date=""; else $lc_date= " and b.amendment_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		else if($cbo_based_on==2)
		{
			if( $from_date=='' && $to_date=='' ) $lc_date=""; else $lc_date= " and a.lc_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		else
		{
			if( $from_date=='' && $to_date=='' ) $lc_date=""; else $lc_date= " and a.insert_date between '".change_date_format($from_date,'yyyy-mm-dd'). " 00:00:01"."' and '".change_date_format($to_date,'yyyy-mm-dd'). " 23:59:59"."'";
		}
		
		$select_insert_date=" DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date";
	}

	$all_data=array();
	$sql="Select a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, a.item_category_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, a.status_active ,b.id as amen_id,b.amendment_no ,b.amendment_date ,b.amendment_value ,b.value_change_by ,b.btb_lc_value,b.is_original, $select_insert_date
			from com_btb_lc_master_details a ,com_btb_lc_amendment b  
			where a.id=b.btb_id and a.is_deleted=0 and b.is_deleted=0 and b.amendment_no!=0 $company_id  $issue_banking $lc_date $lc_no $amendment";
//echo $sql; die;
//Internal File No	Supply Source	LC/SC	BTB LC Date	Insert Date	Supplier	Curr.	LC  Open Value	Amendment Type 	Amendment date	Amendment Amount	Amendment Number
	
	$result_lc_sql=sql_select($sql); 
	foreach($result_lc_sql as $row)
	{  
		if($row[csf("lc_date")]!="" && $row[csf("lc_date")]!="0000-00-00") $lcDate= change_date_format($row[csf("lc_date")]);
		if($row[csf("amendment_date")]!="" && $row[csf("amendment_date")]!="0000-00-00") $amenDate= change_date_format($row[csf("amendment_date")]);

		$all_data[$row[csf('id')]][$row[csf('amen_id')]]['id'] 				=$row[csf('id')];
		$all_data[$row[csf('id')]][$row[csf('amen_id')]]['lc_number'] 		=$row[csf('lc_number')];
		$all_data[$row[csf('id')]][$row[csf('amen_id')]]['lc_category'] 	=$row[csf('lc_category')];
		$all_data[$row[csf('id')]][$row[csf('amen_id')]]['lc_date'] 		=$lcDate;
		
		$all_data[$row[csf('id')]][$row[csf('amen_id')]]['supplier_id'] 	=$supplierArr[$row[csf('supplier_id')]];
		$all_data[$row[csf('id')]][$row[csf('amen_id')]]['currency_id'] 	=$currency[$row[csf('currency_id')]];
		$all_data[$row[csf('id')]][$row[csf('amen_id')]]['value_change_by'] =$increase_decrease[$row[csf('value_change_by')]];
		$all_data[$row[csf('id')]][$row[csf('amen_id')]]['amendment_value'] =$row[csf('amendment_value')];
		$all_data[$row[csf('id')]][$row[csf('amen_id')]]['amendment_no'] 	=$row[csf('amendment_no')];
		$all_data[$row[csf('id')]][$row[csf('amen_id')]]['lc_open_value'] 	=$row[csf('btb_lc_value')];
		$all_data[$row[csf('id')]][$row[csf('amen_id')]]['is_original'] 	=$row[csf('is_original')];
		$all_data[$row[csf('id')]][$row[csf('amen_id')]]['insert_date'] 	=$row[csf('insert_date')];
		$all_data[$row[csf('id')]][$row[csf('amen_id')]]['amendment_date'] 	=$amenDate;
	}

	
	ob_start();
	?>
	<div>
		<table width="1265px" >
		<?
        $company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id=".$cbo_company_id."");
        foreach( $company_library as $row)
        {
        ?>
            <tr>
                <td colspan="14" align="center" style="font-size:22px"><center><strong><? echo $row[csf('company_name')];?></strong></center></td>
            </tr>
    <!--        <span style="font-size:20px"><center><b><?// echo $row[csf('company_name')];?></b></center></span>
    -->	<?
        }
        ?>
        <tr>
            <td colspan="14" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?></u></strong></center></td>
        </tr>
    </table>

		<table cellspacing="0" cellpadding="0" width="1265"  rules="all" class="rpt_table" border="1" >
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="120">LC Number</th>
					<th width="70">Internal File No</th>
					<th width="120">Supply Source</th>
					<th width="120">LC/SC</th>
					<th width="80">BTB LC Date</th>
					<th width="80">Insert Date</th>
					<th width="120">Supplier</th>
					<th width="60">Curr.</th>
					<th width="100">LC  Open Value</th>
					<th width="80">Amendment Type</th>
					<th width="80">Amendment date</th>
					<th width="100">Amendment Amount</th>
					<th width="100">Amendment Number</th>
				</tr>
			</thead>
		</table>
		<div style="width:1285px; overflow-y:scroll; max-height:400px; overflow-x:hidden;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" width="1265"  rules="all" class="rpt_table" border="1" id="" style="margin-left:10px;">
				<tbody>
					<? 
					$i=1; $check_arr_cont=array(); $check_arr_pi=array(); $check_arr_lc=array(); 
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$total_lc_value=0; $total_amendment_value=0;
				 	foreach($all_data as $lc_id => $lc_data_arr)
					{
						foreach ($lc_data_arr as $amen_id => $amen_data_arr)
						{//30 120 70 120 120 80 80 120 60 100 80 80 100 100 
							?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="120"><? echo $amen_data_arr['lc_number']; ?>&nbsp;</td>
							<td width="70"><? echo $file_no_arr[$amen_data_arr['id']]; ?>&nbsp;</td>
							<td width="120"><? echo $supply_source[(int)$amen_data_arr['lc_category']]; ?>&nbsp;</td>
							<td width="120"><?
								$lc_sc_num="";
								$p=1;
								
								$lc_sc_id_arr=array_unique(explode(",",$ls_sc_data[$amen_data_arr['id']]['lc_sc_id']));
								//echo "<pre>";
								//print_r($lc_sc_id_arr);
								foreach($lc_sc_id_arr as $lc_sc_id)
								{
									if($p!=1) $lc_sc_num .=", ";
									if($ls_sc_data[$amen_data_arr['id']]['is_lc_sc']==0)
									{
										$lc_sc_num .=$lc_num_arr[$lc_sc_id];
									}
									else
									{
										$lc_sc_num .=$sc_num_arr[$lc_sc_id];
									}
									$p++;
								}
								echo $lc_sc_num;
								?> &nbsp;</td>
							<td width="80" align="center"><? echo $amen_data_arr['lc_date']; ?>&nbsp;</td>
							<td width="80" align="center"><? echo $amen_data_arr['insert_date']; ?>&nbsp;</td>
							<td width="120"><? echo $amen_data_arr['supplier_id']; ?>&nbsp;</td>
							<td width="60" align="center"><? echo $amen_data_arr['currency_id']; ?>&nbsp;</td>
							<td width="100" align="right"><? echo number_format($amen_data_arr['lc_open_value'],2); $total_lc_value+=$amen_data_arr['lc_open_value']; ?>&nbsp;</td>
							<td width="80"><? echo $amen_data_arr['value_change_by']; ?>&nbsp;</td>
							<td width="80" align="center"><? echo $amen_data_arr['amendment_date']; ?>&nbsp;</td>
							<td width="100" align="right"><? echo number_format($amen_data_arr['amendment_value'],2); $total_amendment_value+=$amen_data_arr['amendment_value']; ?>&nbsp;</td>
							<td width="100"><? echo $amen_data_arr['amendment_no']; ?>&nbsp;</td>
						</tr>
						<?
						$i++;
						}
					}
					?>
				</tbody>
			</table>
		</div>
		<table cellspacing="0" width="1265"  border="1" rules="all" class="rpt_table">
			<tfoot>
				<tr>
					<th width="30">&nbsp;</th>
					<th width="120">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="120">&nbsp;</th>
					<th width="120">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="120">&nbsp;</th>
					<th width="60">Total</th>
					<th width="100"><? echo number_format($total_lc_value,2); ?></th>
					<th width="80">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="100"><? echo number_format($total_amendment_value,2); ?></th>
					<th width="100">&nbsp;</th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
	foreach (glob("$user_id*.xls") as $filename) {
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');	
		$is_created = fwrite($create_new_doc,ob_get_contents());
		echo "$html****$filename****$item_category_id"; 
		exit();	

}

if($action=="lc_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>	
<script>
	
	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<div style="width:570px" align="center" id="scroll_body" >
<fieldset style="width:100%; margin-left:10px" >
<!--<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;&nbsp;&nbsp;-->
	<input type="button" value="Close" onClick="window_close()" style="width:100px"  class="formbutton"/>
     <div style="width:550px" id="report_container" align="center">
     <?
	 if($int_file!="")
	 {
		 ?>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
            <thead>
                <th width="250">File Number : </th>&nbsp;<th><? echo $int_file; ?></th>
            </thead>
        </table>
        <?
	 }
			$ls_sql="select a.export_lc_no, a.expiry_date from com_export_lc a, com_btb_lc_master_details b, com_btb_export_lc_attachment c where a.id=c.lc_sc_id and c.import_mst_id=b.id and  b.id=$lc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and c.is_lc_sc=0";
			 
			$result_ls_sql=sql_select($ls_sql);
			if(count($result_ls_sql)>0)
			{
		?>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
            <thead>
                <th width="40">SL</th>
                <th width="200">LC Number</th>
                <th width="200">Expiry Date</th>                    
            </thead>
            <tbody>
            <?
				$i=1;
				foreach( $result_ls_sql as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor ; ?>">
                        <td><? echo $i; ?></td>
                        <td><? echo $row[csf("export_lc_no")]; ?></td>
                        <td><? echo change_date_format($row[csf("expiry_date")]); ?></td>
                    </tr>
                <?
				}
            ?>
            </tbody>
        </table>
        <?
			}
		?>
        <br />
		<?
			$sc_sql="select a.contract_no, a.expiry_date from com_sales_contract a, com_btb_lc_master_details b, com_btb_export_lc_attachment c where a.id=c.lc_sc_id and c.import_mst_id=b.id and  b.id=$lc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.is_lc_sc =1";
			$result_sc_sql=sql_select($sc_sql);
			if(count($result_sc_sql)>0)
			{
		?>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
            <thead>
                <th width="40">SL</th>
                <th width="200">LC Number</th>
                <th width="200">Expiry Date</th>                    
            </thead>
            <tbody>
            <?
				$i=1;
				foreach( $result_sc_sql as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor ; ?>">
                        <td><? echo $i; ?></td>
                        <td><? echo $row[csf("contract_no")]; ?></td>
                        <td><? echo change_date_format($row[csf("expiry_date")]); ?></td>
                    </tr>
                <?
				}
            ?>
            </tbody>
        </table>
        <?
			}
		?>
       </div>
</fieldset>

</div>
<?	
	
}
if($action == "receive_return_details")
{
    
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//print_r ($pi_id);
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$piNoArr = return_library_array("select id,pi_number from  com_pi_master_details where status_active=1 and is_deleted=0","id","pi_number");
?>	
	
<div style="width:600px" align="center" id="scroll_body" >
<fieldset style="width:100%; margin-left:10px" >
         <div id="report_container" align="center" style="width:100%" > 
             <div style="width:600px">
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead bgcolor="#dddddd">
                     	<tr>
                            <th width="30">SL</th>
                            <th width="80">PI NO.</th>
                            <th width="100">Receive No.</th>
                            <th width="130">Receive Date</th>
                            <th width="100">Qnty</th>
                            <th width="80">Rate</th>
                            <th width="">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                <?
		$i=1;
                $pi_ids = implode(",",array_filter(explode(",",chop($pi_ids,","))));
		if ($pi_ids=="" || $pi_ids == 0) 
                    $piId =""; 
                else 
                    $piId =" and a.pi_wo_batch_no in ($pi_ids)";
                 	$sql = "select a.pi_wo_batch_no,(a.cons_quantity) as cons_quantity,a.cons_rate,a.order_rate,a.cons_amount, b.recv_number, b.receive_date
                        from inv_transaction a, inv_receive_master b
                        where a.mst_id = b.id and a.receive_basis=1 and a.transaction_type=1 
                        and a.status_active=1 $piId and b.company_id = $company_name";
                       //group by  a.pi_wo_batch_no,b.recv_number, b.receive_date"; //and a.item_category=1
                $result=sql_select($sql);
		foreach( $result as $row)
		{
				if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			$amount = $row[csf("cons_quantity")]*$row[csf("order_rate")];
			?>
            <tr bgcolor="<? echo $bgcolor ; ?>">
            	<td><? echo $i; ?></td>
                <td><? echo $piNoArr[$row[csf("pi_wo_batch_no")]]; ?></td>
                <td><? echo $row[csf("recv_number")]; ?></td>
                <td align="center"><? echo $row[csf("receive_date")]; ?></td>
                <td align="right"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
                <td align="right"><? echo number_format($row[csf("order_rate")],2); ?></td>
                <td align="right"><? echo number_format($amount,2); ?></td>
            </tr>
          </tbody>
		<?	
        $i++;
        } 
		?>
        </table>
            </div>
        </div>
	</fieldset>
    <br/>
    <fieldset style="width:100%; margin-left:10px" >
         <div id="report_container" align="center" style="width:100%" > 
             <div style="width:600px">
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead bgcolor="#dddddd">
                     	<tr>
                            <th width="30">SL</th>
                            <th width="80">PI NO.</th>
                            <th width="100">Receive Return No.</th>
                            <th width="130">Receive Return Date</th>
                            <th width="100">Qnty</th>
                            <th width="80">Rate</th>
                            <th width="">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                <?
		$i=1;
                $pi_ids = implode(",",array_filter(explode(",",chop($pi_ids,","))));
		if ($pi_ids=="" || $pi_ids == 0) 
                    $piId =""; 
                else 
                    $piId =" and b.pi_id in ($pi_ids)";
                $sql = "select b.pi_id,(cons_quantity) as cons_quantity,a.cons_rate,a.order_rate,a.cons_amount,b.issue_number, b.issue_date
                        from inv_transaction a, inv_issue_master b
                        where a.mst_id = b.id  
                        and b.status_active = 1 and b.pi_id <> 0 
                        and a.transaction_type = 3 $piId and b.company_id = $company_name
                        order by b.pi_id"; //group by b.pi_id ,b.issue_number, b.issue_date // and b.item_category = 1
                $result=sql_select($sql);
		foreach( $result as $row)
		{
				if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			$amount = $row[csf("order_rate")]*$row[csf("cons_quantity")];
			?>
            <tr bgcolor="<? echo $bgcolor ; ?>">
            	<td><? echo $i; ?></td>
                <td><? echo $piNoArr[$row[csf("pi_id")]]; ?></td>
                <td><? echo $row[csf("issue_number")]; ?></td>
                <td align="center"><? echo $row[csf("issue_date")]; ?></td>
                <td align="right"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
                <td align="right"><? echo number_format($row[csf("order_rate")],2); ?></td>
                <td align="right"><? echo number_format($amount,2); ?></td>
            </tr>
          </tbody>
		<?	
        $i++;
        } 
		?>
        </table>
            </div>
        </div>
	</fieldset>
</div>
<?
exit();
}
?>