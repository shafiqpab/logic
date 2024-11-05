<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

/*if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 140, "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type in (5,6,7,23) group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );  	 
	exit();
}
*/

if ($action=="load_drop_item_group")
{
	echo create_drop_down( "cbo_item_group", 160, "select id,item_name from lib_item_group where item_category=$data and status_active=1 and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", 0, "", 0,"" );
	exit();
}
	
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_room_rack_self_bin('requires/chemical_dyes_issue_return_controller*5_6_7_23', 'store','store_td', $('#cbo_company_id').val(), this.value);",0 );     	 
	exit();
}
if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/chemical_dyes_issue_return_controller",$data);
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
	<table width="850" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="120">Item Category</th>
                    <th width="200">Item Description</th>
                     <th width="100">Product Id</th>
                    <th width="250">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>                    
                    <td>
                        <?  
							echo create_drop_down( "cbo_item_category", 160, $item_category,"", 1, "-- Select --", 0, "", 0,"5,6,7,23","","","" );
                        ?>
                    </td>
                    <td width="" align="center" >
                    	  <input name="txt_itemdescrition" id="txt_itemdescrition" class="text_boxes" style="width:200px"  />
                    </td>
                     <td width="" align="center" >
                    	  <input name="txt_prod_id" id="txt_prod_id" class="text_boxes_numeric" style="width:80px"  />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
                    </td>
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_item_category').value+'_'+document.getElementById('txt_itemdescrition').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_prod_id').value, 'create_item_search_list_view', 'search_div', 'chemical_dyes_issue_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here -->
                     <input type="hidden" id="hidden_recv_number" value="" />
                      <input type="hidden" id="new_prod_id" value="" />
                    <!--  END  -->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" valign="top" id="search_div"> </div> 
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
	$item_descrition = $ex_data[1];
	$prod_id = $ex_data[5];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$company = $ex_data[4];
	$sql_cond="";
	if( trim($item_category)!=0 )  $item_category=" and b.item_category=$item_category"; else $item_category="and b.item_category  in(5,6,7,23)";
	if( trim($item_descrition)!="" )     $item_descrition=" and c.product_name_details like '%$ex_data[1]%'"; else $item_descrition="";
	if( trim($prod_id)!=0 )     $product_cond=" and c.id='$prod_id'"; else $product_cond="";
	if($db_type==2 ) { $year_id=" extract(year from a.insert_date) as year"; }
	if($db_type==0)  { $year_id="YEAR(a.insert_date) as year"; }
	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
	
	if( trim($fromDate)!="" || trim($toDate)!="" )
	{
		if($db_type==0)
		{
		$sql_cond .= " and a.issue_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
		$sql_cond .= " and a.issue_date  between '".change_date_format($fromDate,'yyyy-mm-dd',"-",1)."' and
		'".change_date_format($toDate,'yyyy-mm-dd','-',1)."'";
	    }
	}
	
	$sql="select b.id as tran_id,a.issue_number_prefix_num,$year_id,a.id as issue_id,c.id as prod_id,c.product_name_details,c.item_description,
	c.item_group_id,c.sub_group_name,c.item_size 
	from inv_issue_master a, inv_transaction b, product_details_master c  
	where a.id=b.mst_id and a.status_active=1 and b.prod_id=c.id and c.status_active in(1,3) and b.transaction_type=2
	$sql_cond $item_category $item_descrition $product_cond order by a.id ";
	
	//echo $sql;
		  
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$arr=array(3=>$item_group_arr);
 	echo create_list_view("list_view", "Product Id, Issue Id,Year, Item Group, Item SubGroup, Item Description, Item Size, Issue Challan No",
	"60,50,50,150,150,200,100,80","920","260",0, $sql , "js_set_value", "tran_id,issue_id,prod_id", "", 1, "0,0,0,item_group_id,0,0,0,0",
	$arr, "prod_id,issue_number_prefix_num,year,item_group_id,sub_group_name,item_description,item_size,challan_no", "","",'0,0,0,0,0,0') ;	
	exit();
}

if($action=="populate_data_from_data")
{  
	$ex_data = explode("_",$data);
	$trans_id = $ex_data[0];
	$issue_id = $ex_data[1];
	$prod_id=$ex_data[2];
	
	//if($db_type==0) $prod_des="concat(c.sub_group_name,',',c.item_description,',',c.item_size)";
	//if($db_type==2) $prod_des="(c.sub_group_name||','||c.item_description||','||c.item_size)";
	
	//$prev_issue_rtn_qnty=return_fi
	
	$sql = "select  c.id as prod_id,product_name_details,c.unit_of_measure,c.item_group_id,b.company_id,b.item_category,b.cons_amount,b.cons_rate,a.challan_no,
			b.cons_quantity,a.issue_number,b.store_id, b.floor_id, b.room, b.rack, b.self 
			from inv_issue_master a, inv_transaction b, product_details_master c
			where a.id=b.mst_id and b.prod_id=c.id and c.id=$prod_id  and b.id=$trans_id and a.id=$issue_id and b.transaction_type in(2) ";	
   
  // echo $sql;
   
    
	$res = sql_select($sql);
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	foreach($res as $row)
	{
		if($db_type==0)
		{
			$totalIssuedReturn = return_field_value("sum(b.cons_quantity+ IFNULL(b.cons_reject_qnty, 0) ) as to_issue_return","inv_transaction b"," b.prod_id='".$row[csf("prod_id")]."' and b.item_category  in(5,6,7,23) and b.transaction_type=4 and b.status_active=1 and b.issue_id=$issue_id ","to_issue_return");
		}
		else
		{
			$totalIssuedReturn = return_field_value("sum(b.cons_quantity+ NVL(b.cons_reject_qnty, 0) ) as to_issue_return","inv_transaction b"," b.prod_id='".$row[csf("prod_id")]."' and b.item_category  in(5,6,7,23) and b.transaction_type=4 and b.status_active=1 and b.issue_id=$issue_id ","to_issue_return");
		}
		$totalIssuedReturn = return_field_value("sum(b.cons_quantity+ NVL(b.cons_reject_qnty, 0) ) as to_issue_return","inv_transaction b"," b.prod_id='".$row[csf("prod_id")]."' and b.item_category  in(5,6,7,23) and b.transaction_type=4 and b.status_active=1 and b.issue_id=$issue_id ","to_issue_return");
		$net_used=$row[csf("cons_quantity")]-$totalIssuedReturn;
		echo "$('#total_issue').val(".$net_used.");\n";
		echo "$('#txt_item_description').val('".$row[csf("product_name_details")]."');\n";
		echo "$('#cbo_uom').val(".$row[csf("unit_of_measure")].");\n";
		echo "$('#cbo_item_category').val(".$row[csf("item_category")].");\n";
		echo "$('#txt_return_qnty').val(".$totalIssuedReturn.");\n";
		echo "$('#txt_prod_id').val(".$row[csf("prod_id")].");\n";
		echo "$('#new_prod_id').val(".$row[csf("prod_id")].");\n";
		
		
		echo "$('#hidden_issue_id').val('".$issue_id."');\n";
		echo "$('#txt_amount_qnty').val('".$row[csf("cons_amount")]."');\n";
		echo "$('#txt_rate').val('".$row[csf("cons_rate")]."');\n";
		echo "$('#txt_issue_challan').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_issue_qty').val(".$row[csf("cons_quantity")].");\n";
		echo "$('#txt_net_used').val(".$net_used.");\n";
		echo "$('#txt_issue_id').val('".$row[csf("issue_number")]."');\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_issue_return_controller*5_6_7_23', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_issue_return_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_issue_return_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_issue_return_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_issue_return_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";	
		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";
   	}	
	exit();	
}

 //for batch popup
 
if($action=="batch_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
	
	function js_set_value( data)
	{ 
		//alert(data) 
		var data=data.split("_");
		document.getElementById('hidden_batch_no').value=data[0];
		document.getElementById('hidden_batch_id').value=data[1];
		document.getElementById('issue_master_id').value=data[2];
		parent.emailwindow.hide();
	}
	
    </script>

</head>

<body>
<div align="center" style="width:100%; overflow-y:hidden;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="500" class="rpt_table">
                <thead>
                    <th>Search By</th>
                    <th>Search</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" value="">
                        <input type="hidden" name="hidden_batch_no" id="hidden_batch_no" value="">
                        <input type="hidden" name="issue_master_id" id="issue_master_id" value="">
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">	
                        <?
						if($cbo_issue_basis==5) { $search_by_arr=array(1=>"Batch No",2=>"Booking No");}
						if($cbo_issue_basis==7) { $search_by_arr=array(1=>"Requsign No",2=>"Booking No");}
                           
                            echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>                 
                    <td align="center">				
                        <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_issue_basis; ?>, 'create_batch_search_list_view', 'search_div', 'chemical_dyes_issue_return_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div" style="margin-top:10px"></div>   
        </form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}
//bat
if($action=="create_batch_search_list_view")
{
	$data=explode('_',$data);
	$order_arr=return_library_array( "select id,po_number from wo_po_break_down",'id','po_number');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$search_string="%".trim($data[0])."%";
	$search_by =$data[1];
	$company_id =$data[2];
	$issue_basis=$data[3];
	if($issue_basis==5)
	{
		if($search_by==1) $search_field=" and b.batch_no like '".$search_string."'";
		else $search_field="a.booking_no like '".$search_string."'";
		$batch_relation="";
		if($db_type==0) { $batch_relation=" and a.batch_no=b.id";}
		else { $batch_relation=" and a.batch_no=to_char(b.id)";}
		$sql = sql_select("select a.id as mst_id,a.issue_number,b.id as batch_id,b.batch_no,a.issue_purpose,a.location_id,a.order_id,a.challan_no 
		from inv_issue_master a, pro_batch_create_mst b where a.company_id=$company_id   and a.status_active=1 and a.is_deleted=0 and 
		a.entry_form=5 $batch_relation $search_field"); 
	}
	else
	{
		
		if($search_by==1) $search_field=" and b.requ_prefix_num like '".$search_string."'";
		else $search_field="a.booking_no like '".$search_string."'";
		$sql = sql_select("select a.id as mst_id,a.issue_number,b.id as batch_id,b.requ_no,b.id as requ_id,a.issue_purpose,a.location_id,a.order_id,a.challan_no 
		from inv_issue_master a, dyes_chem_issue_requ_mst b where a.company_id=$company_id and a.req_no=b.id  and a.status_active=1 and a.is_deleted=0
		and a.entry_form=5  $search_field"); 	
	}
	?>
         <table width="780" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                  <tr>                	 
                        <th width="30">Sl</th>
                        <th width="130">Issue number</th>
                        <? if($issue_basis==5) {  ?>
                        <th width="120">Batch No</th>
                        <?
						}
						else
						{
						?>
                        <th width="120">Requisition No</th>
                        <?
						}
						?>
                        <th width="120">Issue Purpose</th>
                        <th width="150">Location</th>
                        <th width="150">Order No</th>
                        <th width="100">Challan no</th>
                   </tr>
              </thead>
           </table>
      <div style="width:780px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden; cursor:pointer;" id="scroll_body">
          <table width="780" cellspacing="0" cellpadding="0" border="0" class="rpt_table" rules="all" align="center" id="tbl_list_search">
             <tbody>
          		<? 
				$i=1;
				foreach($sql as $row)
				  {					
					if($i%2==0) $bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					$ponumber_arr=array();
					foreach(array_unique(explode(",",$row[csf("order_id")])) as $order_id)
					{
					$ponumber_arr[]=$order_arr[$order_id];	
					}
					 if($issue_basis==5)
					 {
 				?>
                 	 <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf("batch_no")]."_".$row[csf("batch_id")]."_".$row[csf("mst_id")];?>")' style="cursor:pointer" >
                    <?
					 }
					 else
					 {
					 ?>
                     <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf("requ_no")]."_".$row[csf("requ_id")]."_".$row[csf("mst_id")];?>")' style="cursor:pointer" >
                    
                     <?
					 }
					 ?>
                        <td width="30"><? echo $i; ?></td>
                        <td width="130"  align="center"><p><? echo $row[csf("issue_number")]; ?></p></td>

                        <td width="120"  align="center"><p><? if($issue_basis==5) { echo $row[csf("batch_no")];} else { echo $row[csf("requ_no")];} ?></p></td>
                        <td width="120"  align="center"><p><? echo $yarn_issue_purpose[$row[csf("issue_purpose")]]; ?></p></td>
                        <td width="150" align="center"><p><? echo $location_arr[$row[csf("location_id")]]; ?></p></td>
                        <td width="150" align="center"><p><? echo implode(",",$ponumber_arr); ?></p></td>
                        <td width="100"  align="center"><p><? echo $row[csf("challan_no")]; ?></p></td>
                   </tr>
                <? $i++; } ?>
              
            </tbody>
        </table> 
	</div>
    <?
}

if($action=="populate_batch_no_data")
{ 
	 $data=explode("**",$data);
	 $company_name=$data[1];
	 $mst_id=$data[3];
	 $item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	 $sql="select a.id, a.prod_id, a.cons_quantity as issue_qty, c.product_name_details, c.item_category_id, c.item_group_id, a.batch_lot	
	 from inv_transaction a, inv_issue_master b, product_details_master c 
	 where b.id=$mst_id and  b.id=a.mst_id and a.prod_id=c.id and a.transaction_type=2 and b.entry_form=5 and b.company_id=$company_name ";

	//echo $sql;
	$item_result=sql_select($sql);
	if(count($item_result)>0)
	{
		?>
		 <table width="430" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr> 
                	<th width="20">Sl</th>               	 
                    <th width="100">Item Category</th>
                    <th width="40">Prod Id</th>
                    <th width="60">Lot</th>
                    <th width="80">Item Group</th>
                    <th>Item Description</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
          	foreach($item_result as $row)
			{		
			
			    $totalIssued = return_field_value("sum(b.cons_quantity+b.cons_reject_qnty) as to_issue","inv_issue_master a, inv_transaction b"," a.id=b.mst_id and b.prod_id='".$row[csf("prod_id")]."' and b.item_category  in(5,6,7,23) and b.transaction_type=2 group by prod_id","to_issue");
	            $totalIssuedReturn = return_field_value("sum(b.cons_quantity+b.cons_reject_qnty) as to_issue_return","inv_receive_master a, inv_transaction b"," a.id=b.mst_id and b.prod_id='".$row[csf("prod_id")]."' and b.item_category  in(5,6,7,23) and b.transaction_type=4 group by prod_id","to_issue_return");
	            $issue_remain=$totalIssued-$totalIssuedReturn;	
				if($issue_remain>0)
				{	
					if($i%2==0) $bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					?>
                 	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("prod_id")]."_".$row[csf("id")]."_".$mst_id;?>","child_form_item_data","requires/chemical_dyes_issue_return_controller")'  style="cursor:pointer" >
                        <td align="center"><? echo $i; ?></td>
                        <td><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
                        <td align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
                        <td align="center"><p><? echo $row[csf("batch_lot")]; ?></p></td>
                        <td><p><? echo $item_group_arr[$row[csf("item_group_id")]]; ?></p></td>
                        <td><p><? echo $row[csf("product_name_details")]; ?></p></td>
                   </tr>
              		<? 
					$i++; 
				}
			} 
			?>
            </tbody>
        </table> 
   		<?	
	}
}


if($action=="populate_independent_data")
{ 
	 $data=explode("_",$data);
	 $trans_id=$data[0];
	 $mst_id=$data[1];
	 $prod_id=$data[1];
	 $item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	 $sql="select a.id, a.prod_id, a.cons_quantity as issue_qty, c.product_name_details, c.item_category_id, c.item_group_id, a.batch_lot
	 from inv_transaction a, inv_issue_master b, product_details_master c 
	 where b.id=$mst_id and b.id=a.mst_id and a.prod_id=c.id and a.transaction_type=2 and b.entry_form=5";
	//echo $sql;
	$item_result=sql_select($sql);
	if(count($item_result)>0)
	{
		?>
		 <table width="430" cellspacing="0" cellpadding="0" border="0" class="rpt_table" rules="all" align="center">
            <thead>
                <tr> 
                	<th width="20">Sl</th>               	 
                    <th width="100">Item Category</th>
                    <th width="40">Prod Id</th>
                    <th width="60">Lot</th>
                    <th width="80">Item Group</th>
                    <th>Item Description</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
          	foreach($item_result as $row)
			{		
			
			    $totalIssued = return_field_value("sum(b.cons_quantity+b.cons_reject_qnty) as to_issue","inv_issue_master a, inv_transaction b"," a.id=b.mst_id and b.prod_id='".$row[csf("prod_id")]."' and b.item_category  in(5,6,7,23) and b.transaction_type=2 group by prod_id","to_issue");
	            $totalIssuedReturn = return_field_value("sum(b.cons_quantity+b.cons_reject_qnty) as to_issue_return","inv_receive_master a, inv_transaction b"," a.id=b.mst_id and b.prod_id='".$row[csf("prod_id")]."' and b.item_category  in(5,6,7,23) and b.transaction_type=4 group by prod_id","to_issue_return");
	            $issue_remain=$totalIssued-$totalIssuedReturn;	
				if($issue_remain>0)
				{	
					if($i%2==0) $bgcolor="#E9F3FF";
					else   $bgcolor="#FFFFFF";
					?>
                 	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("prod_id")]."_".$row[csf("id")]."_".$mst_id;?>","child_form_item_data","requires/chemical_dyes_issue_return_controller")'  style="cursor:pointer" >
                        <td align="center"><? echo $i; ?></td>
                        <td><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
                        <td align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
                        <td align="center"><p><? echo $row[csf("batch_lot")]; ?></p></td>
                        <td><p><? echo $item_group_arr[$row[csf("item_group_id")]]; ?></p></td>
                        <td><p><? echo $row[csf("product_name_details")]; ?></p></td>
                        
                   </tr>
              		<? 
					$i++; 
				}
			} 
			?>
            </tbody>
        </table> 
   		<?	
	}
}

if($action=="child_form_item_data")
{
    $data=explode("_",$data);
	$prod_id=$data[0];
	$trans_id=$data[1];
	$mst_id=$data[2];
	$sql = "select c.id as prod_id, c.product_name_details as pro_description, c.unit_of_measure, c.item_group_id, b.company_id, b.item_category, b.cons_quantity, b.cons_rate, b.cons_amount, a.issue_number, a.challan_no, b.store_id, b.floor_id, b.room, b.rack, b.self, b.batch_lot 
	from inv_issue_master a, inv_transaction b, product_details_master c
	where b.prod_id=c.id and c.id=$prod_id and b.id=$trans_id and a.id=$mst_id and a.id=b.mst_id and b.transaction_type=2";	
	$res = sql_select($sql);
	foreach($res as $row)
	{
		if($db_type==0)
		{
			$totalIssuedReturn = return_field_value("sum(b.cons_quantity+ IFNULL(b.cons_reject_qnty, 0) ) as to_issue_return","inv_transaction b","b.prod_id='".$row[csf("prod_id")]."' and b.item_category  in(5,6,7,23) and b.transaction_type=4 and b.issue_id=$mst_id and b.status_active=1","to_issue_return");
		}
		else
		{
			$totalIssuedReturn = return_field_value("sum(b.cons_quantity+ NVL(b.cons_reject_qnty, 0) ) as to_issue_return","inv_transaction b","b.prod_id='".$row[csf("prod_id")]."' and b.item_category  in(5,6,7,23) and b.transaction_type=4 and b.issue_id=$mst_id and b.status_active=1","to_issue_return");
		}
	    
	    $net_used=$row[csf("cons_quantity")]-$totalIssuedReturn;
		echo "$('#total_issue').val(".$net_used.");\n";
		echo "$('#txt_item_description').val('".$row[csf("pro_description")]."');\n";
		echo "$('#cbo_uom').val(".$row[csf("unit_of_measure")].");\n";
		echo "$('#cbo_item_category').val(".$row[csf("item_category")].");\n";
		echo "$('#txt_return_qnty').val(".$totalIssuedReturn.");\n";
		echo "$('#txt_prod_id').val(".$row[csf("prod_id")].");\n";
		echo "$('#new_prod_id').val(".$row[csf("prod_id")].");\n";
		echo "$('#txt_lot').val('".$row[csf("batch_lot")]."');\n";
		
		
		
	    echo "$('#hidden_issue_id').val('".$mst_id."');\n";
		echo "$('#txt_amount_qnty').val('".$row[csf("cons_amount")]."');\n";
		echo "$('#txt_rate').val('".$row[csf("cons_rate")]."');\n";
		echo "$('#txt_issue_challan').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_issue_qty').val(".$row[csf("cons_quantity")].");\n";
		echo "$('#txt_net_used').val(".$net_used.");\n";
		echo "$('#txt_issue_id').val('".$row[csf("issue_number")]."');\n";
		/*echo "load_room_rack_self_bin('requires/chemical_dyes_issue_return_controller*5_6_7_23', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_issue_return_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_issue_return_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_issue_return_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_issue_return_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";	
		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";	*/
   	}	
	exit();
}


if($action=="populate_data_lib_data")
{
	$sql = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $data and variable_list = 29 and is_deleted = 0 and status_active = 1");
	echo $sql[0][csf("auto_transfer_rcv")];
	exit();
}


//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$variable_lot=str_replace("'","",$variable_lot);
	$txt_rate=str_replace("'","",$txt_rate);
	$txt_return_qnty = str_replace("'","",$txt_return_qnty);
	
	
	$update_cond="";
	if( $operation==1  || $operation==2) 
	{
		$update_cond .= " and id <> $hidden_trans_id ";
	}
	$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id and store_id =$cbo_store_name $update_cond and status_active=1 and is_deleted = 0", "max_date");      
	if($max_issue_date !="" && $operation!=2)
	{
		$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
		$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_return_date)));

		if ($receive_date < $max_issue_date) 
		{
			echo "20**Return Date Can not Be Less Than Last Issue Date Of This Lot";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
	}
	
	//######### this stock item store level and calculate rate ########//
	$update_conds="";
	if(str_replace("'","",$hidden_trans_id)>0) $update_conds=" and id <> $hidden_trans_id";
	$store_stock_sql="select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as BALANCE_STOCK, sum((case when transaction_type in(1,4,5) then store_amount else 0 end)-(case when transaction_type in(2,3,6) then store_amount else 0 end)) as BALANCE_AMT 
	from inv_transaction 
	where status_active=1 and prod_id=$txt_prod_id and store_id=$cbo_store_name $update_conds";
	//echo "20**$store_stock_sql";disconnect($con);die;
	$store_stock_sql_result=sql_select($store_stock_sql);
	$store_item_rate=0;
	if($store_stock_sql_result[0]["BALANCE_AMT"]!=0 && $store_stock_sql_result[0]["BALANCE_STOCK"]!=0)
	{
		$store_item_rate=$store_stock_sql_result[0]["BALANCE_AMT"]/$store_stock_sql_result[0]["BALANCE_STOCK"];
	}
	else
	{
		$store_item_rate=$txt_rate;
	}
	$issue_store_value=$store_item_rate*$txt_return_qnty;
        
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//---------------Check Duplicate product in Same return number ------------------------//
		//[str_replace("'","",$txt_lot)]
		if(str_replace("'","",$txt_lot) !="" && $variable_lot==1) $lot_cond=" and b.batch_lot='".str_replace("'","",$txt_lot)."'";
		$duplicate = is_duplicate_field("b.id","inv_receive_master a, inv_transaction b","a.id=b.mst_id and a.recv_number=$txt_return_no and b.prod_id=$txt_prod_id and b.transaction_type=4 $lot_cond"); 
		if($duplicate==1) 
		{
			echo "20**Duplicate Product is Not Allow in Same Return Number.";
			disconnect($con);die;
		}
		//------------------------------Check Brand END---------------------------------------//
		
		//adjust product master table START-------------------------------------//
 		
		$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value from product_details_master where id=$txt_prod_id");
		$presentStock=$presentStockValue=$presentAvgRate=$available_qnty=0;
 		foreach($sql as $result)
		{
			$presentStock			=$result[csf("current_stock")];
			$presentStockValue		=$result[csf("stock_value")];
			$presentAvgRate			=$result[csf("avg_rate_per_unit")];	
		}
		
		$txt_return_value=$txt_return_qnty*$presentAvgRate;
		$mrr_rate_wise_value=$txt_return_qnty*$txt_rate;
		$nowStock 		= $presentStock+$txt_return_qnty;
		$nowStockValue=0;
		$avg_rate_perunit=$presentAvgRate;
		if ($nowStock != 0){
			$nowStockValue 	= $presentStockValue+$txt_return_value;
			$avg_rate_perunit=$nowStockValue/$nowStock;
		}

		$field_array="last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
		$data_array="".$txt_return_qnty."*".$nowStock."*".number_format($nowStockValue,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";
		
		
		// --Store Wise Stock----//
		if(str_replace("'","",$txt_lot) !="" && $variable_lot==1) $store_lot_cond=" and lot='".str_replace("'","",$txt_lot)."'";
		$sql_store = sql_select("select rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_qty_dtls where prod_id=$txt_prod_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id $store_lot_cond");
		$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
		
		foreach($sql_store as $result)
		{
			$store_presentStock	=$result[csf("current_stock")];
			$store_presentStockValue =$result[csf("stock_value")];
			$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
		}
		//echo "10**$txt_return_qnty=$txt_rate";die;
		$store_nowStock 		= $store_presentStock+$txt_return_qnty;
		$store_nowStockValue 	= $store_presentStockValue+$issue_store_value;
		$store_avg_rate_perunit=0;
		if($store_nowStockValue !=0 && $store_nowStock !=0) $store_avg_rate_perunit=$store_nowStockValue/$store_nowStock;
			
		
		//yarn master table entry here START---------------------------------------//	
	    
		if(str_replace("'","",$txt_return_no)=="")
		{
			if($db_type==2 || $db_type==1) { $year_id=" extract(year from insert_date)="; }
		    if($db_type==0)  { $year_id="YEAR(insert_date)="; }
			
			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'DCIRN',29,date("Y",time()),0 ));

			$field_array1="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form,receive_basis, company_id,receive_date,challan_no, store_id,floor,room,rack,shelf, location_id,batch_id, exchange_rate, currency_id,remarks, inserted_by,insert_date";
			$data_array1="(".$id.",'".$new_recv_number[1]."','".$new_recv_number[2]."','".$new_recv_number[0]."',29,".$cbo_issue_basis.",".$cbo_company_id.",".$txt_return_date.",".$txt_return_challan_no.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_location.",".$txt_batch_id.",1,1,".$txt_remarks.",'".$user_id."','".$pc_date_time."')";
		}
		else
		{
			$new_recv_number[0] = str_replace("'","",$txt_return_no);
			$id=str_replace("'","",$update_id);		
			$field_array1="company_id*receive_date*challan_no*store_id*floor*room*rack*shelf*location_id*exchange_rate*currency_id*remarks*updated_by*update_date";
			$data_array1="".$cbo_company_id."*".$txt_return_date."*".$txt_return_challan_no."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_location."*1*1*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'";

		}
		//transaction table insert here START--------------------------------//
		if($txt_reject_qnty=="") $txt_reject_qnty=0;

		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con); 
						
		$field_array2 = "id,mst_id,company_id,prod_id,item_category,batch_lot,transaction_type,transaction_date,store_id,floor_id,room, rack,self,cons_uom,cons_quantity,cons_reject_qnty,cons_rate,cons_amount,balance_qnty,balance_amount,issue_challan_no,issue_id,rcv_rate,rcv_amount,remarks,inserted_by,insert_date,store_rate,store_amount";
 		$data_array2 = "(".$transactionID.",".$id.",".$cbo_company_id.",".$txt_prod_id.",".$cbo_item_category.",'".str_replace("'","",$txt_lot)."',4,".$txt_return_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_uom.",".$txt_return_qnty.",".$txt_reject_qnty.",".number_format($presentAvgRate,10,'.','').",".number_format($txt_return_value,8,'.','').",".$txt_return_qnty.",".number_format($txt_return_value,8,'.','').",".$txt_return_challan_no.",".$hidden_issue_id.",".number_format($txt_rate,10,'.','').",".number_format($mrr_rate_wise_value,8,'.','').",".$txt_remarks.",'".$user_id."','".$pc_date_time."',".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";
		//echo "insert into inv_transaction ($field_array2)values".$data_array2;	
        	
		
		$cbo_company_id = str_replace("'","",$cbo_company_id);
		$cbo_store_name = str_replace("'","",$cbo_store_name);
		$item_category_id = str_replace("'","",$cbo_item_category);
		$txt_product_id = str_replace("'","",$txt_prod_id);
		if($variable_lot==1) $dyes_lot=str_replace("'","",$txt_lot); else $dyes_lot="";
		$stock_arr=fnc_store_wise_qty_operation($cbo_company_id,$cbo_store_name,$item_category_id,$txt_product_id,4,$dyes_lot);
		//print_r($stock_arr);

		$store_update_id=$stock_arr[$cbo_company_id][$txt_product_id][$cbo_store_name][$item_category_id][$dyes_lot];
		$field_array_store_up="last_purchased_qnty*cons_qty*rate*amount*updated_by*update_date";
		$field_array_store_insert="id,company_id,store_id,floor,room,rack,shelf,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot"; 
		$data_array_store_up="".$txt_return_qnty."*".$store_nowStock."*".number_format($store_avg_rate_perunit,10,'.','')."*".number_format($store_nowStockValue,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";
		
		//$sdtlsid = return_next_id("id", "inv_store_wise_qty_dtls", 1);
		$sdtlsid = return_next_id_by_sequence("INV_STORE_WISE_QTY_DTLS_PK_SEQ", "inv_store_wise_qty_dtls", $con); 
		$data_array_store_insert= "(".$sdtlsid.",".$cbo_company_id.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$item_category_id.",".$txt_product_id.",".$txt_return_qnty.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').",".$txt_return_qnty.",'".$user_id."','".$pc_date_time."','".$dyes_lot."')";
		
		//echo "10** insert into inv_transaction ($field_array2) values  $data_array2";die;
		
		$prodUpdate = sql_update("product_details_master",$field_array,$data_array,"id",$txt_prod_id,1);
	
		if(str_replace("'","",$txt_return_no)=="")
		{
			 $rID=sql_insert("inv_receive_master",$field_array1,$data_array1,1); 
		}
		else
		{
		    $rID = sql_update("inv_receive_master",$field_array1,$data_array1,"id",$id,1);	
		}
		
		$dtlsrID = sql_insert("inv_transaction",$field_array2,$data_array2,1);
		
		
		if($store_update_id!='')
		{
			$storeUpdate = sql_update("inv_store_wise_qty_dtls",$field_array_store_up,$data_array_store_up,"id",$store_update_id,1);
		}
		else
		{
			$storeUpdate = sql_insert("inv_store_wise_qty_dtls",$field_array_store_insert,$data_array_store_insert,1); 
		}
		
		//echo "10**".$prodUpdate.'='.$rID.'='.$dtlsrID.'='.$dtlsrID.'='.$store_update_id;die;
		
		if($db_type==0)
		{
			if( $prodUpdate && $rID && $dtlsrID && $storeUpdate)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_recv_number[0]."**".$id."**".$txt_prod_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_recv_number[0];
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if( $prodUpdate && $rID && $dtlsrID && $storeUpdate)
			{
				oci_commit($con);  
				echo "0**".$new_recv_number[0]."**".$id."**".$txt_prod_id;
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
		$flag=0;
		$sql = sql_select("select a.cons_quantity, a.cons_amount, a.store_amount, b.current_stock, b.stock_value, b.avg_rate_per_unit from inv_transaction a, product_details_master b where a.id=$hidden_trans_id and a.prod_id=b.id");
		$beforeReturnQnty=$beforeReturnValue=0;
		$currentStockQnty=$currentStockValue=$before_available_qnty=0;
		foreach($sql as $result)
		{
			//current stock
			$currentStockQnty		=$result[csf("current_stock")];
			$currentStockValue		=$result[csf("stock_value")];
			$cu_avg_rate_per_unit	=$result[csf("avg_rate_per_unit")];
			//before return qnty
			$beforeReturnQnty		=$result[csf("cons_quantity")];
			$beforeReturnValue		=$result[csf("cons_amount")];
			$beforeStoreReturnValue	=$result[csf("store_amount")];
		}
		$txt_return_value=$cu_avg_rate_per_unit*$txt_return_qnty;
		
		$issue_id_check=return_field_value("id as id","inv_mrr_wise_issue_details","recv_trans_id=$hidden_trans_id and is_deleted=0 and status_active=1","id");
		if($issue_id_check!="")
		{
			echo "20**Update Not Allow, This Item Found In Issue Entry.";disconnect($con);die;
		}
		
		//--------------Store Wise Stock------------------//
		if(str_replace("'","",$txt_lot) !="" && $variable_lot==1) $store_lot_cond=" and lot='".str_replace("'","",$txt_lot)."'";
		$sql_store = sql_select("select rate as avg_rate_per_unit,cons_qty as current_stock,amount as stock_value,last_purchased_qnty from inv_store_wise_qty_dtls where prod_id=$txt_prod_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id $store_lot_cond");
		$store_presentStock=$store_presentStockValue=$store_presentAvgRate=$store_before_receive_qnty=0;
		
		foreach($sql_store as $result)
		{
			$store_presentStock	=$result[csf("current_stock")];
			$store_presentStockValue =$result[csf("stock_value")];
			$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
			//$beforeStock =$result[csf("last_purchased_qnty")];
			$store_before_receive_qnty = $result[csf("last_purchased_qnty")]; //stock qnty
		}
		$adj_beforeStock_store			=$store_presentStock-$beforeReturnQnty;
	    $adj_beforeStockValue_store		=$store_presentStockValue-$beforeStoreReturnValue;
		
		$adj_beforeAvgRate_Store=0;
		if($adj_beforeStockValue_store!=0 && $adj_beforeStock_store!=0) $adj_beforeAvgRate_Store =abs($adj_beforeStockValue_store/$adj_beforeStock_store);
		
		//adjust product master table START-------------------------------------//
		$txt_prod_id = str_replace("'","",$txt_prod_id);
		$update_array="last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
		$update_data = $updateID_array = array();
		if(str_replace("'","",$txt_prod_id) == str_replace("'","",$before_prod_id))
		{
			$presentStockQnty   = $currentStockQnty-$beforeReturnQnty+$txt_return_qnty; //current qnty - before qnty + present return qnty
			$presentStockValue  = $currentStockValue-$beforeReturnValue+$txt_return_value;
			$present_avgrate=$cu_avg_rate_per_unit;
		
			$data_array_prod	= "".$txt_return_qnty."*".$presentStockQnty."*".number_format($presentStockValue,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";		
		}
		else
		{
			//before
			$presentStockQnty   = $currentStockQnty-$beforeReturnQnty; //current qnty - before qnty
			$presentStockValue  = $currentStockValue-$txt_return_value; 
			$update_data[$before_prod_id]=explode("*",("".$txt_return_qnty."*".$presentStockQnty."*".number_format($presentStockValue,8,'.','')."*'".$user_id."'*'".$pc_date_time."'"));
			$updateID_array[]=$before_prod_id;
		
			$sql = sql_select("select current_stock,stock_value,avg_rate_per_unit from product_details_master  where id=$txt_prod_id");
			foreach($sql as $result)
			{
				$currentStockQntyAfter		=$result[csf("current_stock")];
				$currentStockValueAfter		=$result[csf("stock_value")];
				$currentAvarageRateAfter	=$result[csf("avg_rate_per_unit")];
			}
			$txt_return_value=$txt_return_qnty*$currentAvarageRateAfter;
			$presentStockQntyAfter   = $currentStockQntyAfter+$txt_return_qnty; //current qnty + present return qnty
			$presentStockValueAfter  = $currentStockValueAfter+$txt_return_value;

			$update_data[$txt_prod_id]=explode("*",("".$txt_return_qnty."*".$presentStockQntyAfter."*".number_format($presentStockValueAfter,8,'.','')."*'".$user_id."'*'".$pc_date_time."'"));
			$updateID_array[]=$txt_prod_id;		
		
		}
		
		
		//adjust product master table END  -------------------------------------//
		$field_array="receive_basis*receive_date*challan_no*location_id*updated_by*update_date";
		$data_array="".$cbo_issue_basis."*".$txt_return_date."*".$txt_return_challan_no."*".$cbo_location."*'".$user_id."'*'".$pc_date_time."'";
		
		$mrr_rate_wise_value=$txt_rate*$txt_return_qnty;
		if($txt_reject_qnty=="")$txt_reject_qnty=0;
		$txt_amount=$txt_return_qnty*$cu_avg_rate_per_unit;
		$field_array1 = "company_id*prod_id*item_category*batch_lot*transaction_type*transaction_date*store_id*floor_id*room*rack*self*cons_uom*cons_quantity*cons_reject_qnty*cons_amount*balance_qnty*balance_amount*issue_id*rcv_rate*rcv_amount*remarks*updated_by*update_date*store_rate*store_amount";
		$data_array1 = "".$cbo_company_id."*".$txt_prod_id."*".$cbo_item_category."*'".str_replace("'","",$txt_lot)."'*4*".$txt_return_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_uom."*".$txt_return_qnty."*".$txt_reject_qnty."*".number_format($txt_amount,8,'.','')."*".$txt_return_qnty."*".number_format($txt_amount,8,'.','')."*".$hidden_issue_id."*".number_format($txt_rate,10,'.','')."*".number_format($mrr_rate_wise_value,8,'.','')."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','')."";
			
		
		
		//---Store Wise Stock---//
		$cbo_company_id = str_replace("'","",$cbo_company_id);
		$cbo_store_name = str_replace("'","",$cbo_store_name);
		$item_category_id = str_replace("'","",$cbo_item_category);
		$txt_product_id = str_replace("'","",$txt_prod_id);
		if($variable_lot==1) $dyes_lot=str_replace("'","",$txt_lot); else $dyes_lot="";
		$stock_arr=fnc_store_wise_qty_operation($cbo_company_id,$cbo_store_name,$item_category_id,$txt_product_id,4,$dyes_lot);
		$store_update_id=$stock_arr[$cbo_company_id][$txt_product_id][$cbo_store_name][$item_category_id][$dyes_lot];
		//print_r($stock_arr);
		
		if(str_replace("'","",$txt_prod_id) == str_replace("'","",$before_prod_id))
		{
			
			$store_presentStockQnty   = $store_presentStock-$beforeReturnQnty+$txt_return_qnty; //current qnty - before qnty + present return qnty
			$store_presentStockValue  = $store_presentStockValue-$beforeStoreReturnValue+$issue_store_value; 
			$store_present_avgrate=0; 
			if($store_presentStockValue !=0 && $store_presentStockQnty !=0) $store_present_avgrate=$store_presentStockValue/$store_presentStockQnty;
			$data_array_store_up= "".$txt_return_qnty."*".$store_presentStockQnty."*".number_format($store_present_avgrate,10,'.','')."*".number_format($store_presentStockValue,10,'.','')."*'".$user_id."'*'".$pc_date_time."'";
			
		}
		else
		{
			$supdateID_array[]=$store_update_id;
			$store_presentStockQnty   = $store_presentStock-$beforeReturnQnty+$txt_return_qnty; //current qnty - before qnty + present return qnty
			$store_presentStockValue  = $store_presentStockValue-$beforeStoreReturnValue+$issue_store_value; 
			$store_present_avgrate=0; 
			if($store_presentStockValue !=0 && $store_presentStockQnty !=0) $store_present_avgrate=$store_presentStockValue/$store_presentStockQnty;
			$data_array_store_up[$store_update_id]=explode("*",("".$txt_return_qnty."*".$store_presentStockQnty."*".number_format($store_present_avgrate,10,'.','')."*".number_format($store_presentStockValue,8,'.','')."*'".$user_id."'*'".$pc_date_time."'"));
		}
 		$field_array_store_up="last_purchased_qnty*cons_qty*rate*amount*updated_by*update_date";
		$field_array_store_insert="id,company_id,store_id,floor,room,rack,shelf,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot"; 
		//$sdtlsid = return_next_id("id", "inv_store_wise_qty_dtls", 1);
		$sdtlsid = return_next_id_by_sequence("INV_STORE_WISE_QTY_DTLS_PK_SEQ", "inv_store_wise_qty_dtls", $con); 
		$data_array_store_insert= "(".$sdtlsid.",".$cbo_company_id.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$item_category_id.",".$txt_product_id.",".$txt_return_qnty.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').",".$txt_return_qnty.",'".$user_id."','".$pc_date_time."','".$dyes_lot."')";
		
		//print_r($data_array_store_up);
		//echo "10**".$store_update_id;
		$prodUpdate=$rID=$transID=$storeupdate=true;
		if(str_replace("'","",$txt_prod_id) == str_replace("'","",$before_prod_id))
		{
			$prodUpdate = sql_update("product_details_master",$update_array,$data_array_prod,"id",$txt_prod_id,0);
		}
		else
		{
			$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array,$update_data,$updateID_array),0);
		}
		$rID=sql_update("inv_receive_master",$field_array,$data_array,"id",$update_id,1);	
		$transID = sql_update("inv_transaction",$field_array1,$data_array1,"id",$hidden_trans_id,1); 
		
		if($store_update_id!='')
		{
			if(str_replace("'","",$txt_prod_id) == str_replace("'","",$before_prod_id))
			{
				$storeupdate = sql_update("inv_store_wise_qty_dtls",$field_array_store_up,$data_array_store_up,"id",$store_update_id,1);
			}
			else
			{
				$storeupdate=execute_query(bulk_update_sql_statement("inv_store_wise_qty_dtls","id",$field_array_store_up,$data_array_store_up,$supdateID_array),0);
			}
		}
		else
		{
			$storeupdate = sql_insert("inv_store_wise_qty_dtls",$field_array_store_insert,$data_array_store_insert,1); 
		}
		
		//echo "10**".$rID.'='.$transID.'='.$prodUpdate.'='.$storeupdate;die;
        if($db_type==0)
		{
			if($prodUpdate && $rID && $transID && $storeupdate)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_return_no)."**".str_replace("'","",$update_id)."**".$txt_prod_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_return_no)."**".str_replace("'","",$update_id)."**".$txt_prod_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
		
			if($prodUpdate && $rID && $transID && $storeupdate)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_return_no)."**".str_replace("'","",$update_id)."**".$txt_prod_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_return_no)."**".str_replace("'","",$update_id)."**".$txt_prod_id;
			}
		}
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$txt_product_id = str_replace("'","",$txt_prod_id);
		$cbo_company_id = str_replace("'","",$cbo_company_id);
		$cbo_store_name = str_replace("'","",$cbo_store_name);
		$item_category_id = str_replace("'","",$cbo_item_category);
		
		$issue_id_check=return_field_value("id as id","inv_mrr_wise_issue_details","recv_trans_id=$hidden_trans_id and is_deleted=0 and status_active=1","id");
		if($issue_id_check!="")
		{
			echo "20**Update Not Allow, This Item Found In Issue Entry.";disconnect($con);die;
		}
		
		$sql = sql_select("select a.cons_quantity, a.cons_amount, a.store_amount, b.current_stock, b.stock_value, b.avg_rate_per_unit from inv_transaction a, product_details_master b where a.id=$hidden_trans_id and a.prod_id=b.id");
		$beforeReturnQnty=$beforeReturnValue=0;
		$currentStockQnty=$currentStockValue=$before_available_qnty=0;
		foreach($sql as $result)
		{
			//current stock
			$currentStockQnty		=$result[csf("current_stock")];
			$currentStockValue		=$result[csf("stock_value")];
			$cu_avg_rate_per_unit	=$result[csf("avg_rate_per_unit")];
			//before return qnty
			$beforeReturnQnty		=$result[csf("cons_quantity")];
			$beforeReturnValue		=$result[csf("cons_amount")];
			$beforeStoreAmount		= $row[csf("store_amount")];
			
		}
		
		//adjust product master table START-------------------------------------//
		$txt_prod_id = str_replace("'","",$txt_prod_id);
		$update_array="last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
		$presentStockQnty   = $currentStockQnty-$beforeReturnQnty; //current qnty - before qnty + present return qnty
		$presentStockValue  = $currentStockValue-$beforeReturnValue; 
		$data_array_prod	= "".$txt_return_qnty."*".$presentStockQnty."*".number_format($presentStockValue,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";
		
		//--------------Store Wise Stock------------------//
		if(str_replace("'","",$txt_lot) !="" && $variable_lot==1) $store_lot_cond=" and lot='".str_replace("'","",$txt_lot)."'";
		$sql_store = sql_select("select rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value, last_purchased_qnty from inv_store_wise_qty_dtls where prod_id=$txt_prod_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id $store_lot_cond");
		$store_presentStock=$store_presentStockValue=$store_presentAvgRate=$store_before_receive_qnty=0;
		foreach($sql_store as $result)
		{
			$store_presentStock	=$result[csf("current_stock")];
			$store_presentStockValue =$result[csf("stock_value")];
		}
		
		$adj_beforeStock_store			=$store_presentStock-$beforeReturnQnty;
	    $adj_beforeStockValue_store		=$store_presentStockValue-$beforeStoreAmount;
		$adj_beforeAvgRate_Store=0;
		if($adj_beforeStockValue_store!=0 && $adj_beforeStock_store!=0) $adj_beforeAvgRate_Store =abs($adj_beforeStockValue_store/$adj_beforeStock_store);
		
		//---Store Wise Stock---//
		if($variable_lot==1) $dyes_lot=str_replace("'","",$txt_lot); else $dyes_lot="";
		$stock_arr=fnc_store_wise_qty_operation($cbo_company_id,$cbo_store_name,$item_category_id,$txt_product_id,4,$dyes_lot);
		$store_update_id=$stock_arr[$cbo_company_id][$txt_product_id][$cbo_store_name][$item_category_id][$dyes_lot];
		
		$field_array_store_up="last_purchased_qnty*cons_qty*rate*amount*updated_by*update_date";
		$data_array_store_up = "".$txt_return_qnty."*".$adj_beforeStock_store."*".number_format($adj_beforeAvgRate_Store,10,'.','')."*".number_format($adj_beforeStockValue_store,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";
		
		$field_array1="updated_by*update_date*status_active*is_deleted";
		$data_array1="".$user_id."*'".$pc_date_time."'*0*1";
		
		$transID=$prodUpdate=$storeupdate=true;
		$prodUpdate = sql_update("product_details_master",$update_array,$data_array_prod,"id",$txt_prod_id,0);
		$transID = sql_update("inv_transaction",$field_array1,$data_array1,"id",$hidden_trans_id,1); 
		if($store_update_id!='')
		{
			$storeupdate = sql_update("inv_store_wise_qty_dtls",$field_array_store_up,$data_array_store_up,"id",$store_update_id,1); 
		}
		
		//echo "10**".$transID.'='.$prodUpdate.'='.$storeupdate;die;
        if($db_type==0)
		{
			if($transID && $prodUpdate && $storeupdate)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_return_no)."**".$update_id."**".$txt_prod_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_return_no)."**".$update_id."**".$txt_prod_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($transID && $prodUpdate && $storeupdate)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_return_no)."**".$update_id."**".$txt_prod_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_return_no)."**".$update_id."**".$txt_prod_id;
			}
		}
		disconnect($con);
		die;
 	}
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
	<table width="880" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="150">Search By</th>
                    <th width="250" align="center" id="search_by_td_up">Enter Return No</th>
                    <th width="200">Issue Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?  
                            $search_by = array(1=>'Return No',2=>'Challan No');
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td width="" align="center" id="search_by_td">				
                        <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_return_search_list_view', 'search_div', 'chemical_dyes_issue_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-------->
                     <input type="hidden" id="hidden_return_number" value="" />
                    <!-- ---------END------------->
                </td>
            </tr>    
            </tbody>
         </tr>         
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
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$company = $ex_data[4];
	$sql_cond="";
	
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for mrr
		{
			$sql_cond .= " and a.recv_number LIKE '%$txt_search_common%'";	
			
		}
		else if(trim($txt_search_by)==2) // for chllan no
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";				
 		}		 
 	}
	
	if( $fromDate!="" || $toDate!="" ) 
	{ 
		if($db_type==0)
		{
		 $sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		if($db_type==2 || $db_type==1)
		{ 
			$sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'yyyy-mm-dd',"-",1)."' and '".change_date_format($toDate,'yyyy-mm-dd',"-",1)."'"; 
		}
	}
	$batch_arr = return_library_array("select id,batch_no from pro_batch_create_mst where company_id=$company and batch_against<>0 ","id","batch_no");
	$req_arr=return_library_array( "select id, requ_no from  dyes_chem_issue_requ_mst",'id','requ_no');
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	if($db_type==2 ) { $year_id=" extract(year from a.insert_date) as year"; }
	if($db_type==0)  { $year_id="YEAR(a.insert_date) as year"; }
	if($db_type==0){ $nulvalue="IFNULL";}
	if($db_type==1  || $db_type==2 ){ $nulvalue="nvl";}
	
	$sql = "select distinct a.id as mst_id,a.recv_number,a.recv_number_prefix_num,a.receive_basis,a.company_id,a.receive_date,a.challan_no,$year_id
	,a.batch_id as batch
			 from inv_receive_master a
			where a.entry_form=29 and a.company_id=$company $sql_cond and a.status_active=1 and a.is_deleted=0 order by a.id DESC"; 
 //echo $sql;
	//$arr=array(3=>$location_arr);
   ?>
    
       <table class="rpt_table" border="1"  cellspacing="0" rules="all" style="width:800px" >
        	 <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="100" >Return No</th>
                <th width="60" >Year</th>
                <th width="150" >Company</th>
                <th width="150" >Issue Basis</th>
                <th width="150" >Batch No</th>
                <th width="120" >Return Date</th> 
                <th width="100" >Challan No</th> 
            </thead>
        </table>
   <div style="width:800px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden; cursor:pointer;" id="scroll_body">
        <table class="rpt_table" border="1"  cellspacing="0" rules="all" style="width:800px">
            <tbody>
            
            	<? 
				$result=sql_select($sql);
				$i=1;
				foreach($result as $row)
				{					
					if($i%2==0)
						$bgcolor="#E9F3FF";
					else 
						$bgcolor="#FFFFFF";
					if($row[csf("receive_basis")]==5){ $batch_no=$batch_arr[$row[csf("batch")]];}
					if($row[csf("receive_basis")]==7) { $batch_no=$req_arr[$row[csf("batch")]];}
					
 				    ?>
                 	<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf("recv_number")]."**".$row[csf("batch")]."**".$row[csf("receive_basis")]."**".$row[csf("mst_id")];?>","","")' style="cursor:pointer" >
                        <td width="30"><? echo $i; ?></td>
                        <td width="100"><p><? echo $row[csf("recv_number_prefix_num")]; ?></p></td>
                        <td width="60"><p><? echo $row[csf("year")]; ?></p></td>
                        <td width="150" align="center"><p><? echo $company_arr[$row[csf("company_id")]]; ?></p></td>
                        <td width="150" align="center"><p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?></p></td>
                        
                        <td width="150" align="center"><p><? echo $batch_no; ?></p></td>
                        <td width="120" align="center"><p><? echo change_date_format($row[csf("receive_date")]); ?></p></td>
                        <td width="100"><p><? echo $row[csf("challan_no")]; ?></p></td>

                   </tr>
                <? 
				   $i++;
				} 
				
				?>
              
            </tbody>
        </table>
   </div>
 <?
}

//for update

if($action=="populate_batch_no_data_update")
{ 
     
	 $data=explode("**",$data);
	 $company_name=$data[0];
	 $receive_id=$data[1];
	 $hidden_posted_account=$data[2];
	 if($db_type==0) $group_tr=" group_concat(b.prod_id) as prod_id";
	 if($db_type==2) $group_tr=" listagg(b.prod_id,',') within group ( order by b.id) as prod_id";
   	 $issue_sql=sql_select("select b.issue_id,$group_tr from inv_transaction b,inv_receive_master a where a.id=b.mst_id and b.transaction_type=4 and a.id='$receive_id' and a.company_id=$company_name and a.entry_form=29 group by b.issue_id ");
 
	 $sql="select  a.id,a.prod_id,a.cons_quantity as issue_qty ,c.product_name_details,c.item_category_id ,c.item_group_id	from  inv_transaction a, inv_issue_master b,product_details_master c where b.id=".$issue_sql[0][csf("issue_id")]." and a.prod_id not in (".$issue_sql[0][csf("prod_id")].") and b.id=a.mst_id and a.prod_id=c.id and  a.transaction_type=2 and b.entry_form=5 and b.company_id=$company_name ";

	//echo $sql;
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$item_result=sql_select($sql);
	if(count($item_result)>0)
	{
	?>
     <table width="400" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr> 
                	<th width="40">Sl</th>               	 
                    <th width="100">Item Category</th>
                    <th width="60">Prod Id</th>
                    <th width="100">Item Group</th>
                    <th width="150">Item Description</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
          	foreach($item_result as $row)
			{		
			
			    $totalIssued = return_field_value("sum(b.cons_quantity+b.cons_reject_qnty) as to_issue","inv_issue_master a, inv_transaction b"," a.id=b.mst_id and b.prod_id='".$row[csf("prod_id")]."' and b.item_category  in(5,6,7,23) and b.transaction_type=2 group by prod_id","to_issue");
	            $totalIssuedReturn = return_field_value("sum(b.cons_quantity+b.cons_reject_qnty) as to_issue_return","inv_receive_master a, inv_transaction b"," a.id=b.mst_id and b.prod_id='".$row[csf("prod_id")]."' and b.item_category  in(5,6,7,23) and b.transaction_type=4 group by prod_id","to_issue_return");
	            $issue_remain=$totalIssued-$totalIssuedReturn;	
				if($issue_remain>0)
				{	
		        if($i%2==0) $bgcolor="#E9F3FF";
			    else   $bgcolor="#FFFFFF";
				
 				?>
                 	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("prod_id")]."_".$row[csf("id")]."_".$issue_sql[0][csf("issue_id")];?>","child_form_item_data","requires/chemical_dyes_issue_return_controller")'  style="cursor:pointer" >
                        <td  align="center"><? echo $i; ?></td>
                        <td  align="center"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
                        <td  align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
                        <td  align="center"><p><? echo $item_group_arr[$row[csf("item_group_id")]]; ?></p></td>
                        <td  align="center"><p><? echo $row[csf("product_name_details")]; ?></p></td>
                        
                   </tr>
              <? $i++; 
				}
			} ?>
              
            </tbody>
        </table> 

   <?	
	}
}



if($action=="populate_master_from_data")
{
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');  
	$req_arr=return_library_array( "select id, requ_no from  dyes_chem_issue_requ_mst",'id','requ_no');
 	$sql = "select b.id,b.recv_number,b.company_id,b.receive_date,b.challan_no,b.receive_basis,b.location_id,b.batch_id,b.is_posted_account
			from   inv_receive_master b
			where b.recv_number='$data' and b.entry_form=29";
	//echo $sql;
	
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_return_no').val('".$row[csf("recv_number")]."');\n";
 		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_location').val(".$row[csf("location_id")].");\n";
		echo "$('#txt_return_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#txt_return_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#cbo_issue_basis').val('".$row[csf("receive_basis")]."');\n";
		
		echo "$('#hidden_posted_account').val('".$row[csf("is_posted_account")]."');\n";
		
		if($row[csf("receive_basis")]==5)
		{
		echo "$('#txt_batch_name').val('".$batch_arr[$row[csf("batch_id")]]."');\n";
		}
		if($row[csf("receive_basis")]==7)
		{
		echo "$('#txt_batch_name').val('".$req_arr[$row[csf("batch_id")]]."');\n";
		}
		echo "$('#update_id').val(".$row[csf("id")].");\n";
		if($row[csf("is_posted_account")]==1)
		{
			echo "disable_enable_fields( 'cbo_company_id*cbo_issue_basis*txt_return_date*txt_return_challan_no', 1, '', '' );\n"; // disable true
		}
		else
		{
			echo "disable_enable_fields( 'cbo_company_id*cbo_issue_basis', 1, '', '' );\n"; // disable true
			echo "$('#txt_return_date').attr('disabled',false);\n";
			echo "$('#txt_return_challan_no').attr('disabled',false);\n";
		}

		$msg="Already Posted in Accounts";
        if($row[csf("is_posted_account")]==1){
			echo "$('#posted_account_td').text('".$msg."');\n";
		}else{
			
		}
				
   	}	
	exit();	
}

if($action=="show_dtls_list_view")	
{
	$ex_data=explode('**',$data);
	//print_r($ex_data);die;
	$hidden_posted_account=$ex_data[1];
	$return_number=str_replace("'","",$ex_data[0]);

	$cond="";
	//if($return_number!="") $cond .= " and a.id='$return_number'";

	$sql = "select b.id as tr_id, a.location_id, a.recv_number, a.company_id, a.receive_date, a.store_id, a.receive_basis, b.id, b.cons_quantity, b.cons_reject_qnty, b.cons_uom, b.cons_rate, b.cons_amount, c.product_name_details, c.id as prod_id   
	       from  inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id
	       where a.id=b.mst_id and a.id='$return_number' and b.transaction_type=4 and a.entry_form=29 and a.status_active=1 and b.status_active=1 $cond";	
	  //echo $sql; 	
	$result = sql_select($sql);
	$i=1;
	$rettotalQnty=0;
	$rcvtotalQnty=0;
	$totalAmount=0;
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	?> 
     	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:850px" >
        	<thead>
            	<tr>
                	<th>SL</th>
                    <th>Store Name</th>
                    <th>Basis</th>
                    <th>Item Description</th>
                    <th>Location</th>
                    <th>Return Qnty</th>
                    <th>Reject Qnty</th>
                    <th>UOM</th>
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
                 	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("recv_number")]."**".$row[csf("prod_id")]."**".$row[csf("tr_id")]."**".$hidden_posted_account;?>","child_form_input_data_update","requires/chemical_dyes_issue_return_controller")' style="cursor:pointer" >
                        <td width="30"><? echo $i; ?></td>
                        <td width="100"><p><? echo $store_arr[$row[csf("store_id")]]; ?></p></td>
                        <td width="100"><p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?></p></td>
                        <td width="200"><p><? echo $row[csf("product_name_details")]; ?></p></td>
                        <td width="80"><p><? echo $location_arr[$row[csf("location_id")]]; ?></p></td>
                        <td width="80" align="center"><p><? echo $row[csf("cons_quantity")]; ?></p></td>
                        <td width="80" align="center"><p><? echo $row[csf("cons_reject_qnty")]; ?></p></td>
                        <td width="80" align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                   </tr>
                <? $i++; } ?>
              
            </tbody>
        </table>
    <?
	exit();
}

//for update 
if($action=="child_form_input_data_update")
{ 
    $data=explode('**',$data);
	
  	$sql = "select b.id as prod_id, b.product_name_details, a.batch_lot, a.id as tr_id, a.store_id,a.floor_id,a.room,a.rack,a.self, a.cons_uom, a.cons_rate, a.cons_quantity,
	a.cons_reject_qnty, a.cons_amount, a.issue_challan_no,a.remarks,a.item_category,a.machine_id,b.item_group_id,c.receive_basis,c.location_id,
	c.receive_date,c.receive_basis,c.challan_no,c.company_id,c.batch_id,a.issue_id
	from inv_transaction a, product_details_master b, inv_receive_master c
 	where c.recv_number='".$data[0]."' and b.id=".$data[1]." and a.status_active=1 and a.id=".$data[2]."  and c.id=a.mst_id and transaction_type=4 
	and c.entry_form=29 and a.prod_id=b.id and b.status_active in(1,3)";
	$result = sql_select($sql);
	foreach($result as $row)
	{   
		echo "$('#txt_item_description').val('".$row[csf("product_name_details")]."');\n";
		
		echo "load_room_rack_self_bin('requires/chemical_dyes_issue_return_controller*5_6_7_23', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";

		echo "load_room_rack_self_bin('requires/chemical_dyes_issue_return_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_issue_return_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_issue_return_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_issue_return_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";	
		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";

		//echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";
		echo "$('#txt_return_qnty').val('".$row[csf("cons_quantity")]."');\n"; 
		echo "$('#txt_reject_qnty').val('".$row[csf("cons_reject_qnty")]."');\n";
		echo "$('#txt_lot').val('".$row[csf("batch_lot")]."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#cbo_uom').val('".$row[csf("cons_uom")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#before_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#cbo_item_category').val(".$row[csf("item_category")].");\n";
		echo "$('#hidden_trans_id').val(".$row[csf("tr_id")].");\n";
		echo "$('#hidden_issue_id').val(".$row[csf("issue_id")].");\n";
		
		if($db_type==0)
		{
			$totalIssuedReturn = return_field_value("sum(b.cons_quantity+ IFNULL(b.cons_reject_qnty, 0) ) as to_issue_return","inv_transaction b"," b.prod_id='".$row[csf("prod_id")]."' and b.item_category  in(5,6,7,23) and b.transaction_type=4 and b.status_active=1  and b.issue_id=".$row[csf("issue_id")]." and b.id <>".$row[csf("tr_id")]."","to_issue_return");
		}
		else
		{
			$totalIssuedReturn = return_field_value("sum(b.cons_quantity+ NVL(b.cons_reject_qnty, 0) ) as to_issue_return","inv_transaction b"," b.prod_id='".$row[csf("prod_id")]."' and b.item_category  in(5,6,7,23) and b.transaction_type=4 and b.status_active=1  and b.issue_id=".$row[csf("issue_id")]." and b.id <>".$row[csf("tr_id")]."","to_issue_return");
		}
		
		$sql_issue=sql_select("select issue_number,challan_no,b.cons_quantity,b.cons_amount,b.cons_rate from inv_issue_master a, inv_transaction b 
		where a.id=".$row[csf("issue_id")]." and b.transaction_type=2 and a.id=b.mst_id and  b.prod_id=".$row[csf("prod_id")]." and b.status_active=1");
		foreach($sql_issue as $inv)
		{
			$net_used=$inv[csf("cons_quantity")]-$totalIssuedReturn;
			echo "$('#txt_issue_id').val('".$inv[csf("issue_number")]."');\n";
			echo "$('#txt_issue_qty').val('".$inv[csf("cons_quantity")]."');\n";
			echo "$('#txt_issue_challan').val('".$inv[csf("challan_no")]."');\n";
			echo "$('#txt_rate').val(".$inv[csf("cons_rate")].");\n";
			echo "$('#txt_amount_qnty').val(".$inv[csf("cons_amount")].");\n";
		}
		
		echo "$('#txt_return_total').val(".$totalIssuedReturn.");\n";
		echo "$('#txt_net_used').val(".$net_used.");\n";
		if($data[3]==1)
		{
			echo "$('#txt_item_description').attr('disabled',true);\n";
			echo "$('#txt_return_qnty').attr('disabled',true);\n";
			echo "$('#cbo_store_name').attr('disabled',true);\n";
		}
		else
		{
			echo "$('#txt_item_description').attr('disabled',false);\n";
			echo "$('#txt_return_qnty').attr('disabled',false);\n";
			echo "$('#cbo_store_name').attr('disabled',false);\n";
		}
		
		echo "disable_enable_fields('txt_batch_name*txt_item_description*cbo_store_name', 1, '', '' );\n";
		echo "set_button_status(1, permission, 'fnc_chemical_issue_return_entry',1);\n";
		
	}
  	exit();
}

if($action=="child_form_input_data")
{ 
    $data=explode('**',$data);
	
  	$sql = "select b.id as prod_id, b.product_name_details, b.lot, a.id as tr_id, a.store_id, a.cons_uom, a.cons_rate, a.cons_quantity,a.cons_reject_qnty, a.cons_amount,    a.issue_challan_no,a.remarks,a.item_category,a.machine_id,b.item_group_id,c.receive_basis,c.location_id,c.receive_date,c.receive_basis,c.challan_no,c.company_id,c.batch_id
			from inv_transaction a, product_details_master b, inv_receive_master c
 			where c.recv_number='".$data[0]."' and b.id=".$data[1]." and a.status_active=1 and a.id=".$data[3]."  and c.id=a.mst_id and transaction_type=4 and c.entry_form=29 and a.prod_id=b.id and b.status_active in(1,3)";
   //echo $sql;
	$result = sql_select($sql);
	foreach($result as $row)
	   {   
		
			echo "$('#txt_item_description').val('".$row[csf("product_name_details")]."');\n";
			echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";
			echo "$('#txt_return_qnty').val('".$row[csf("cons_quantity")]."');\n"; 
			echo "$('#txt_reject_qnty').val('".$row[csf("cons_reject_qnty")]."');\n";
			echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
			echo "$('#cbo_uom').val('".$row[csf("cons_uom")]."');\n";
			echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
			echo "$('#before_prod_id').val('".$row[csf("prod_id")]."');\n";
			echo "$('#cbo_item_category').val(".$row[csf("item_category")].");\n";
			echo "$('#cbo_item_group').val(".$row[csf("item_group_id")].");\n";
			
    	}
 	
	echo "set_button_status(1, permission, 'fnc_chemical_issue_return_entry',1);\n";		
  	exit();
}


if ($action=="issue_return_print")
{
    extract($_REQUEST);
	//echo $data;
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql=" select id, recv_number, receive_basis, batch_id, knitting_source, knitting_company, challan_no, receive_date from inv_receive_master where id='$data[3]' and entry_form=29";
	
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	if($dataArray[0][csf('receive_basis')]==5) 
	{
		$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
		$batch_req=$batch_arr[$dataArray[0][csf('batch_id')]];
	}
	else if($dataArray[0][csf('receive_basis')]==7)
	{
		$req_arr=return_library_array( "select id, requ_no from  dyes_chem_issue_requ_mst",'id','requ_no');
		$batch_req=$req_arr[$dataArray[0][csf('batch_id')]];
	}
	else $batch_req="";
	
?>
    <div style="width:930px;">
        <table width="900" cellspacing="0" align="right">
            <tr>
                <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">  
                    <?
                        $nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0]"); 
                        foreach ($nameArray as $result)
                        { 
                        ?>
                            Plot No: <? echo $result[csf('plot_no')]; ?> 
                            Level No: <? echo $result[csf('level_no')]?>
                            Road No: <? echo $result[csf('road_no')]; ?> 
                            Block No: <? echo $result[csf('block_no')];?> 
                            City No: <? echo $result[csf('city')];?> 
                            Zip Code: <? echo $result[csf('zip_code')]; ?> 
                            Province No: <? echo $result[csf('province')];?> 
                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                            Email Address: <? echo $result[csf('email')];?> 
                            Website No: <? echo $result[csf('website')];
                        }
                    ?> 
                </td>  
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
            </tr>
            <tr>
                <td width="120"><strong>Return ID:</strong></td><td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
                <td width="130"><strong>Basis:</strong></td> <td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
                <td width="125"><strong>Batch/Requ.:</strong></td><td width="175px"><? echo $batch_req; ?></td>
            </tr>
                <td><strong>Return Date:</strong></td><td><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
                <td><strong>Return Challan:</strong></td> <td><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td><strong>&nbsp;</strong></td><td><? //echo $dataArray[0][csf('challan_no')]; ?></td>
            </tr>
        </table>
     <br>
        <div style="width:100%;">
        <table align="right" cellspacing="0" cellpadding="0" width="1000" border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="40">SL</th>
                <th width="250">Item Description</th>
                <th width="80">Lot</th> 
                <th width="70">UOM</th>
                <th width="80">Returned Qty.</th>
                <th width="80">Reject Qty.</th>
                <th width="120">Store</th>
                <th width="120">Issue Purpose</th>
                <th>Remarks</th>
            </thead>
            <tbody>
        <?
            
            
            $i=1;
            $mst_id=$dataArray[0][csf('id')];
        
            $sql_dtls="Select a.id as pd_id, a.product_name_details, a.lot, b.id, b.cons_uom, b.cons_quantity, b.store_id, b.cons_reject_qnty, b.remarks, d.issue_purpose 
			from product_details_master a, inv_transaction b, inv_issue_master d 
			where a.id=b.prod_id and b.issue_id=d.id and b.transaction_type=4 and b.item_category in (5,6,7,23) and b.mst_id=$data[3] and b.status_active=1 and b.is_deleted=0";
            //echo $sql_dtls;
            $sql_result = sql_select($sql_dtls);	
            foreach($sql_result as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <td><p><? echo $row[csf("product_name_details")]; ?></p></td>
                    <td><? echo $row[csf("lot")]; ?></td>
                    <td><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_quantity")],2,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_reject_qnty")],2,'.',''); ?></td>
                    <td><p><? echo $store_arr[$row[csf("store_id")]]; ?></p></td>
                    <td><p><? echo $general_issue_purpose[$row[csf("issue_purpose")]]; ?></p></td>
                    <td><p><? echo $row[csf("remarks")]; ?></p></td>
                </tr>
                <? 
                $cons_quantity_sum+=$row[csf('cons_quantity')];
				$cons_reject_qnty_sum+=$row[csf('cons_reject_qnty')];
                $i++; 
            } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" align="right">Total :</td>
                    <td align="right"><? echo number_format($cons_quantity_sum,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($cons_reject_qnty_sum,2,'.',''); ?></td>
                    <td colspan="3">&nbsp;</td>
                </tr>                           
            </tfoot>
        </table>
        <br>
         <?
            echo signature_table(89, $data[0], "1000px");
         ?>
        </div>
	</div>
	<?
    exit();			
}

function fnc_store_wise_qty_operation($company_id,$store_id,$category,$prod_id,$trans_type,$dyes_lot)
{
	
	$trans_type=str_replace("'","",$trans_type);
	$prod_id=str_replace("'","",$prod_id);
	$store_id=str_replace("'","",$store_id);
	$category=str_replace("'","",$category);
	$company_id=str_replace("'","",$company_id);
	$dyes_lot=str_replace("'","",$dyes_lot);
	if($trans_type==2) //Issue
	{
		$prod_ids=rtrim($prod_id,",");
		$prod_ids=array_chunk(array_unique(explode(",",$prod_ids)),1000, true);
		 $prod_cond="";
		   $ji=0;
		   foreach($prod_ids as $key=> $value)
		   {
			   if($ji==0)
			   {
				$prod_cond=" and prod_id  in(".implode(",",$value).")"; 
				
			   }
			   else
			   {
				$prod_cond.=" or prod_id  in(".implode(",",$value).")";
				
			   }
			   $ji++;
		   }
		 $category_ids=rtrim($category,",");
		$cat_ids=array_chunk(array_unique(explode(",",$category_ids)),1000, true);
		 $cat_cond="";
		   $k=0;
		   foreach($cat_ids as $key=> $value)
		   {
			   if($k==0)
			   {
				$cat_cond=" and category_id  in(".implode(",",$value).")"; 
				
			   }
			   else
			   {
				$cat_cond.=" or category_id  in(".implode(",",$value).")";
				
			   }
			   $k++;
		   }
	}
	if($trans_type==2) //Issue
	{
		$sql_data=sql_select("select id, company_id, category_id, prod_id, cons_qty, rate, amount
		from inv_store_wise_qty_dtls where company_id=$company_id  and status_active=1 and is_deleted=0 $prod_cond $cat_cond");
	}
	else if($trans_type==1 || $trans_type==4) //Recv && Issue Return;
	{
		if($dyes_lot!="") $lot_cond=" and lot='$dyes_lot'"; 
		$sql_data=sql_select("select id, company_id, category_id, store_id, prod_id, cons_qty, rate, amount, lot
		from inv_store_wise_qty_dtls where  company_id=$company_id and store_id=$store_id and category_id in($category)  and status_active=1 and is_deleted=0 and prod_id=$prod_id $lot_cond");
	}
	$stock_prod_arr=array();
	if($trans_type==2) //Issue
	{
		$updated_store_ids=''; $updated_ids='';$prod_arr=array();
		foreach($sql_data as $row)
		{
			if($updated_store_ids=='') $updated_store_ids=$row[csf("id")];else $updated_store_ids.=",".$row[csf("id")];
		}
		$stock_prod_arr=$updated_store_ids;//.'**'.$stock_prod_arr;
	}
	else if($trans_type==1 || $trans_type==4) //recv
	{
		foreach($sql_data as $row)
		{
			$stock_prod_arr[$row[csf('company_id')]][$row[csf('prod_id')]][$row[csf('store_id')]][$row[csf('category_id')]][$row[csf('lot')]]=$row[csf('id')];
		}
	}
	return $stock_prod_arr;
} //Function End

?>