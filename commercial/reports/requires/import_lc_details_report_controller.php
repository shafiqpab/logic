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
	$con=connect();
	//echo $con;die;
	$cbo_company=str_replace("'","",$cbo_company_id);
	$issue_banking_id=str_replace("'","",$cbo_issue_banking);
	$lc_sc_no=str_replace("'","",$txt_lc_sc);
	$hdn_lc_ids=str_replace("'","",$txt_lc_sc_id);
	$ex_rate=str_replace("'","",$txt_ex_rate);
	$ex_rate=str_replace("'","",$txt_ex_rate);
	if ($ex_rate!='') $ex_rate =$ex_rate; else $ex_rate=0;
	
	$cbo_lc_type=str_replace("'","",$cbo_lc_type_id);
	if ($cbo_lc_type==0) $type_id =""; else $type_id =" and a.lc_type_id=$cbo_lc_type ";
	
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);

	if ($cbo_company==0) $company_id =""; else $company_id =" and a.importer_id=$cbo_company ";
	if ($issue_banking_id==0) $issue_banking =""; else $issue_banking =" and a.issuing_bank_id=$issue_banking_id ";
	
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

	if($db_type==2)
	{
		if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and a.lc_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		
	}
	else if($db_type==0)
	{
		if( $from_date=='' && $to_date=='' ) $lc_date=""; else $lc_date= " and a.lc_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	}

	
	$companyArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name"); 
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$issueBankrArr = return_library_array("select id,bank_name from lib_bank ","id","bank_name"); 
	
	$cumulative_array=return_library_array("select pi_id, sum(current_acceptance_value) as amount from com_import_invoice_dtls where status_active=1 and is_deleted=0 group by pi_id",'pi_id','amount');
	$nagotiate_date_array=return_library_array("select btb_lc_id, nagotiate_date from com_import_invoice_mst where status_active=1 and is_deleted=0 and nagotiate_date is not null",'btb_lc_id','nagotiate_date');
	$payment_array=return_library_array("select lc_id, sum(accepted_ammount) as amount from com_import_payment where status_active=1 and is_deleted=0 group by lc_id",'lc_id','amount');
	$payment_com_array=return_library_array("select lc_id, sum(accepted_ammount) as amount from com_import_payment_com where status_active=1 and is_deleted=0 group by lc_id",'lc_id','amount');
	$all_data=array();
	
	// if($db_type == 0){
	// 	$list_aggregate_cond = " group_concat(c.item_category_id) as item_category";
	// }else if($db_type == 2 ){
	// 	$list_aggregate_cond = " listagg(c.item_category_id, ',' ) within group (order by c.item_category_id) as item_category";
	// }
	// if($cbo_lc_type==1)
	// {

		// $sql="Select a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.issuing_bank_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date,a.payterm_id, a.item_category_id, $list_aggregate_cond
		// from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c
		// where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active = 1 and b.status_active=1
		// and c.status_active =1 and a.lc_category not in ('01','02','23') and  a.is_deleted=0  $company_id  $issue_banking $lc_date $lc_no $type_id group by a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.issuing_bank_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date,a.payterm_id, a.item_category_id order by a.lc_category"; 
		// }
		// else if($cbo_lc_type==2)
		// {
		// 	$sql="Select a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.issuing_bank_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date,a.payterm_id, a.item_category_id, $list_aggregate_cond
		// 	from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
		// 	where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active = 1 and b.status_active=1
		// 	and c.status_active =1 and a.lc_category in ('01','02','23') and  a.is_deleted=0  $company_id  $issue_banking $lc_date $lc_no $type_id group by a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.issuing_bank_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date,a.payterm_id, a.item_category_id order by a.lc_category"; 
		// }
		// else
		// {
		// 	$sql="Select a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.issuing_bank_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date,a.payterm_id, a.item_category_id, $list_aggregate_cond
		// 	from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
		// 	where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active = 1 and b.status_active=1
		// 	and c.status_active =1 and a.is_deleted=0  $company_id  $issue_banking $lc_date $lc_no $type_id group by a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.issuing_bank_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date,a.payterm_id, a.item_category_id order by a.lc_category";
	// }

	if($cbo_lc_type==1)
	{

		$sql="Select a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.issuing_bank_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date,a.payterm_id, a.item_category_id, c.item_category_id as item_category
		from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c
		where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active = 1 and b.status_active=1
		and c.status_active =1 and a.lc_category not in ('01','02','23') and  a.is_deleted=0  $company_id  $issue_banking $lc_date $lc_no $type_id 
		order by a.lc_category"; 
	}
	else if($cbo_lc_type==2)
	{
		$sql="Select a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.issuing_bank_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date,a.payterm_id, a.item_category_id, c.item_category_id as item_category
		from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
		where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active = 1 and b.status_active=1
		and c.status_active =1 and a.lc_category in ('01','02','23') and  a.is_deleted=0  $company_id  $issue_banking $lc_date $lc_no $type_id 
		order by a.lc_category"; 
	}
	else
	{
		$sql="Select a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.issuing_bank_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date,a.payterm_id, a.item_category_id, c.item_category_id as item_category
		from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
		where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active = 1 and b.status_active=1
		and c.status_active =1 and a.is_deleted=0  $company_id  $issue_banking $lc_date $lc_no $type_id 
		order by a.lc_category";
	}
	
	 //echo $sql; //die;
	$result_lc_sql=sql_select($sql);
	// $temp_table_id=return_field_value("max(id) as id","gbl_temp_report_id","1=1","id");
	if($temp_table_id=="") $temp_table_id=1;   
	foreach($result_lc_sql as $row)
	{
		if($lc_id_check[$row[csf('id')]]=="")
		{
			$lc_id_check[$row[csf('id')]]=$row[csf('id')];
			/* $r_id=execute_query("insert into gbl_temp_report_id (id, ref_val, user_id) values ($temp_table_id,".$row[csf('id')].",$user_id)");
			if($r_id) $r_id=1; else {echo "insert into gbl_temp_report_id (id, ref_val, user_id) values ($temp_table_id,".$row[csf('id')].",$user_id)";oci_rollback($con);die;}
			$temp_table_id++; */ 
			if($row[csf("lc_date")]!="" && $row[csf("lc_date")]!="0000-00-00") $lcDate= change_date_format($row[csf("lc_date")]);
			if($row[csf("lc_expiry_date")]!="" && $row[csf("lc_expiry_date")]!="0000-00-00") $lcExpDate= change_date_format($row[csf("lc_expiry_date")]);

			$all_data[$row[csf('lc_category')]][$row[csf('issuing_bank_id')]][$row[csf('id')]]['id'] 				=$row[csf('id')];
			$all_data[$row[csf('lc_category')]][$row[csf('issuing_bank_id')]][$row[csf('id')]]['lc_number'] 		=$row[csf('lc_number')];
			$all_data[$row[csf('lc_category')]][$row[csf('issuing_bank_id')]][$row[csf('id')]]['importer_id'] 		=$row[csf('importer_id')];
			$all_data[$row[csf('lc_category')]][$row[csf('issuing_bank_id')]][$row[csf('id')]]['issuing_bank_id'] 	=$row[csf('issuing_bank_id')];
			$all_data[$row[csf('lc_category')]][$row[csf('issuing_bank_id')]][$row[csf('id')]]['supplier_id'] 		=$row[csf('supplier_id')];
			$all_data[$row[csf('lc_category')]][$row[csf('issuing_bank_id')]][$row[csf('id')]]['lc_date'] 			=$lcDate;
			$all_data[$row[csf('lc_category')]][$row[csf('issuing_bank_id')]][$row[csf('id')]]['lc_expiry_date'] 	=$lcExpDate;
			$all_data[$row[csf('lc_category')]][$row[csf('issuing_bank_id')]][$row[csf('id')]]['lc_value'] 			=$row[csf('lc_value')];
			$all_data[$row[csf('lc_category')]][$row[csf('issuing_bank_id')]][$row[csf('id')]]['payterm_id'] 		=$row[csf('payterm_id')];
			$all_data[$row[csf('lc_category')]][$row[csf('issuing_bank_id')]][$row[csf('id')]]['pi_id'] 			.=$row[csf('pi_id')].",";
			$all_data[$row[csf('lc_category')]][$row[csf('issuing_bank_id')]][$row[csf('id')]]['item_category_id'] 	=$row[csf('item_category_id')];			
			$all_data[$row[csf('lc_category')]][$row[csf('issuing_bank_id')]][$row[csf('id')]]['item_category_id'] 	=$row[csf('item_category_id')];			

		}
		$all_data[$row[csf('lc_category')]][$row[csf('issuing_bank_id')]][$row[csf('id')]]['item_categories'].=$row[csf('item_category')].",";
		
	}
	$btb_id_in=where_con_using_array($lc_id_check,0,'a.import_mst_id');
	$lc_sc_file_sql="SELECT a.lc_sc_id, a.import_mst_id as BTB_ID, b.internal_file_no as LC_SC_FILE_NO from com_btb_export_lc_attachment a, com_export_lc b where a.lc_sc_id=b.id and a.is_lc_sc=0 $btb_id_in and a.status_active = 1 and b.status_active=1 
	union all 
	SELECT a.lc_sc_id, a.import_mst_id as BTB_ID, b.internal_file_no as LC_SC_FILE_NO  from com_btb_export_lc_attachment a, com_sales_contract b where a.lc_sc_id=b.id and a.is_lc_sc=1 $btb_id_in and a.status_active = 1 and b.status_active=1 ";
	// echo $lc_sc_file_sql;
	$lc_sc_file_result=sql_select($lc_sc_file_sql);
	$lc_sc_file_data=array();
	foreach($lc_sc_file_result as $row)
	{
		$lc_sc_file_data[$row['BTB_ID']].=$row['LC_SC_FILE_NO'].', ';
	}
	
	/* if($r_id)
	{
		oci_commit($con);
	} */
	
	/*$sql_export_lc="select b.IMPORT_MST_ID, c.INTERNAL_FILE_NO from GBL_TEMP_REPORT_ID a, COM_BTB_EXPORT_LC_ATTACHMENT b, COM_EXPORT_LC c where a.ref_val=b.IMPORT_MST_ID and b.LC_SC_ID=c.ID and b.IS_LC_SC=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0
	union all 
	select b.IMPORT_MST_ID, c.INTERNAL_FILE_NO from GBL_TEMP_REPORT_ID a, COM_BTB_EXPORT_LC_ATTACHMENT b, COM_SALES_CONTRACT c where a.ref_val=b.IMPORT_MST_ID and b.LC_SC_ID=c.ID and b.IS_LC_SC=1 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0";
	$sql_export_lc_result=sql_select($sql_export_lc);
	$file_data=array();
	foreach($sql_export_lc_result as $row)
	{
		$file_data[$row["IMPORT_MST_ID"]]=$row["INTERNAL_FILE_NO"];
	}*/
	/* $r_id2=execute_query("delete from gbl_temp_report_id where user_id=$user_id");
	if($r_id2)
	{
		oci_commit($con);
	} */
	
	//echo $sql_export_lc;die;
	
	ob_start();
	?>
	<div style="width:1390px; margin: 0 auto;" id="" >
		<table width="1350px"  align="left">
			<?
			$company_library=sql_select("select id, company_short_name, plot_no, level_no,road_no,city from lib_company where id=".$cbo_company_id."");        
			?>
			<tr>
				<td colspan="11" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?></u></strong></center></td>
			</tr>
			<?
			if( $from_date!='' && $to_date!='' )
			{
				?>
				<tr>
					<td colspan="11" align="center" style="font-size:18px"><center><strong><u><? echo "From ".change_date_format($from_date)." To ".change_date_format($to_date); ?></u></strong></center></td>
				</tr>
				<?
			}
			?>       
		</table>
		<table cellspacing="0" cellpadding="0" width="1350"  rules="all" class="rpt_table" border="1" align="left" >
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="120">BTB No</th>
					<th width="120">Item Category</th>
					<th width="60">Applicant</th>
					<th width="140">Bank</th>
					<th width="120">Beneficiary</th>
					<th width="80">LC Date</th>
					<th width="80">LC Expiry Date</th>
					<th width="100">LC Amount (USD)</th>
					<th width="100">Acc. Amount (USD)</th>
					<th width="100">Paid Amount (USD)</th>
					<th width="100">Due Amount (USD)</th>
                    <th width="100">File No</th>
                    <th>File</th>
				</tr>
			</thead>
		</table>
		<div style="width:1370px; overflow-y:scroll; max-height:400px; overflow-x:hidden; float: left;" id="scroll_body" >
			<table cellspacing="0" cellpadding="0" width="1350"  rules="all" class="rpt_table" border="1" id="" style="margin-left:0px;" align="left">
				<tbody>
					<?
					$i=1; 
					$grand_lc_value=''; $grand_cumulative_value=''; $grand_paidValue=''; $grand_deu_value='';
				 	foreach($all_data as $lcCatId => $lc_data_arr)
					{
						?>
						<tr bgcolor="#b0d6df">
							<td colspan="14"><strong><? echo $supply_source[abs($lcCatId)]; ?>&nbsp;</strong></td>
						</tr>
						<?
						$total_lc_value=''; $total_cumulative_value=''; $total_paidValue=''; $total_deu_value='';
						foreach ($lc_data_arr as $bankId => $data_arr)
						{
							foreach ($data_arr as $lcId => $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="120" style="word-break: break-all;"><a href="##" onClick="lc_details_popup(<?= $lcId;?>,'lc_popup_details','LC Details')"><? echo $row['lc_number']; ?></a>&nbsp;</td>
									<?
										if($item_category_id !=0){
									?>
									<td width="120" style="word-break: break-all;"><? echo $item_category[$row['item_category_id']]; ?>&nbsp;</td>
									<?
										}else{
											$item_categories="";
											$item_category_arr=array_unique(explode(",",chop($row['item_categories'],",")));
											//echo "<pre>";
											//print_r($lc_sc_id_arr);
											foreach($item_category_arr as $item_cat)
											{
												if($item_cat==0){
													$item_categories .= $item_category[$item_cat];
												}else{
													$item_categories .= $item_category[$item_cat].",";
												}
											} 	
									?>
									<td width="120" style="word-break: break-all;"><? echo chop($item_categories,","); ?>&nbsp;</td>
									<?
										}
									?>
									<td width="60"><? echo $companyArr[$row['importer_id']]; ?>&nbsp;</td>
									<td width="140"><? echo $issueBankrArr[$row['issuing_bank_id']]; ?>&nbsp;</td>
									<td width="120"><? echo $supplierArr[$row['supplier_id']]; ?>&nbsp;</td>
									<td width="80" align="center"><? echo $row['lc_date']; ?>&nbsp;</td>
									<td width="80" align="center"><? echo $row['lc_expiry_date']; ?>&nbsp;</td>
									<td width="100" align="right"><? echo number_format($row['lc_value'],2); $total_lc_value +=$row['lc_value'];?></td>
									<td width="100" align="right"><? 
									$cumulative_value="";
									$pi_id_arr=array_unique(explode(",",chop($row['pi_id'],",")));
									$pi_id_all=implode(",",$pi_id_arr);
									//echo "<pre>";
									//print_r($lc_sc_id_arr);
									foreach($pi_id_arr as $pi_id)
									{
										$cumulative_value += $cumulative_array[$pi_id];
									} 
									echo number_format($cumulative_value,2); 
									$total_cumulative_value+=$cumulative_value ; ?>
									</td>
									<td width="100" align="right" title="<?= $row['payterm_id'];?>"><?
									$is_nego_date_available='';  $paidValue='';
									if($row['payterm_id']==3 || $row['payterm_id']==4 )
									{
										$paidValue=$row['lc_value'];
									}
									else if($row['payterm_id']==2)
									{
										$paidValue=$payment_array[$row['id']];
									}
									else
									{
										$paidValue=$payment_com_array[$row['id']];
									} 
									echo number_format($paidValue,2) ;
									$total_paidValue+=$paidValue;?>
									</td>
									<td width="100" align="right"><? 
									if($row['payterm_id']==3 || $row['payterm_id']==4 )
									{
										$deu_value= $row['lc_value']-$paidValue ;
									}
									else
									{
										$deu_value= $cumulative_value-$paidValue ;
									}
									
									echo number_format($deu_value,2);
									$total_deu_value+=$deu_value; ?>
									</td>
                                    <td width="100"><p><? echo rtrim($lc_sc_file_data[$row['id']],', ');?></p></td>
                                    <td align="center"><p>
									<input type="button" class="image_uploader" id="fileno_<? echo $i;?>" style="width:40px" value="File" onClick="openmypage_file(<? echo $i; ?>,'<? echo $pi_id_all;?>')"/>
                                    </p></td>
								</tr>
								<?
								$i++;
							}
						}
						$grand_lc_value+=$total_lc_value;
						$grand_cumulative_value+=$total_cumulative_value;
						$grand_paidValue+=$total_paidValue;
						$grand_deu_value+=$total_deu_value;
						?>
						<tr bgcolor="#CCFFCC">
							<td colspan="8" align="right"> <strong> Currency Wise Product Total </strong></td>
							<td align="right"> <strong><u> <? echo number_format($total_lc_value,2) ;?></u> </strong></td>
							<td align="right"> <strong><u> <? echo number_format($total_cumulative_value,2) ;?></u> </strong></td>
							<td align="right"> <strong><u> <? echo number_format($total_paidValue,2) ;?></u> </strong></td>
							<td align="right"> <strong><u> <? echo number_format($total_deu_value,2) ;?></u> </strong></td>
                            <th colspan="2">&nbsp;</th>
						</tr>
						<?
					}
					?>
				</tbody>
			</table>
		</div>
		<table cellspacing="0" width="1350"  border="1" rules="all" class="rpt_table" align="left">
			<tfoot>
				<tr>
					<th width="755" align="right"> <strong> GRAND TOTAL </strong></th>
					<th width="100" align="right"><u> <? echo number_format($grand_lc_value,2) ;?></u></th>
					<th width="100" align="right"><u> <? echo number_format($grand_cumulative_value,2) ;?></u></th>
					<th width="100" align="right"><u> <? echo number_format($grand_paidValue,2) ;?></u></th>
					<th width="100" align="right"><u> <? echo number_format($grand_deu_value,2) ;?></u></th>
                   	<th>&nbsp;</th> 
				</tr>
				<tr>
					<th align="right"> <strong> BDT( <? echo $ex_rate ;?> ) </strong></th>
					<th align="right"><u> <? echo number_format($grand_lc_value*$ex_rate,2) ;?></u></th>
					<th align="right"><u> <? echo number_format($grand_cumulative_value*$ex_rate,2) ;?></u></th>
					<th align="right"><u> <? echo number_format($grand_paidValue*$ex_rate,2) ;?></u></th>
					<th align="right"><u> <? echo number_format($grand_deu_value*$ex_rate,2) ;?></u></th>
                    <th>&nbsp;</th>
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
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";	
	exit();	

}


if($action=="report_generate_summary")
{
	extract($_REQUEST);
	$con=connect();
	//echo $con;die;
	$cbo_company=str_replace("'","",$cbo_company_id);
	$issue_banking_id=str_replace("'","",$cbo_issue_banking);
	$lc_sc_no=str_replace("'","",$txt_lc_sc);
	$hdn_lc_ids=str_replace("'","",$txt_lc_sc_id);
	$ex_rate=str_replace("'","",$txt_ex_rate);
	$ex_rate=str_replace("'","",$txt_ex_rate);
	if ($ex_rate!='') $ex_rate =$ex_rate; else $ex_rate=0;
	
	$cbo_lc_type=str_replace("'","",$cbo_lc_type_id);
	if ($cbo_lc_type==0) $type_id =""; else $type_id =" and a.LC_TYPE_ID=$cbo_lc_type ";
	
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);

	if ($cbo_company==0) $company_id =""; else $company_id =" and a.IMPORTER_ID=$cbo_company ";
	//if ($issue_banking_id==0) $issue_banking =""; else $issue_banking =" and a.issuing_bank_id=$issue_banking_id ";
	if($db_type==2)
	{
		if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and a.LC_DATE between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		
	}
	else if($db_type==0)
	{
		if( $from_date=='' && $to_date=='' ) $lc_date=""; else $lc_date= " and a.LC_DATE between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	}
	
	ob_start();
	$all_data=array();
	// $inv_sql="SELECT A.CURRENCY_ID, A.ID, A.LC_YEAR, A.IMPORTER_ID, A.LC_DATE, A.LC_VALUE, B.CURRENT_ACCEPTANCE_VALUE
	// 	from com_btb_lc_master_details a, COM_IMPORT_INVOICE_DTLS b
	// 	where A.ID=b.BTB_LC_ID and a.STATUS_ACTIVE = 1 and b.STATUS_ACTIVE = 1 $company_id  $lc_date group by A.ID, A.LC_YEAR, A.IMPORTER_ID, A.LC_DATE, A.LC_VALUE, A.CURRENCY_ID,B.CURRENT_ACCEPTANCE_VALUE";
	$inv_sql="SELECT A.CURRENCY_ID, A.ID, A.LC_YEAR, A.IMPORTER_ID, A.LC_DATE, A.LC_VALUE, B.CURRENT_ACCEPTANCE_VALUE,b.ID  as INV_DTLS_ID, C.ID AS INV_ID, C.MATURITY_DATE
	from com_btb_lc_master_details a, COM_IMPORT_INVOICE_DTLS b, com_import_invoice_mst c 
	where a.ID=b.BTB_LC_ID and b.IMPORT_INVOICE_ID=c.ID and a.STATUS_ACTIVE = 1
	and b.STATUS_ACTIVE = 1 and c.STATUS_ACTIVE = 1 $company_id  $lc_date
	order by C.MATURITY_DATE";
	//	echo $inv_sql;
	$result_lc_inv_sql=sql_select($inv_sql);
	$inv_data=array();
	foreach($result_lc_inv_sql as $row)
	{
		$inv_data[$row["INV_ID"]]["IMPORTER_ID"]=$row["IMPORTER_ID"];
		$inv_data[$row["INV_ID"]]["MATURITY_DATE"]=$row["MATURITY_DATE"];
		if($dtls_id_check[$row["INV_DTLS_ID"]]=="")
		{
			$dtls_id_check[$row["INV_DTLS_ID"]]=$row["INV_DTLS_ID"];
			$data_arr_inv[$row["ID"]]["CURRENT_ACCEPTANCE_VALUE"]+=$row["CURRENT_ACCEPTANCE_VALUE"];
			$inv_data[$row["INV_ID"]]["ACCEP_VALUE"]+=$row["CURRENT_ACCEPTANCE_VALUE"];
		}
	}
	unset($result_lc_inv_sql);	
	
	$payment_com_array=return_library_array("select invoice_id, sum(accepted_ammount) as amount from com_import_payment_com where status_active=1 and is_deleted=0 group by invoice_id",'invoice_id','amount');

	$accptd_sql="SELECT A.ID, A.LC_YEAR, A.IMPORTER_ID, A.LC_DATE, A.LC_VALUE,A.PAYTERM_ID, B.LC_ID , B.ACCEPTED_AMMOUNT, b.INVOICE_ID as INV_ID
	from com_btb_lc_master_details A, com_import_payment B 
	where A.ID=B.LC_ID and A.STATUS_ACTIVE = 1 and B.STATUS_ACTIVE = 1 $company_id  $lc_date";
	//echo $accptd_sql;
	$result_accptd_sql=sql_select($accptd_sql);
	$payment_data=array();
	foreach($result_accptd_sql as $row)
	{
		// $data_arr_accptd[$row["ID"]]["ACCEPTED_AMMOUNT"]+=$row["ACCEPTED_AMMOUNT"];
		// $payment_data[$row["INV_ID"]]+=$row["ACCEPTED_AMMOUNT"];
		
		if($row['PAYTERM_ID']==3 || $row['PAYTERM_ID']==4 )
		{
			$data_arr_accptd[$row["ID"]]["ACCEPTED_AMMOUNT"]+=$row['LC_VALUE'];
			$payment_data[$row["INV_ID"]]+=$row["LC_VALUE"];
		}
		else if($row['PAYTERM_ID']==2)
		{
			$data_arr_accptd[$row["ID"]]["ACCEPTED_AMMOUNT"]+=$row["ACCEPTED_AMMOUNT"];
			$payment_data[$row["INV_ID"]]+=$row["ACCEPTED_AMMOUNT"];
		}
		else
		{
			$data_arr_accptd[$row["ID"]]["ACCEPTED_AMMOUNT"]+=$payment_com_array[$row['INV_ID']];
			$payment_data[$row["INV_ID"]]+=$payment_com_array[$row['INV_ID']];
		} 
	}
	unset($result_accptd_sql);
	$sql="SELECT A.CURRENCY_ID, A.ID, A.LC_YEAR, A.IMPORTER_ID, A.LC_DATE, A.LC_VALUE
	from com_btb_lc_master_details A
	where  A.STATUS_ACTIVE = 1  $company_id  $lc_date";
	//echo $sql; //die;
	$result_lc_sql=sql_select($sql);
	foreach($result_lc_sql as $row)
	{
		$data_arr[$row["IMPORTER_ID"]]["LC_VALUE"]+=$row["LC_VALUE"];
		$data_arr[$row["IMPORTER_ID"]]["BANK_ACCEP_VALUE"]+=$data_arr_inv[$row["ID"]]["CURRENT_ACCEPTANCE_VALUE"];
		$data_arr[$row["IMPORTER_ID"]]["PAID_VALUE"]+=$data_arr_accptd[$row["ID"]]["ACCEPTED_AMMOUNT"];
		$acceptance_pending_value=$row["LC_VALUE"]-$data_arr_inv[$row["ID"]]["CURRENT_ACCEPTANCE_VALUE"];
		$data_arr[$row["IMPORTER_ID"]]["ACCEPTANCE_PANDING_VALUE"]+=$acceptance_pending_value;
		$payment_pending_value=$data_arr_inv[$row["ID"]]["CURRENT_ACCEPTANCE_VALUE"]-$data_arr_accptd[$row["ID"]]["ACCEPTED_AMMOUNT"];
		$data_arr[$row["IMPORTER_ID"]]["PAYMENT_PANDING_VALUE"]+=$payment_pending_value;
	}
	unset($result_lc_sql);
	//echo "<pre>";print_r($data_arr);die;

	$month_wise_pending_data=array();
	foreach($inv_data as $inv_id=>$inv_val)
	{
		$pending_value=$inv_val["ACCEP_VALUE"]-$payment_data[$inv_id];
		$month_wise_pending_data[$inv_val["IMPORTER_ID"]][date("Y-F",strtotime($inv_val["MATURITY_DATE"]))]+=$pending_value;
		
	}
	//echo "<pre>";print_r($month_wise_pending_data);die;
    ?>
	<div style="width:1250px; margin: 0 auto;" id="" >
		<table width="1200px"  align="left">
			<?
			if($cbo_company > 0){
				$cond_comapny = " where id=".$cbo_company_id."";
			}
			$company_librarys=sql_select("SELECT ID, COMPANY_NAME , COMPANY_SHORT_NAME, PLOT_NO, LEVEL_NO,ROAD_NO,CITY from lib_company $cond_comapny order by COMPANY_NAME");        
			?>
			<tr>
				<td colspan="11" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?></u></strong></center></td>
			</tr>
			<?
			if( $from_date!='' && $to_date!='' )
			{
				?>
				<tr>
					<td colspan="11" align="center" style="font-size:18px"><center><strong><u><? echo "From ".change_date_format($from_date)." To ".change_date_format($to_date); ?></u></strong></center></td>
				</tr>
				<?
			}
			?>       
		</table>
		<?
		//echo "<pre>";print_r($data_arr);die;
		foreach($company_librarys as $com_data)
		{
			//echo $com_data["ID"].testee;
		   	?>
			<table width="1200px"  align="left">
				<tr >
					<td colspan="11" align="center" >
						<strong style="font-size:20px"><? echo $com_data['COMPANY_NAME'];
						?></strong>
					</td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" width="1200px"  rules="all" class="rpt_table" border="1" align="left">
				<thead>
					<tr>
						<th width="170">Total LC Open Value</th>
						<th width="170">Bank Acceptance Value</th>
						<th width="170">Acceptance Pending</th>
						<th width="170">Paid Value</th>
						<th width="170">Payment Pending / Acc. Liability</th>
						<th>Acceptance Pending + Acc. Liability</th>
					</tr>
				</thead>
			</table>
			<div style="width:1220px; max-height:400px; overflow-x:hidden; float: left;" id="scroll_body" >
				<table cellspacing="0" cellpadding="0" width="1200px"  rules="all" class="rpt_table" border="1" id=""  align="left">
					<tbody>
					<tr bgcolor="#CCFFCC">
							<td width="170" align="right"><? echo number_format($data_arr[$com_data["ID"]]["LC_VALUE"],2);?>&nbsp;</td>
							<td width="170" align="right"><? echo number_format($data_arr[$com_data["ID"]]["BANK_ACCEP_VALUE"],2);?>&nbsp;</td>
							<td width="170" align="right"><? echo number_format($data_arr[$com_data["ID"]]["ACCEPTANCE_PANDING_VALUE"],2); ?>&nbsp;</td>
							<td width="170" align="right"><? echo number_format($data_arr[$com_data["ID"]]["PAID_VALUE"],2);?>&nbsp;</td>
							<td width="170" align="right"><? echo number_format($data_arr[$com_data["ID"]]["PAYMENT_PANDING_VALUE"],2); ?>&nbsp;</td>
							<td align="right"><? $total_pending = $data_arr[$com_data["ID"]]["ACCEPTANCE_PANDING_VALUE"] + $data_arr[$com_data["ID"]]["PAYMENT_PANDING_VALUE"]; 
							echo number_format($total_pending,2); ?>&nbsp;</td>
						</tr>
						
					</tbody>
				</table>
			</div>

			<div>
			<table>
					<tr><td><br></td></tr>
					<tr>
						<th colspan="11" align="center" >
							<strong style="font-size:16px">As of Today Payment Pending / Acc. Liability (As Per Maturity Date)</strong>
						</th>
					</tr>
			</table>

			<table cellspacing="0" cellpadding="0" width="750px"  rules="all" class="rpt_table" border="1" align="center">
				<thead>
					<tr>
						<th width="250">Year</th>
						<th width="250">Month</th>
						<th width="250">Amount</th>
					</tr>
				</thead>
				<tbody>
					<?
						$i=1;
						$total_month_wise_pending =''; 
						foreach($month_wise_pending_data[$com_data["ID"]] AS $month_wise_pending_key => $month_wise_pending_val)
						{
					 		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					        ?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="250" align="center"><?
								$explode_month_wise = explode('-',$month_wise_pending_key); 
								$year = $explode_month_wise[0];
								if($explode_month_wise[0]!='1970'){
									echo $year;
								}
								?></td>
								<td width="250" align="center"><?
								$explode_month_wise = explode('-',$month_wise_pending_key); 
								$month = $explode_month_wise[1];
								if($explode_month_wise[0]=='1970'){
									$month='<b>Pending liability not found</b>';
								}
								echo $month;?></td>
								<td width="250" align="right"><? echo number_format($month_wise_pending_val,2);
								$total_month_wise_pending+=$month_wise_pending_val;
								?></td>
							</tr>
						<?
						$i++;
						}
						?>
						<tr>
							<td></td>
							<td align="center"><strong>Current Liability</strong></td>
							<td align="right"><strong><? echo number_format($total_month_wise_pending,2);?></strong></td>
						</tr>
				</tbody>
			</table>
			</div>

        <?
		echo "<br><br>";
	    }
		?>
	</div>
  <?
}


if($action=="show_file")
{
	//echo load_html_head_contents("Development File","../../../", 1, 1, $unicode);
	echo load_html_head_contents("File", "../../../", 1, 1,'','','');
    extract($_REQUEST);
	$pi_id_arr=explode(",",$pi_id);
	$pi_id_all="";
	foreach($pi_id_arr as $ids)
	{
		$pi_id_all.="'".$ids."',";
	}
	$pi_id_all=chop($pi_id_all,",");
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id in($pi_id_all) and form_name='proforma_invoice' and is_deleted=0 and file_type=2");
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        { 
        ?>
        <td><a href="../../../<? echo $row[csf('image_location')]; ?>" target="_new"> 
        <img src="../../../file_upload/<? echo $row[csf('image_location')]; ?>" width="80" height="60"> </a>
        </td>
        <?
        }
        ?>
        </tr>
    </table>
    <?
}

if($action == "lc_popup_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $lc_id;die;
	?>	
    <table class="rpt_table" border="1" rules="all" width="500" cellpadding="0" cellspacing="0">
        <thead bgcolor="#dddddd">
            <tr>
                <th width="50">SL</th>
                <th width="250">PI NO</th>
                <th>SC/LC No</th>
            </tr>
        </thead>
        <tbody>
        	<?
			$export_sql="select b.IMPORT_MST_ID, c.EXPORT_LC_NO as LC_SC_NO from COM_BTB_EXPORT_LC_ATTACHMENT b, COM_EXPORT_LC c where b.LC_SC_ID=c.ID and b.IS_LC_SC=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.IMPORT_MST_ID=$lc_id
			union all 
			select b.IMPORT_MST_ID, c.CONTRACT_NO as LC_SC_NO from COM_BTB_EXPORT_LC_ATTACHMENT b, COM_SALES_CONTRACT c where b.LC_SC_ID=c.ID and b.IS_LC_SC=1 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.IMPORT_MST_ID=$lc_id";
			$export_result=sql_select($export_sql);
			$export_data=array();
			foreach($export_result as $row)
			{
				$export_data[$row["IMPORT_MST_ID"]]=$row["LC_SC_NO"];
			}
            $sql = "select a.COM_BTB_LC_MASTER_DETAILS_ID as IMPORT_MST_ID, b.PI_NUMBER
            from COM_BTB_LC_PI a, COM_PI_MASTER_DETAILS b
            where a.PI_ID = b.ID and a.COM_BTB_LC_MASTER_DETAILS_ID=$lc_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0";
			//echo $sql;
            //group by  a.pi_wo_batch_no,b.recv_number, b.receive_date"; //and a.item_category=1
            $result=sql_select($sql);
            $i=1;
			foreach( $result as $row)
			{
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				$amount = $row[csf("cons_quantity")]*$row[csf("order_rate")];
				?>
				<tr bgcolor="<? echo $bgcolor ; ?>">
					<td align="center"><? echo $i; ?></td>
					<td><? echo $row["PI_NUMBER"]; ?></td>
					<td><? echo $export_data[$row["IMPORT_MST_ID"]]; ?></td>
				</tr>
			<?	
			$i++;
			} 
			?>
        </tbody>
	</table>
	<?
	exit();
}
?>