<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($db_type==2 || $db_type==1 )
{
	$mrr_date_check="and to_char(insert_date,'YYYY')=".date('Y',time())."";
	$concat="";
	$concat_coma="||";
	$group_concat="wm_concat";
}
else if ($db_type==0)
{
	$mrr_date_check="and year(insert_date)=".date('Y',time())."";
	$concat="concat";
	$concat_coma=",";
	$group_concat="group_concat";

}

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,company_location_id,store_location_id,item_cate_id,supplier_id FROM user_passwd where id='$user_id'");
$company_id = $userCredential[0][csf('company_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}

if ($company_location_id !='') {
    $company_location_credential_cond = "and lib_location.id in($company_location_id)"; 
}

if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}
if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;  
}
else
{
	 $item_cate_credential_cond="".implode(",",array_flip(106))."";
}
if ($supplier_id !='') {
    $supplier_credential_cond = "and a.id in($supplier_id)";
}

//========== user credential end ==========


//--------------------------------------------------------------------------------------------

/*
//load drop down supplier
if ($action=="load_drop_down_supplier")
{	  
	echo create_drop_down( "cbo_return_to", 170, "select id,supplier_name from lib_supplier where FIND_IN_SET($data,tag_company) and FIND_IN_SET(2,party_type) order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );  	 
	exit();
}
*/


//load drop down company location
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $company_location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );  
	//load_room_rack_self_bin('requires/emb_material_issue_return_controller*106', 'store','store_td', $('#cbo_company_id').val(), this.value);   	 
	exit();
}
if ($action=="load_room_rack_self_bin")
	{
		load_room_rack_self_bin("requires/emb_material_issue_return_controller",$data);
	}

if ($action=="load_drop_down_store")
{
	//echo "select a.id,a.store_name from lib_store_location a, lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data $store_location_credential_cond and b.category_type in (4,8,9,10,11,15,16,17,18,19,20,21,22,32,34,35,36,37,38,39) group by a.id,a.store_name order by a.store_name";die;
	echo create_drop_down( "cbo_store_name", 162, "select a.id,a.store_name from lib_store_location a, lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data $store_location_credential_cond and b.category_type in (106) group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );  	 
	exit();
}

if($action=="serial_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	 
 	$serialStringID = str_replace("'","",trim($serialStringID));
	$txt_received_id = str_replace("'","",$txt_received_id);
	$current_prod_id = str_replace("'","",$current_prod_id);
	//echo $serialStringID;die;
	
 	?>
	<script>
	var selected_id = new Array();
	var selected_no = new Array();	
	
	 
	var serialNoArr="<? echo $serialStringID; ?>";
 	var chk_selected_no = new Array();
	var chk_selected_id = new Array();
	if(serialNoArr!=""){chk_selected_no=serialNoArr.split(",");}
	
	 
	
	function check_all_data() 
	{
		var tbl_row_count = document.getElementById( 'hidden_all_id' ).value.split(","); 
 		//tbl_row_count = tbl_row_count-1;
		for( var i = 0; i < tbl_row_count.length; i++ ) {
 			if( jQuery.inArray( $('#txt_serial_id' + tbl_row_count[i]).val(), chk_selected_id ) != -1 )
			js_set_value( tbl_row_count[i] );
		}
	}
	
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				//x.style.backgroundColor = ( $serialStringID != "")? newColor : origColor;
			}
		} 
		
	function js_set_value( str ) { //alert(str);
		toggle( document.getElementById('search'+str), '#FFFFCC' );
		
		if( jQuery.inArray( $('#txt_serial_id' + str).val(), selected_id ) == -1 ) {
			selected_id.push( $('#txt_serial_id' + str).val() );
			selected_no.push( $('#txt_serial_no' + str).val() );
 		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == $('#txt_serial_id' + str).val() ) break;
			}
			selected_id.splice( i, 1 );
			selected_no.splice( i, 1 );
		}
		var id = '';	var no = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			no += selected_no[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		no = no.substr( 0, no.length - 1 );
  		$('#txt_string_id').val( id );
		$('#txt_string_no').val( no );
	}
	 
	function fn_onClosed()
	{
		var txt_string = $('#txt_string').val();
		if(txt_string==""){ alert("Please Select The Serial"); return;}
		parent.emailwindow.hide();
	}
	 
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
    	<table width="300" cellspacing="0" cellpadding="0" border="0" class="rpt_table" id="tbl_header" >
				<thead>
					<tr>                	 
						<th width="300">Serial No</th>
 					</tr>
				</thead>
        </table>        
        <div style="width:300px; min-height:220px">
		<table width="300" cellspacing="0" cellpadding="0" border="0" class="rpt_table" id="tbl_serial" style="overflow:scroll; min-height:200px" >
 				<tbody>
                	<?
						$i=1;
						$sql="";
						if(trim($serialStringID)=="")
						{
							$sql ="select id,serial_no from inv_serial_no_details where prod_id=$current_prod_id and is_issued=1";
						}
						else
						{
							
							$sql ="select id,serial_no from (select id,serial_no from inv_serial_no_details where prod_id=$current_prod_id and is_issued=1 union select id,serial_no from inv_serial_no_details where id in($serialStringID))  table1";
						}
						//echo $sql;
						//$sql .="select id,serial_no from inv_serial_no_details where prod_id=$current_prod_id and is_issued=1";
						//echo $sql;die;
						
						
						
						$result = sql_select($sql);
						$count=count($result );
						foreach($result as $row) 
						{
							if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($new_data=="") $new_data=$row[csf("id")]; else $new_data .=",".$row[csf("id")];				
						?>	
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf("id")];?>)" id="search<? echo $row[csf("id")]; ?>" style="cursor:pointer">
								<td  width="300">
									<? echo trim($row[csf("serial_no")]); ?> 
									<input type="hidden" id="txt_serial_id<? echo $row[csf("id")]; ?>" value="<? echo $row[csf("id")]; ?>" >
                                    <input type="hidden" id="txt_serial_no<? echo $row[csf("id")]; ?>" value="<? echo $row[csf("serial_no")]; ?>" >
								</td>
									<?  
									
									if($count==$i)
									{
									?> 
                                    <input type="hidden" id="hidden_all_id" value="<? echo $new_data; ?>" >
                                    <? } ?>
							</tr> 
					<? 
						
							$i++;
						}

				?>
				</tbody>         
			</table>  
            </div>
            <div><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" /></div>  
            <!-- Hidden field here-------->
			<input type="hidden" id="txt_string_id" value="" />
            <input type="hidden" id="txt_string_no" value="" />				 
			<!-- ---------END-------------> 
			</form>
	   </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
        <script>
		if(serialNoArr!="")
		{
			serialNoArr=serialNoArr.split(",");
			for(var k=0;k<serialNoArr.length; k++)
			{
				js_set_value(serialNoArr[k] );
			}
		}
	</script>
	<?
	
}


if($action=="itemdesc_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(mrr)
	{
 		$("#hidden_recv_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th width="220">Item Category</th>
                    <th width="220">Item Group</th>
                    <th width="300">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>                    
                    <td align="center">
                        <?  
                           // $search_by = array(1=>'Return Number');
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index )
							
							echo create_drop_down( "cbo_item_category", 180, $item_category,"", 1, "-- Select --",106, "", 1,"$item_cate_credential_cond","","","" );
                        ?>
                    </td>
                    <td width="" align="center">
                    	<?  
                            //$search_by = array(1=>'Return Number');
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_item_group", 180, "select id,item_name from lib_item_group where status_active=1 and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", 0, "", 0,"" );
                        ?>	
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
                    </td>
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_item_category').value+'_'+document.getElementById('cbo_item_group').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_item_search_list_view', 'search_div', 'emb_material_issue_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-------->
                     <input type="hidden" id="hidden_recv_number" value="" />
                    <!-- ---------END------------->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" valign="top" id="search_div" style="margin-top:10px;"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}



if($action=="create_item_search_list_view")
{
	$ex_data = explode("_",$data);
	$item_category = $ex_data[0];
	$item_group = $ex_data[1];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$company = $ex_data[4];
	
	$sql_cond="";
	
	if( $item_category!=0 )  $item_category=" and b.item_category='$item_category'"; else $item_category="";
	if( $item_group!=0 )  $item_group=" and c.item_group_id='$item_group'"; else $item_group="";
	
	if( $txt_date_from!="" || $txt_date_to!="" ) $sql_cond .= " and a.issue_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
		
	$sql ="select sum(case when b.transaction_type=2 then (b.cons_quantity+b.cons_reject_qnty) else 0 end) as to_isue,c.id as prod_id,b.item_category,c.product_name_details,c.lot,c.current_stock,c.item_description,c.item_group_id,c.sub_group_name,c.item_size,c.avg_rate_per_unit ,c.unit_of_measure,a.issue_number_prefix_num
		from inv_issue_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id and c.status_active=1
		where a.id=b.mst_id and a.status_active=1 and c.status_active=1 and b.item_category in(106) and b.transaction_type=2 and a.entry_form=367 $sql_cond $item_category $item_group 
		group by c.id,c.product_name_details,c.lot,c.current_stock,c.item_description,c.item_group_id,c.sub_group_name,c.item_size,c.avg_rate_per_unit,c.unit_of_measure,b.item_category,a.issue_number_prefix_num";
	
	//echo $sql; die;	
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$arr=array(2=>$item_group_arr);
 	echo create_list_view("list_view", "Product Id, Issue Id, Item Group, Item SubGroup, Item Description, Item Size, Issue Challan No","60,60,150,150,230,100,80","900","260",0, $sql , "js_set_value", "item_description,prod_id,unit_of_measure,to_isue,item_category,item_group_id,avg_rate_per_unit", "", 1, "0,0,item_group_id,0,0,0,0", $arr, "prod_id,issue_number_prefix_num,item_group_id,sub_group_name,item_description,item_size,challan_no", "","",'0,0,0,0,0') ;	
	exit();
	
}


if($action=="issue_rtn_qty")
{
	//echo $data;
	echo return_field_value("sum(case when transaction_type=4 then (cons_quantity+cons_reject_qnty) else 0 end) as to_isue_return","inv_transaction","prod_id=$data and item_category in(".implode(",",array_flip(106)).") and transaction_type=4","to_isue_return");
	
}

if($action=="populate_data_from_data")
{
	$ex_data = explode("_",$data);
	$avarage_rate_per_unit = $ex_data[0];
	$prodID = $ex_data[1];
	$total_issue=$ex_data[2];
	//$total_issue_return=$ex_data[3];
	//$current_total=$total_issue-$total_issue_return;
	//echo $current_total; die;
	//echo ($ex_data[2]);
	
	
			
	$sql = "select sum(case when b.transaction_type=4 then (b.cons_quantity+b.cons_reject_qnty) else 0 end) as to_isue_return, c.id as prod_id,concat(c.sub_group_name,',',c.item_description,',',c.item_size) as pro_description,c.unit_of_measure,c.item_group_id,b.item_category
			from inv_receive_master a, inv_transaction b, product_details_master c
			where a.id=b.mst_id and b.prod_id=c.id and c.id=$prodID and b.item_category in(".implode(",",array_flip(106)).") and b.transaction_type in(2,4) group by c.id";		
			
	//echo $sql;
	$res = sql_select($sql);
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	foreach($res as $row)
	{
		echo "$('#txt_item_description').val('".$row[csf("pro_description")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		//echo "$('#txt_supplier_id').val('".$row[csf("supplier_id")]."');\n";
		//echo "$('#txt_yarn_lot').val('".$row[csf("lot")]."');\n";
		echo "$('#cbo_uom').val(".$row[csf("unit_of_measure")].");\n";
		//echo "$('#txt_rate').val(".$row[csf("avg_rate_per_unit")].");\n";
		//echo "$('#txt_issue_challan_no').val('".$row[csf("challan_no")]."');\n";
		
		//echo "$('#txt_issue_qnty').val('".$row[csf("cons_quantity")]."');\n";
		//$totalIssued = return_field_value("sum(cons_quantity)","inv_transaction","issue_challan_no='".$row[csf("challan_no")]."' and prod_id='".$row[csf("prod_id")]."' and item_category=1 and transaction_type=4");
		//if($totalIssued=="") $totalIssued=0;
		echo "$('#total_issue').val('".$totalIssued."');\n";
		//$netUsed = $row[csf("cons_quantity")]-$totalIssued;
		//echo "$('#txt_net_used').val('".$netUsed."');\n";
		echo "$('#cbo_item_category').val(".$row[csf("item_category")].");\n";
		echo "$('#cbo_item_group').val(".$row[csf("item_group_id")].");\n";
		echo "$('#total_issue').val($total_issue-".$row[csf("to_isue_return")].");\n";
		echo "$('#txt_avrage_rate').val($avarage_rate_per_unit);\n";
		//echo "$('#item_group_id').val(".$row[csf("item_group_id")].");\n";
		
		
   	}	
	exit();	
}

  



//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$is_update_cond = ($operation == 1) ? " and id <> $update_id " : "" ;


	//---------------Check Receive date with Last Transaction date-------------//
	$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id $is_update_cond and store_id=$cbo_store_name and status_active = 1", "max_date");      
	if($max_transaction_date != "")
	{
		$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
		$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_return_date)));
		if ($receive_date < $max_transaction_date) 
		{
			echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot";
			//check_table_status($_SESSION['menu_id'], 0);
			disconnect($con);
			die;
		}
	}
	//---------------Check Last Transaction date End -------------//


	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here  
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		//---------------Check Duplicate product in Same return number ------------------------//
		$duplicate = is_duplicate_field("b.id","inv_receive_master a, inv_transaction b","a.id=b.mst_id and a.recv_number=$txt_return_no and b.prod_id=$txt_prod_id and b.transaction_type=4"); 
		if($duplicate==1) 
		{
			echo "20**Duplicate Product is Not Allow in Same Return Number.";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con); 
			die;
		}
		//------------------------------Check Brand END---------------------------------------//
		
 		
		//adjust product master table START-------------------------------------//
 		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$txt_return_value = str_replace("'","",$txt_return_value);
		$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value from product_details_master where id=$txt_prod_id");
		$presentStock=$presentStockValue=$presentAvgRate=$available_qnty=0;
 		foreach($sql as $result)
		{
			$presentStock			=$result[csf("current_stock")];
			$presentStockValue		=$result[csf("stock_value")];
			$presentAvgRate			=$result[csf("avg_rate_per_unit")];	
		}
		
		$txt_return_value=$presentAvgRate*$txt_return_qnty;
		$nowStock 		= $presentStock+$txt_return_qnty;
		$txt_return_value=$txt_return_qnty*$txt_avrage_rate;
		$nowStockValue 	= $presentStockValue+$txt_return_value;
		
		//Product table update
		$field_array_product="last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
		$data_array_product="".$txt_return_qnty."*".$nowStock."*".$nowStockValue."*'".$user_id."'*'".$pc_date_time."'";
		
		
		//$prodUpdate = sql_update("product_details_master",$field_array_product,$data_array_product,"id",$txt_prod_id,0);	 
		//adjust product master table END  -------------------------------------//
		
		/*$field_array = "id,mst_id,company_id,supplier_id,prod_id,item_category,transaction_type,transaction_date,store_id,cons_uom,cons_quantity,cons_rate,cons_amount,inserted_by,insert_date";
 		$data_array = "(".$transactionID.",".$id.",".$cbo_company_id.",".$cbo_return_to.",".$txt_prod_id.",1,3,".$txt_return_date.",".$cbo_store_name.",".$cbo_uom.",".$txt_issue_qnty.",".$txt_rate.",".$issue_stock_value.",'".$user_id."','".$pc_date_time."')"; */
		
 		 
		//yarn master table entry here START---------------------------------------//	
		//$currency=array(1=>"Taka",2=>"USD",3=>"EURO");	
		if(str_replace("'","",$txt_return_no)=="")
		{
			//$id=return_next_id("id", "inv_receive_master", 1);		
			//$new_recv_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GIIR', date("Y",time()), 5, "select recv_number_prefix,recv_number_prefix_num from inv_receive_master where company_id=$cbo_company_id and entry_form='28' $mrr_date_check order by id DESC ", "recv_number_prefix", "recv_number_prefix_num" ));
			
			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,str_replace("'","",$cbo_company_id),'EBIR',369,date("Y",time()))); 
			
			$field_array_receive="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, company_id, receive_date,challan_no, store_id,floor,room,rack,shelf,bin, location_id, exchange_rate, currency_id,  remarks, inserted_by, insert_date";
			$data_array_receive="(".$id.",'".$new_recv_number[1]."','".$new_recv_number[2]."','".$new_recv_number[0]."',369,".$cbo_item_category.",".$cbo_company_id.",".$txt_return_date.",".$txt_return_challan_no.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_location.",1,1,".$txt_remarks.",'".$user_id."','".$pc_date_time."')";
			//echo $field_array."<br>".$data_array;die;
			//$rID=sql_insert("inv_receive_master",$field_array_receive,$data_array_receive,1); 		
		}
		else
		{
			$new_recv_number[0] = str_replace("'","",$txt_return_no);
			$id=str_replace("'","",$txt_sys_id);			
			$field_array_receive="entry_form*item_category*company_id*receive_date*challan_no*store_id*floor*room*rack*shelf*bin*location_id*exchange_rate*currency_id*remarks*updated_by*update_date";
			$data_array_receive="369*".$cbo_item_category."*".$cbo_company_id."*".$txt_return_date."*".$txt_return_challan_no."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$cbo_location."*1*1*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'";
			//echo $field_array."<br>".$data_array;die;
 			//$rID = sql_update("inv_receive_master",$field_array_receive,$data_array_receive,"id",$id,1);
		}
		//yarn master table entry here END---------------------------------------// 
		 
  		
		//transaction table insert here START--------------------------------//
		if($txt_reject_qnty=="")$txt_reject_qnty=0;
		$txt_amount=((str_replace("'","",$txt_return_qnty))*str_replace("'","",$txt_avrage_rate));
		//echo $txt_amount; die;
		
		
		//$dtlsid = return_next_id("id", "inv_transaction", 1);		 
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans_insert = "id,mst_id,company_id,prod_id,item_category,transaction_type,transaction_date,store_id,floor_id,room,rack,self,bin_box,machine_id,cons_uom,cons_quantity,cons_reject_qnty,cons_rate,cons_amount,balance_qnty,balance_amount,issue_challan_no,remarks,inserted_by,insert_date";
 		$data_array_trans_insert = "(".$transactionID.",".$id.",".$cbo_company_id.",".$txt_prod_id.",".$cbo_item_category.",4,".$txt_return_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_machine_no.",".$cbo_uom.",".$txt_return_qnty.",".$txt_reject_qnty.",".$txt_avrage_rate.",".$txt_amount.",".$txt_return_qnty.",".$txt_amount.",".$txt_return_challan_no.",".$txt_remarks.",'".$user_id."','".$pc_date_time."')";
		//echo $field_array."<br>".$data_array;die;
		//$dtlsrID = sql_insert("inv_transaction",$field_array_trans_insert,$data_array_trans_insert,1);
 		//transaction table insert here END ---------------------------------//
		$prodUpdate=$rID=$dtlsrID=$serialUpdate=true;
		
		
		
		$txt_serial_id 	= str_replace("'","",$txt_serial_id);
 		if($txt_serial_id!="")
		{
			$field_array_serial="issue_trans_id*is_issued*updated_by*update_date";
			if( strpos(trim($txt_serial_no), ",")>0)
			{
				$se_data=explode(",",str_replace("'","",$txt_serial_no));
				//echo $se_data; die;
				if( (count($se_data)<=str_replace("'","",$txt_return_qnty)))
				{
					$txt_serial_id_arr=explode(",",$txt_serial_id);
					if(count($txt_serial_id_arr)>0)
					{
						foreach($txt_serial_id_arr as $serial_id)
						{
							$update_data_serial[$serial_id]=explode("*",("".$transactionID."*0*'".$user_id."'*'".$pc_date_time."'"));
						}
					}
					
					$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
					//$serialUpdate = execute_query("update inv_serial_no_details set issue_rtn_trans_id=$transactionID , is_issued=0 where id in ($txt_serial_id)",0);
				}
				else
				{
					echo "50";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con); 
					die;
				}
			}
			else
			{
				$txt_serial_id_arr=explode(",",$txt_serial_id);
				if(count($txt_serial_id_arr)>0)
				{
					foreach($txt_serial_id_arr as $serial_id)
					{
						$update_data_serial[$serial_id]=explode("*",("".$transactionID."*0*'".$user_id."'*'".$pc_date_time."'"));
					}
				}
				$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
				//$serialUpdate 	= execute_query("update inv_serial_no_details set issue_rtn_trans_id=$transactionID , is_issued=0 where id in ($txt_serial_id)",0);
				
			}
		}
		
		
		
		$prodUpdate = sql_update("product_details_master",$field_array_product,$data_array_product,"id",$txt_prod_id,1);
		if(str_replace("'","",$txt_return_no)=="")
		{
			$rID=sql_insert("inv_receive_master",$field_array_receive,$data_array_receive,1); 		
		}
		else
		{
 			$rID = sql_update("inv_receive_master",$field_array_receive,$data_array_receive,"id",$id,1);
		}
		
		$dtlsrID = sql_insert("inv_transaction",$field_array_trans_insert,$data_array_trans_insert,1);
		
				
		
		//echo "10**".$prodUpdate." && ".$rID." && ".$dtlsrID;oci_rollback($con);die;
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		//mysql_query("ROLLBACK");die;
		if($db_type==0)
		{
			if( $prodUpdate && $rID && $dtlsrID  && $serialUpdate)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_recv_number[0]."**".$transactionID."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_recv_number[0];
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if( $prodUpdate && $rID && $dtlsrID  && $serialUpdate)
			{
				oci_commit($con);  
				echo "0**".$new_recv_number[0]."**".$transactionID."**".$id;
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$new_recv_number[0];
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$id=str_replace("'","",$txt_sys_id);
		//table lock here 
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		//check update id
		if( str_replace("'","",$update_id) == "" || str_replace("'","",$txt_prod_id) == "" || str_replace("'","",$before_prod_id) == "" )
		{
			echo "15";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con); 
			exit(); 
		}
		
		
		$sql = sql_select("select a.cons_quantity,a.cons_amount,b.current_stock,b.stock_value from inv_transaction a, product_details_master b where a.id=$update_id and a.prod_id=b.id");
		$beforeReturnQnty=$beforeReturnValue=0;
		$currentStockQnty=$currentStockValue=$before_available_qnty=0;
		foreach($sql as $result)
		{
			//current stock
			$beforeStockQnty		=$result[csf("current_stock")];
			$beforeStockValue		=$result[csf("stock_value")];
			//before return qnty
			$beforeReturnQnty		=$result[csf("cons_quantity")];
			$beforeReturnValue		=$result[csf("cons_amount")];
			
 		}
		
		//adjust product master table START-------------------------------------//
 		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$txt_return_value=$txt_return_qnty*$txt_avrage_rate;
		$txt_prod_id = str_replace("'","",$txt_prod_id);
		$update_array_product="last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
		$update_data = $updateID_array = array();
		if(str_replace("'","",$txt_prod_id) == str_replace("'","",$before_prod_id))
		{
			$presentStockQnty   = ($beforeStockQnty-$beforeReturnQnty)+$txt_return_qnty; //current qnty - before qnty + present return qnty
			$presentStockValue  = ($beforeStockValue-$beforeReturnValue)+$txt_return_value; 
			$data_array_product_same = "".$txt_return_qnty."*".$presentStockQnty."*".$presentStockValue."*'".$user_id."'*'".$pc_date_time."'";
 			//$prodUpdate = sql_update("product_details_master",$update_array_product,$data_array_product_same,"id",$txt_prod_id,1);
		}
		else
		{
			//before
			$presentStockQnty_before   = $beforeStockQnty-$beforeReturnQnty; //current qnty - before qnty
			$presentStockValue_before  = $beforeStockValue-$beforeReturnValue; 
			$updateID_array_before[]=$before_prod_id;
			$update_data_before[$before_prod_id]=explode("*",("".$txt_return_qnty."*".$presentStockQnty_before."*".$presentStockValue_before."*'".$user_id."'*'".$pc_date_time."'"));
			
			//$prodUpdate_before=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_product,$update_data_before,$updateID_array_before),0);
			
			//current
			$sql = sql_select("select current_stock,stock_value,avg_rate_per_unit from product_details_master  where id=$txt_prod_id");
			foreach($sql as $result)
			{
				//current stock after product
				$currentStockQntyAfter		=$result[csf("current_stock")];
				$currentStockValueAfter		=$result[csf("stock_value")];
				$currentAvarageRateAfter		=$result[csf("avg_rate_per_unit")];
				
			}
			$txt_return_value=$txt_return_qnty*$currentAvarageRateAfter;
			$presentStockQnty   = $currentStockQntyAfter+$txt_return_qnty; //current qnty + present return qnty
			$presentStockValue  = $currentStockValueAfter+$txt_return_value; 
			
			$updateID_array_after[]=$txt_prod_id;
			$update_data_after[$txt_prod_id]=explode("*",("".$txt_return_qnty."*".$presentStockQnty."*".$presentStockValue."*'".$user_id."'*'".$pc_date_time."'"));
			
			//$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_product,$update_data_after,$updateID_array_after),1);
		}
		//adjust product master table END  -------------------------------------//
					
 				
  		//yarn receive master table UPDATE here START----------------------//		
 		$field_array_receive_update="item_category*company_id*receive_date*challan_no*store_id*floor*room*rack*shelf*bin*location_id*supplier_id*remarks*updated_by*update_date";
		$data_array_receive_update="".$cbo_item_category."*".$cbo_company_id."*".$txt_return_date."*".$txt_return_challan_no."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$cbo_location."*".$txt_supplier_id."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'";
 		//echo $field_array."<br>".$data_array;die;
		//yarn receive master table entry here END---------------------------------------//	 
		//$rID=sql_update("inv_receive_master",$field_array_receive_update,$data_array_receive_update,"recv_number",$txt_return_no,1);
  		 
  		
		//transaction table update here START--------------------------------//
 		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		if($txt_reject_qnty=="")$txt_reject_qnty=0;
		$txt_amount=((str_replace("'","",$txt_return_qnty))*str_replace("'","",$txt_avrage_rate));
		
		
		
		$field_array_trans = "company_id*prod_id*item_category*transaction_type*transaction_date*store_id*floor_id*room*rack*self*bin_box*machine_id*cons_uom*cons_quantity*cons_reject_qnty*cons_rate*cons_amount*balance_qnty*balance_amount*issue_challan_no*remarks*updated_by*update_date";
 		$data_array_trans = "".$cbo_company_id."*".$txt_prod_id."*".$cbo_item_category."*4*".$txt_return_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$cbo_machine_no."*".$cbo_uom."*".$txt_return_qnty."*".$txt_reject_qnty."*".$txt_avrage_rate."*".$txt_amount."*".$txt_return_qnty."*".$txt_amount."*".$txt_return_challan_no."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'";
		//echo $field_array."<br>".$data_array;die;
		//$transID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
		
		$prodUpdate=$prodUpdate_before=$rID=$transID=$serialUpdate=$serialDelete=true;
		
		
				
 		//transaction table update here END ---------------------------------//
		
		
		$txt_serial_id 	= str_replace("'","",$txt_serial_id);
 		if($txt_serial_id!="")
		{
			$before_serial_id=trim(str_replace("'","",$before_serial_id));$txt_serial_id=trim(str_replace("'","",$txt_serial_id));$update_id=trim(str_replace("'","",$update_id));
			$field_array_serial="issue_trans_id*is_issued*updated_by*update_date";
			if( strpos(trim($txt_serial_no), ",")>0)
			{
				$se_data=explode(",",str_replace("'","",$txt_serial_no));
				if( (count($se_data)<=str_replace("'","",$txt_return_qnty)))
				{
					
					$txt_serial_id_arr=explode(",",$before_serial_id);
					if(count($txt_serial_id_arr)>0)
					{
						foreach($txt_serial_id_arr as $serial_id)
						{
							$update_data_serial[$serial_id]=explode("*",("0*1*'".$user_id."'*'".$pc_date_time."'"));
						}
						$serialDelete=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
					}
					$txt_serial_id_arr=explode(",",$txt_serial_id);
					if(count($txt_serial_id_arr)>0)
					{
						foreach($txt_serial_id_arr as $serial_id)
						{
							$update_data_serial[$serial_id]=explode("*",("".$update_id."*0*'".$user_id."'*'".$pc_date_time."'"));
						}
					}
					$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
					/*if($before_serial_id!="")
					{
					$serialDelete=execute_query("update inv_serial_no_details set issue_rtn_trans_id=0 , is_issued=1 where id in ($before_serial_id)",1);
					}
					$serialUpdate = execute_query("update inv_serial_no_details set issue_rtn_trans_id=$update_id , is_issued=0 where id in ($txt_serial_id)",1);*/
				}
				else
				{
					echo "50";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con); 
					die;
				}
			}
			else
			{
				if($before_serial_id!="")
				{
					$txt_serial_id_arr=explode(",",$before_serial_id);
					if(count($txt_serial_id_arr)>0)
					{
						foreach($txt_serial_id_arr as $serial_id)
						{
							$update_data_serial[$serial_id]=explode("*",("0*1*'".$user_id."'*'".$pc_date_time."'"));
						}
						$serialDelete=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
					}
				}
				$txt_serial_id_arr=explode(",",$txt_serial_id);
				if(count($txt_serial_id_arr)>0)
				{
					foreach($txt_serial_id_arr as $serial_id)
					{
						$update_data_serial[$serial_id]=explode("*",("".$update_id."*0*'".$user_id."'*'".$pc_date_time."'"));
					}
				}
				$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
				/*if($before_serial_id!="")
				{
				$serialDelete	=execute_query("update inv_serial_no_details set issue_rtn_trans_id=0 , is_issued=1 where id in ($before_serial_id)",1);
				}
				$serialUpdate 	= execute_query("update inv_serial_no_details set issue_rtn_trans_id=$update_id , is_issued=0 where id in ($txt_serial_id)",1);*/
			}
		}
		
		
		
		if(str_replace("'","",$txt_prod_id) == str_replace("'","",$before_prod_id))
		{
 			$prodUpdate = sql_update("product_details_master",$update_array_product,$data_array_product_same,"id",$txt_prod_id,1);
		}
		else
		{
			$prodUpdate_before=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_product,$update_data_before,$updateID_array_before),1);
			
			$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_product,$update_data_after,$updateID_array_after),1);
		}
		
		$rID=sql_update("inv_receive_master",$field_array_receive_update,$data_array_receive_update,"id",$id,1);
		$transID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1); 
			
		//order_wise_pro_details table data insert END -----//
 		//release lock table
		//echo "20**".$prodUpdate." && ".$rID." && ".$transID."&&".$proportQ;mysql_query("ROLLBACK");die;
		if($db_type==0)
		{
			if($prodUpdate && $prodUpdate_before && $rID && $transID && $serialDelete && $serialUpdate )
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_return_no)."**".str_replace("'","",$update_id)."**".str_replace("'","",$id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_return_no);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($prodUpdate && $prodUpdate_before && $rID && $transID && $serialDelete && $serialUpdate )
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_return_no)."**".str_replace("'","",$update_id)."**".str_replace("'","",$id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_return_no);
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------
		if( str_replace("'","",$update_id) == "" || str_replace("'","",$txt_prod_id) == "" || str_replace("'","",$before_prod_id) == "" )
		{
			echo "16**Delete not allowed. Problem occurred"; die;
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con); 
			exit(); 
		}
		else 
		{
			//echo "10**select id from inv_transaction where transaction_type in(2,3,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and id >$update_id"; die;
			$chk_next_transaction=return_field_value("id","inv_transaction","transaction_type in(2,3,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and id >$update_id ","id");
			if($chk_next_transaction !="")
			{ 
				echo "17**Delete not allowed.This item is used in another transaction"; disconnect($con); die;
			}
			else
			{
				$sql = sql_select("select a.prod_id,a.cons_quantity,a.cons_rate,a.cons_amount,b.avg_rate_per_unit,b.current_stock,b.stock_value from inv_transaction a, product_details_master b  where a.status_active=1 and a.id=$update_id and a.prod_id=b.id");
			
				$before_prod_id=$before_receive_qnty=$before_rate=$beforeAmount=$before_brand="";
				$beforeStock=$beforeStockValue=$beforeAvgRate=0;
				foreach( $sql as $row)
				{
					$before_prod_id 		= $row[csf("prod_id")]; 
					$before_receive_qnty 	= $row[csf("cons_quantity")]; //stock qnty
					$before_rate 			= $row[csf("cons_rate")]; 
					$beforeAmount			= $row[csf("cons_amount")]; //stock value
					$beforeStock			=$row[csf("current_stock")];
					$beforeStockValue		=$row[csf("stock_value")];
					$beforeAvgRate			=$row[csf("avg_rate_per_unit")];	
				}
				//stock value minus here---------------------------//
				$adj_beforeStock			=$beforeStock-$before_receive_qnty;
				$adj_beforeStockValue		=$beforeStockValue-$beforeAmount;
				$adj_beforeAvgRate			=number_format(($adj_beforeStockValue/$adj_beforeStock),$dec_place[3],'.','');		
			
				$field_array_product="avg_rate_per_unit*current_stock*stock_value*updated_by*update_date";
				$data_array_product = "".$adj_beforeAvgRate."*".$adj_beforeStock."*".number_format($adj_beforeStockValue,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'";
				
				$field_array_trans="updated_by*update_date*status_active*is_deleted";
				$data_array_trans="".$user_id."*'".$pc_date_time."'*0*1";
				
				$rID=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
				$rID2=sql_updates("product_details_master",$field_array_product,$data_array_product,"id",$txt_prod_id,1);
			}
		}
		//echo "10**".$rID."**".$rID2; die;
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$hidden_mrr_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$hidden_mrr_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$hidden_mrr_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$hidden_mrr_id);
			}
		}
		disconnect($con);
		die;
	}		
}
function sql_updates($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
{
	
	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);	
	
	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}
	
	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";
	
	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);	
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}

	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	echo "10**".$strQuery; die;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd) 
		return "1";
	else 
		return "0";
	
	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con); 
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}


if($action=="return_number_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(mrr)
	{
 		$("#hidden_return_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="780" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="200">Return No</th>
                    <th width="150">Date</th>
                    <th width="150">Challan No</th>
                    <th width="170">Location</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr> 
                
                    <td>
                       <input type="text" id="src_return_no" name="src_return_no" style="width:190px;" class="text_boxes" />
                    </td>
                    <td align="center">
                    	<input type="text" name="txt_return_date" id="txt_return_date" class="datepicker" style="width:145px;" placeholder="Select Date" />
                    </td>
                    <td align="center" >
                    	<input type="text" id="src_challan_no" name="src_challan_no" style="width:145px;" class="text_boxes" />				
                    </td>    
                    <td align="center">
						<? 
                        	echo create_drop_down( "cbo_location", 168, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$company' $company_location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", "", "" );
                        ?>
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('src_return_no').value+'_'+document.getElementById('txt_return_date').value+'_'+document.getElementById('src_challan_no').value+'_'+document.getElementById('cbo_location').value+'_'+<? echo $company; ?>, 'create_return_search_list_view', 'search_div', 'emb_material_issue_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
            <input type="hidden" id="hidden_return_number" value="" />
            </tbody>
        </table>    
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_return_search_list_view")
{
	$ex_data = explode("_",$data);
	$return_no = $ex_data[0];
	$return_date = $ex_data[1];
	$challan_no = $ex_data[2];
	$location = $ex_data[3];
	$company = $ex_data[4];
	
	
	if($return_no!="") $return_no =" and a.recv_number like '%$return_no'"; else $return_no="";
	if($return_date!="") $return_date =" and a.receive_date='".date("j-M-Y",strtotime($return_date))."'"; else $return_date="";
	if($challan_no!="") $challan_no =" and a.challan_no like '%$challan_no'"; else $challan_no="";
	if($location!=0) $location =" and a.location_id='$location'"; else $location="";
	if($company!="") $company =" and a.company_id='$company'"; else $company="";
	
	
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id='$user_id'");
	$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_supplier_id = $userCredential[0][csf('supplier_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	$cre_item_cate_id = $userCredential[0][csf('item_cate_id')];
	
	$credientian_cond="";
	if($cre_company_id!="") $credientian_cond=" and a.company_id in($cre_company_id)";
	//if($cre_supplier_id!="") $credientian_cond.=" and a.supplier_id in($cre_supplier_id)";
	if($cre_store_location_id!="") $credientian_cond.=" and b.store_id in($cre_store_location_id)";
	if($cre_item_cate_id!="") $credientian_cond.=" and b.item_category in(106)";
	
	$sql = "select a.id,a.recv_number,a.company_id,a.receive_date,a.challan_no,a.location_id,b.prod_id,a.is_posted_account
			 from inv_receive_master a, inv_transaction b
			where a.id=b.mst_id and b.transaction_type=4 and a.entry_form=369 $company $location $challan_no $return_date $return_no  $credientian_cond
			group by a.id ,a.recv_number,a.company_id,a.receive_date,a.challan_no,a.location_id,b.prod_id,a.is_posted_account"; 
 	//echo $sql; 
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$arr=array(3=>$location_arr);
 
  	echo create_list_view("list_view", "Return No,Return Date,Return Challan No,Location","150,150,150,200","700","260",0, $sql , "js_set_value", "id,recv_number,is_posted_account", "", 1, "0,0,0,location_id", $arr, "recv_number,receive_date,challan_no,location_id","","",'0,0,0,0') ;	
 	exit();
}
 
 

if($action=="populate_master_from_data")
{  
 	$sql = "select id,recv_number,company_id,receive_date,challan_no,store_id,location_id
			from inv_receive_master 
			where id=$data";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_sys_id').val('".$row[csf("id")]."');\n";
		echo "$('#txt_return_no').val('".$row[csf("recv_number")]."');\n";
 		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_location').val(".$row[csf("location_id")].");\n";
		echo "$('#txt_return_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#txt_return_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "disable_enable_fields( 'cbo_company_id', 1, '', '' );\n"; // disable true
				
   	}	
	exit();	
}



if($action=="show_dtls_list_view")	
{
	$return_number=str_replace("'","",$data);
	$ex_data = explode("**",$data);
	$return_number = $ex_data[0];
	$ret_mst_id = str_replace("'","",$ex_data[1] );
	//echo $ret_mst_id;die;
	$cond="";
	if($ret_mst_id!="") $cond .= " and a.id='$ret_mst_id'";
	

	$sql = "select a.recv_number,a.company_id,a.receive_date,a.item_category,a.recv_number,b.id,b.cons_quantity,b.cons_reject_qnty,b.cons_uom,b.cons_rate,b.cons_amount,c.product_name_details,c.id as prod_id   
			from  inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id
			where a.id=b.mst_id and b.item_category in(106) and b.transaction_type=4 $cond";		
	//echo $sql; //die;
	$result = sql_select($sql);
	$i=1;
	$rettotalQnty=0;
	$rcvtotalQnty=0;
	$totalAmount=0;
	?> 
     	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:980px" rules="all" >
        	<thead>
            	<tr>
                	<th>SL</th>
                    <th>Return No</th>
                    <th>Item Description</th>
                    <th>Product ID</th>
                    <th>Return Qnty</th>
                    <th>Reject Qnty</th>
                    <th>UOM</th>
                    <th>Rate</th>                    
                    <th>Return Value</th> 
                </tr>
            </thead>
            <tbody>
            	<? 
				foreach($result as $row){					
					if($i%2==0)
						$bgcolor="#E9F3FF";
					else 
						$bgcolor="#FFFFFF";
 					
					$rettotalQnty +=$row[csf("cons_quantity")];
 					$totalAmount +=$row[csf("cons_amount")];
 					
 				?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")];?>","child_form_input_data","requires/emb_material_issue_return_controller")' style="cursor:pointer" >
                        <td width="30"><? echo $i; ?></td>
                        <td width="100"><p><? echo $row[csf("recv_number")]; ?></p></td>
                        <td width="200"><p><? echo $row[csf("product_name_details")]; ?></p></td>
                        <td width="80"><p><? echo $row[csf("prod_id")]; ?></p></td>
                        <td width="90" align="right"><p><? echo $row[csf("cons_quantity")]; ?></p></td>
                        <td width="90" align="right"><p><? echo $row[csf("cons_reject_qnty")]; ?></p></td>
                        <td width="80"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row[csf("cons_rate")],2,'.',','); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row[csf("cons_amount")],2,'.',','); ?></p></td>
                   </tr>
                <? $i++; } ?>
                	<tfoot>
                        <th colspan="4">Total</th>                         
                        <th><? echo $rettotalQnty; ?></th> 
                        <th colspan="3"></th>
                        <th><? echo number_format($totalAmount,2,'.',','); ?></th>
                   </tfoot>
            </tbody>
        </table>
    <?
	exit();
}

if($action=="child_form_input_data")
{
	//$data // transaction id
  	$sql = "select b.id as prod_id, b.product_name_details, b.lot, a.id as tr_id,a.company_id,a.location_id, a.store_id,a.floor_id,a.room,a.rack,a.self,a.bin_box, a.cons_uom, a.cons_rate, a.cons_quantity,a.cons_reject_qnty, a.cons_amount, a.issue_challan_no,a.remarks,a.item_category,a.machine_id,b.item_group_id 	
			from inv_transaction a, product_details_master b
 			where a.id=$data and a.status_active=1 and a.item_category in(106) and transaction_type=4 and a.prod_id=b.id and b.status_active=1";
 	//echo $sql;die;
	$result = sql_select($sql);
	foreach($result as $row)
	{
 		echo "$('#txt_item_description').val('".$row[csf("product_name_details")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#before_prod_id').val('".$row[csf("prod_id")]."');\n";
		//echo "$('#txt_yarn_lot').val('".$row[csf("lot")]."');\n";
		//load_drop_down( 'requires/emb_material_issue_return_controller', this.value, 'load_drop_down_store', 'store_td' );
		
		echo "load_drop_down('requires/emb_material_issue_return_controller', '".$row[csf('company_id')]."','load_drop_down_store', 'store_td');\n";
		
		//echo "load_room_rack_self_bin('requires/emb_material_issue_return_controller*106', 'store','store_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."',this.value);\n";
		echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";

		echo "load_room_rack_self_bin('requires/emb_material_issue_return_controller', 'floor','floor_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "load_room_rack_self_bin('requires/emb_material_issue_return_controller', 'room','room_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "load_room_rack_self_bin('requires/emb_material_issue_return_controller', 'rack','rack_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "load_room_rack_self_bin('requires/emb_material_issue_return_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";	
		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";
		echo "load_room_rack_self_bin('requires/emb_material_issue_return_controller', 'bin','bin_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('self')]."',this.value);\n";	
		echo "$('#cbo_bin').val('".$row[csf("bin_box")]."');\n";

		echo "$('#txt_return_qnty').val('".$row[csf("cons_quantity")]."');\n"; 
		echo "$('#txt_reject_qnty').val('".$row[csf("cons_reject_qnty")]."');\n";
 		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#cbo_uom').val('".$row[csf("cons_uom")]."');\n";
		echo "$('#cbo_machine_no').val(".$row[csf("machine_id")].");\n";
		echo "$('#cbo_item_category').val(".$row[csf("item_category")].");\n";
		echo "$('#cbo_item_group').val(".$row[csf("item_group_id")].");\n";
		 
		$totalIssued = return_field_value("sum(b.cons_quantity+b.cons_reject_qnty) as to_issue","inv_issue_master a, inv_transaction b"," a.id=b.mst_id and b.prod_id='".$row[csf("prod_id")]."' and b.item_category in(106) and b.transaction_type=2 group by prod_id","to_issue");
		$totalIssuedReturn = return_field_value("sum(b.cons_quantity+b.cons_reject_qnty) as to_issue_return","inv_receive_master a, inv_transaction b"," a.id=b.mst_id and b.prod_id='".$row[csf("prod_id")]."' and b.item_category in(106) and b.transaction_type=4 group by prod_id","to_issue_return");
		if($totalIssued=="") $totalIssued=0;
		if($totalIssuedReturn=="") $totalIssuedReturn=0;
		$current_total_issue=$totalIssued-$totalIssuedReturn;
		$current_total_issue=$current_total_issue+$row[csf("cons_quantity")]+$row[csf("cons_reject_qnty")];
		echo "$('#total_issue').val(".$current_total_issue.");\n";
		if($db_type==0)
		{
			//echo "select group_concat(serial_no) as sr from inv_serial_no_details where issue_rtn_trans_id=".$row[csf("tr_id")]."";
			$serialNo = return_field_value("group_concat(serial_no) as sr","inv_serial_no_details","issue_rtn_trans_id=".$row[csf("tr_id")]."","sr");
			$serialID = return_field_value("group_concat(id) as id","inv_serial_no_details","issue_rtn_trans_id=".$row[csf("tr_id")]."","id");		}
		else
		{
			//echo "select LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as sr from inv_serial_no_details where issue_trans_id=".$row[csf("tr_id")];
			$serialNo=return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as sr","inv_serial_no_details","issue_trans_id=".$row[csf("tr_id")],"sr");
			$serialID=return_field_value("LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as id","inv_serial_no_details","issue_trans_id=".$row[csf("tr_id")],"id");
		}
		
		
		echo "$('#txt_serial_no').val('".$serialNo."');\n";
		echo "$('#txt_serial_id').val('".$serialID."');\n";
		echo "$('#before_serial_id').val('".$serialID."');\n";
		echo "$('#txt_avrage_rate').val('".$row[csf("cons_rate")]."');\n";		
		echo "$('#update_id').val(".$row[csf("tr_id")].");\n";
	}
 	echo "set_button_status(1, permission, 'fnc_gi_issue_return_entry',1);\n";		
  	exit();
}

if ($action=="general_item_issue_return_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql=" select id, recv_number, location_id, receive_date, challan_no from  inv_receive_master where id='$data[1]' and company_id='$data[0]' and entry_form=369";
	//echo $sql;die;
	$dataArray=sql_select($sql);

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$rec_id_arr=return_library_array( "select id, recv_number from  inv_receive_master", "id", "recv_number"  );
?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result['plot_no']; ?> 
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?> 
						Block No: <? echo $result['block_no'];?> 
						City No: <? echo $result['city'];?> 
						Zip Code: <? echo $result['zip_code']; ?> 
						Province No: <?php echo $result['province'];?> 
						Country: <? echo $country_arr[$result['country_id']]; ?><br> 
						Email Address: <? echo $result['email'];?> 
						Website No: <? echo $result['website'];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Reprot</u></strong></td>
        </tr>
        <tr>
        	<td width="120"><strong>Return ID:</strong></td><td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="100"><strong>Return Date :</strong></td> <td width="230px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
            <td width="120"><strong>Return Challan:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
    </table>
         <br>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="40">SL</th>
                <th width="80" align="center">Item Category</th>
                <th width="150" align="center">Item Group</th>
                <th width="200" align="center">Item Description</th>
                <th width="50" align="center">UOM</th> 
                <th width="80" align="center">Returned. Qnty.</th>
                <th width="50" align="center">Rate</th>
                <th width="70" align="center">Return Value</th>
                <th width="80" align="center">Store</th> 
            </thead>
<?
	 $i=1;
	$item_name_arr=return_library_array( "select id, item_name from  lib_item_group", "id", "item_name"  );
	$sql_dtls= "select a.id, b.item_category,
	b.cons_uom, b.cons_quantity, b.cons_rate, b.cons_amount, b.store_id,
	c.item_group_id, $concat(c.sub_group_name $concat_coma ' ' $concat_coma c.item_description $concat_coma ' ' $concat_coma c.item_size ) as product_name_details
	from inv_receive_master a, inv_transaction b,  product_details_master c
	where a.id='$data[1]' and a.company_id='$data[0]' and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=4 and b.item_category in (".implode(",",array_flip(106)).") and a.entry_form=369 ";
	//echo $sql_dtls;die;
	$sql_result=sql_select($sql_dtls);
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			$cons_quantity=$row[csf('cons_quantity')];
			$cons_quantity_sum += $cons_quantity;
			
			$cons_amount=$row[csf('cons_amount')];
			$cons_amount_sum += $cons_amount;
			
			$desc=$row[csf('item_description')];
			
			if($row[csf('item_size')]!="")
			{
				$desc.=", ".$row[csf('item_size')];
			}
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i; ?></td>
                <td><? echo $item_category[$row[csf('item_category')]]; ?></td>
                <td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
                <td><? echo $row[csf('product_name_details')]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                <td align="right"><? echo $row[csf('cons_quantity')]; ?></td>
                <td align="right"><? echo number_format($row[csf('cons_rate')],2,'.',','); ?></td>
                <td align="right"><? echo number_format($row[csf('cons_amount')],2,'.',','); ?></td>
                <td><? echo $store_library[$row[csf('store_id')]]; ?></td>
			</tr>
			<?
			$i++;
			}
			?>
        	<tr> 
                <td align="right" colspan="5" >Total</td>
                <td align="right"><? echo number_format($cons_quantity_sum,0,'',','); ?></td>
                <td align="right" colspan="2" ><? echo number_format($cons_amount_sum,2,'.',','); ?></td>
                <td align="right">&nbsp;</td>
			</tr>
		</table>
        <br>
		 <?
            echo signature_table(187, $data[0], "900px");
         ?>
      </div>
   </div> 
<?
exit();
}
?>