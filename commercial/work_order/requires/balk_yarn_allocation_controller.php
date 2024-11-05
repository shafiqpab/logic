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

//$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
//$buyer_name_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$supplier_arr = return_library_array("select id,short_name from lib_supplier where status_active=1 and is_deleted=0","id","short_name"); 
$yarn_count_arr = return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
$color_arr = return_library_array( "select id,color_name from lib_color",'id','color_name');
$returnRes_date="select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 group by prod_id";
$result_returnRes_date = sql_select($returnRes_date);
foreach($result_returnRes_date as $row)	
{
	$prod_date_array[$row[csf("prod_id")]]['min_date']=change_date_format($row[csf("min_date")]);
	$prod_date_array[$row[csf("prod_id")]]['max_date']=change_date_format($row[csf("max_date")]);
}	


//style search------------------------------//
if($action=="yarn_refarence_surch")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//echo $style_id;die;
	?>
    <script>
	function js_set_value(str)
	{
		$('#prod_id_des').val(str);
		parent.emailwindow.hide();
	}
    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="searchyarnfrm_1"  id="searchyarnfrm_1" autocomplete="off">
			<table width="700" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
                <thead>
                    <tr>
                        <th width="80">Product Id</th>
                        <th width="150">Supplier</th>
                        <th width="130">Yarn Type</th>
                        <th width="120">Count</th>
                        <th width="120">Lot</th>
                        <th ><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
                    </tr>
                </thead> 
                <tbody>
                    <tr>
                        <td align="center">
                            <input type="text" id="txt_pord_id" name="txt_pord_id" class="text_boxes" style="width:80px"  />
                        </td>
                        <td align="center">
                            <?
								echo create_drop_down( "cbo_supplier", 150, "select a.id,a.supplier_name from lib_supplier_party_type b, lib_supplier a where a.id=b.supplier_id and b.party_type =2 and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by supplier_name","id,supplier_name",1, "-- Select --", 0, "",0 );  	 
                            ?>                    
                        </td> 
                        <td align="center">				
                            <?
                                echo create_drop_down( "cbo_yarn_type", 130, $yarn_type,"", 1, "--Select--", 0, "",0 );
                            ?>                    
                        </td>  
                        <td align="center">				
                            <?
                                echo create_drop_down("cbo_yarn_count",120,"select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",1, "-- Select --", $selected, "");
                            ?>                   
                        </td> 
                        <td align="center">				
                            <input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes" style="width:110px" />                    
                        </td>
                        <td align="center" >
                           <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_pord_id').value+'_'+document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_yarn_type').value+'_'+document.getElementById('cbo_yarn_count').value+'_'+document.getElementById('txt_lot_no').value, 'create_yarn_search_list_view', 'search_div', 'balk_yarn_allocation_controller', 'setFilterGrid(\'table_body\',-1)')" style="width:100px;" />				
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" id="prod_id_des" name="prod_id_des" class="text_boxes"/>    
            <div align="center" valign="top" id="search_div"> </div> 
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
	exit();
}

if($action=="create_yarn_search_list_view")
{ 
	$ex_data = explode("_",$data);
	$product_id = str_replace("'","",$ex_data[0]);
	$supplier_id = str_replace("'","",$ex_data[1]);
	$yarn_type_id = str_replace("'","",$ex_data[2]);
	$yarn_count_id = str_replace("'","",$ex_data[3]);
	$lot_no = str_replace("'","",$ex_data[4]);
	
	
	
	if( $product_id!="")  $product_cond=" and id=$product_id"; else  $product_cond="";
	if( $supplier_id!=0 )  $supplier_cond=" and supplier_id='$supplier_id'"; else  $supplier_cond="";
	if( $yarn_type_id!=0 )  $yarn_type_cond=" and yarn_type='$yarn_type_id'"; else  $yarn_type_cond="";
	if( $yarn_count_id!=0 )  $yarn_count_cond=" and yarn_count_id='$yarn_count_id'"; else  $yarn_count_cond="";
	if( $lot_no!="")  $lot_cond=" and lot='$lot_no'"; else  $lot_cond="";
	
	$sql = "select id, supplier_id, product_name_details, lot, yarn_count_id, yarn_type, color, current_stock, stock_value,allocated_qnty,available_qnty from   product_details_master where status_active=1 and is_deleted=0 and item_category_id=1 $product_cond $supplier_cond $yarn_type_cond $yarn_count_cond $lot_cond";
	//echo $sql;

?>
	<table width="1000" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
        <thead>
            <tr>
                <th width="30">Sl</th>
                <th width="40">Prod Id</th>
                <th width="100">Supplier Name</th>
                <th width="200">Product Name</th>
                <th width="60">Lot</th>
                <th width="60">Yarn Count</th>
                <th width="60">Yarn Type</th>
                <th width="80">Color</th>
                <th width="60">Stock</th>
                <th width="60">Allocated</th>
                <th width="60">Un Allocated</th>
                <th width="60">Age (Days)</th>
                <th >DOH</th>
            </tr>
        </thead> 
    </table>
    <div style="width:1000px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
    <table width="980" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
    	<tbody>
        <?
		$k=1;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
			if ($k%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$ageOfDays = datediff("d",$prod_date_array[$row[csf("id")]]['min_date'],date("d-m-Y"));
			$daysOnHand = datediff("d",$prod_date_array[$row[csf("id")]]['max_date'],date("d-m-Y"));
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("id")];?>_<? echo $row[csf("product_name_details")];?>')" style="cursor:pointer;">
            	<td width="30" align="center"><? echo $k; ?></td>
                <td width="40"  align="center"><p><? echo $row[csf("id")];?></p></td>
                <td width="100"><p><? echo $supplier_arr[$row[csf("supplier_id")]];?></p></td>
                <td width="200"><p><? echo $row[csf("product_name_details")];?></p></td>
                <td width="60" ><p><? echo $row[csf("lot")];?></p></td>
                <td width="60"  align="center"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]];?></p></td>
                <td width="60"><p><? echo $yarn_type[$row[csf("yarn_type")]];?></p></td>
                <td width="80"><p><? echo $color_arr[$row[csf("color")]];?></p></td>
                <td width="60" align="right"><p><? echo number_format($row[csf("current_stock")],0);?></p></td>
                <td width="60"  align="right"><p><? echo number_format($row[csf("allocated_qnty")],0);?></p></td>
                <td width="60"  align="right"><p><? echo number_format($row[csf("available_qnty")],0);?></p></td>
                <td width="60" align="center"><p><? echo $ageOfDays;?></p></td>
                <td  align="center"><p><? echo $daysOnHand;?><p></td>
            </tr>
            <?
			$k++;
		}
		?>
        </tbody>
    </table>
    </div>
<?

}




//list view here--------------------//
if($action=="show_dtls_list_view")
{
	$sql="select id, supplier_id, product_name_details, lot, yarn_count_id, yarn_type, color, current_stock, stock_value,allocated_qnty,available_qnty,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd from  product_details_master where status_active=1 and is_deleted=0 and item_category_id=1 and id=$data";
	
	//var_dump();
	
?>
<fieldset>
	<table width="1000" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
        <thead>
            <tr>
                <th width="30">Sl</th>
                <th width="40">Prod Id</th>
                <th width="60">Count</th>
                <th width="200">Composition</th>
                <th width="80">Yarn Type</th>
                <th width="80">Color</th>
                <th width="80">Lot</th>
                <th width="80">Supplier</th>
                <th width="70">Stock</th>
                <th width="70">Allocated</th>
                <th width="70">Un Allocated</th>
                <th width="60">Age (Days)</th>
                <th >DOH</th>
            </tr>
        </thead> 
    </table>
    <table width="1000" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" style="margin-bottom:20px;">
    	<tbody>
        <?
		$k=1;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
			if ($k%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$ageOfDays = datediff("d",$prod_date_array[$row[csf("id")]]['min_date'],date("d-m-Y"));
			$daysOnHand = datediff("d",$prod_date_array[$row[csf("id")]]['max_date'],date("d-m-Y"));
			$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]]." ".$row[csf("yarn_comp_percent1st")]."%\n";
            if($row[csf("yarn_comp_type2nd")]!=0) $compositionDetails.=$composition[$row[csf("yarn_comp_type2nd")]]." ".$row[csf("yarn_comp_percent2nd")]."%";
			?>
            <tr bgcolor="<? echo $bgcolor; ?>">
            	<td width="30" align="center"><? echo $k; ?></td>
                <td width="40"  align="center"><p><? echo $row[csf("id")];?></p></td>
                <td width="60"  align="center"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]];?></p></td>
                <td width="200"><p><? echo $compositionDetails;?></p></td>
                <td width="80"><p><? echo $yarn_type[$row[csf("yarn_type")]];?></p></td>
                <td width="80"><p><? echo $color_arr[$row[csf("color")]];?></p></td>
                <td width="80" ><p><? echo $row[csf("lot")];?></p></td>
                <td width="80"><p><? echo $supplier_arr[$row[csf("supplier_id")]];?></p></td>
                <td width="70" align="right" id="current_stock"><? echo number_format($row[csf("current_stock")],0);?></td>
                <td width="70"  align="right"><p><? echo number_format($row[csf("allocated_qnty")],0);?></p></td>
                <td width="70"  align="right"><p><? echo number_format($row[csf("available_qnty")],0);?></p></td>
                <td width="60" align="center"><p><? echo $ageOfDays;?></p></td>
                <td  align="center"><p><? echo $daysOnHand;?></p></td>
            </tr>
            <?
			$k++;
		}
		?>
        </tbody>
    </table>
	<div id="bottom_part">
        <table width="620" cellpadding="0" cellspacing="0" id="tbl_pay_head" rules="all" border="1" class="rpt_table" style="margin-bottom:20px;">
            <thead>
                <tr>
                    <th width="200">Buyer Name</th>
                    <th width="100">Allocat Qunatity</th>
                    <th width="250">Remarks</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <?
			$allocated_sql=sql_select("select id, product_id, buyer_id, remarks, allocate_qnty from com_balk_yarn_allocate where status_active=1 and is_deleted=0 and product_id=$data order by id");
			//echo $allocated_sql;die;
			$i=1;$k=0;
			if(count($allocated_sql)>0)
			{
				foreach($allocated_sql as $row)
				{				
					?>
					<tr>
						<td align="center">
						<? 
							echo create_drop_down( "cbobuyername_$i", 190, "select id,buyer_name from  lib_buyer where status_active=1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "--Select --", $row[csf("buyer_id")], "","","");
						?> 
						</td>
						<td align="center">
						<input type="text" id="txtamount_<? echo $i; ?>" name="txtamount_<? echo $i; ?>" style="width:80px;" class="text_boxes_numeric" value="<? echo $row[csf("allocate_qnty")]; $total_allocation+=$row[csf("allocate_qnty")]; ?>" onBlur="total_val(1)" >
						</td>
						<td align="center">
						<input type="text" id="txtremarks_<? echo $i; ?>" name="txtremarks_<? echo $i; ?>" style="width:230px;" class="text_boxes" value="<? echo $row[csf("remarks")]; ?>" >
						</td>
						<td>
                        <?
						if(count($allocated_sql)==$i)
						{
							?>
							<input style="width:30px;" type="button" id="incrementfactor_<? echo $i; ?>" name="incrementfactor_<? echo $i; ?>"  class="formbutton" value="+" onClick="add_factor_row(<? echo $i; ?>)"/>
							 <input style="width:30px;" type="button" id="decrementfactor_<? echo $i; ?>" name="decrementfactor_<? echo $i; ?>"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>)"/>
							 <?
						}
						else
						{
							?>
							<input style="width:30px; display:none;" type="button" id="incrementfactor_<? echo $i; ?>" name="incrementfactor_<? echo $i; ?>"  class="formbutton" value="+" onClick="add_factor_row(<? echo $i; ?>)"/>
							 <input style="width:30px;  display:none;" type="button" id="decrementfactor_<? echo $i; ?>" name="decrementfactor_<? echo $i; ?>"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>)"/>
                            <?
						}
						?>
						</td>
					</tr>
					<?
					$i++;$k++;
				}
				?>
            </tbody>
            <tfoot>
            	<tr>
                	<th>&nbsp;</th>
                    <th align="right" id="total_val_foot"><? echo number_format($total_allocation,2,".",""); ?></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
        </table>
        <table width="680" cellpadding="0" cellspacing="0" id="" rules="all" border="0" class="">
            <tr>
                <td align="center" colspan="3" valign="middle" class="button_container">
                <?
					echo load_submit_buttons( $permission, "fnc_charge_payment", 1,0,"",0);
				 ?>
                <input type="hidden" id="update_dts_id" name="update_dts_id" value="">
                
                </td>
            </tr>
        </table>
                <?
			}
			else
			{
				?>
                <tr>
                	<td align="center">
					<? 
                    	echo create_drop_down( "cbobuyername_1", 190, "select id,buyer_name from  lib_buyer where status_active=1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "--Select --", $selected, "","","");
                    ?> 
                    </td>
                    <td align="center">
                    <input type="text" id="txtamount_1" name="txtamount_1" style="width:80px;" class="text_boxes_numeric" onBlur="total_val(1)" >
                    </td>
                    <td align="center">
                    <input type="text" id="txtremarks_1" name="txtremarks_1" style="width:230px;" class="text_boxes" >
                    </td>
                    <td>
                    <input style="width:30px;" type="button" id="incrementfactor_1" name="incrementfactor_1"  class="formbutton" value="+" onClick="add_factor_row(1)"/>
                     <input style="width:30px;" type="button" id="decrementfactor_1" name="decrementfactor_1"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1)"/>&nbsp;
                    </td>
                </tr>
            </tbody>
            <tfoot>
            	<tr>
                	<th>&nbsp;</th>
                    <th align="right" id="total_val_foot"></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
        </table>
        <table width="680" cellpadding="0" cellspacing="0" id="" rules="all" border="0" class="">
            <tr>
                <td align="center" colspan="3" valign="middle" class="button_container">
                <?
				//function load_submit_buttons( $permission, $sub_func, $is_update, $is_show_print, $refresh_function, $btn_id, $is_show_approve )
					echo load_submit_buttons( $permission, "fnc_charge_payment", 0,0,"fnc_refresh();",0);
				 ?>
                <input type="hidden" id="update_dts_id" name="update_dts_id" value="">
                
                </td>
            </tr>
        </table>
                <?
			}
			?>
    </div>
    
    </fieldset>
    <?
}

if($action=="show_primary_dtls_list_view")
{
?>
        <table width="620" cellpadding="0" cellspacing="0" id="tbl_pay_head" rules="all" border="1" class="rpt_table" style="margin-bottom:20px;">
            <thead>
                <tr>
                    <th width="200">Buyer Name</th>
                    <th width="150">Allocat Qunatity</th>
                    <th width="200">Remarks</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                	<td align="center">
					<? 
                    	echo create_drop_down( "cbobuyername_1", 190, "select id,buyer_name from  lib_buyer where status_active=1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "--Select --", $selected, "","","");
                    ?> 
                    </td>
                    <td align="center">
                    <input type="text" id="txtamount_1" name="txtamount_1" style="width:130px;" class="text_boxes_numeric" >
                    </td>
                    <td align="center">
                    <input type="text" id="txtremarks_1" name="txtremarks_1" style="width:180px;" class="text_boxes" >
                    </td>
                    <td>
                    <input style="width:30px;" type="button" id="incrementfactor_1" name="incrementfactor_1"  class="formbutton" value="+" onClick="add_factor_row(1)"/>
                     <input style="width:30px;" type="button" id="decrementfactor_1" name="decrementfactor_1"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1)"/>
                    </td>
                </tr>
            </tbody>
        </table>
        <table width="680" cellpadding="0" cellspacing="0" id="" rules="all" border="0" class="">
            <tr>
                <td align="center" colspan="3" valign="middle" class="button_container">
                <?
				//function load_submit_buttons( $permission, $sub_func, $is_update, $is_show_print, $refresh_function, $btn_id, $is_show_approve )
					echo load_submit_buttons( $permission, "fnc_charge_payment", 0,0,"fnc_refresh();",0);
				 ?>
                <input type="hidden" id="update_dts_id" name="update_dts_id" value="">
                
                </td>
            </tr>
        </table>
    <?
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$total_row=str_replace("'","",$total_row);
	$hidden_yarn_id=str_replace("'","",$hidden_yarn_id);
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//echo $btb_lc_id."xx";die;

		if($hidden_yarn_id!="")
		{
			$id=return_next_id( "id", "com_balk_yarn_allocate", 1 ) ;
			$field_array="id,product_id,buyer_id,remarks,allocate_qnty,inserted_by,insert_date";
			
			$data_array="";
			for($i=1;$i<=$total_row;$i++)
			{
			if($i!=1)$id=$id+1;
			$cbo_buyer="cbobuyername_".$i;
			$txtamount="txtamount_".$i;
			$txtremarks="txtremarks_".$i;
			if ($i!=1) $data_array .=",";
			$data_array	.="(".$id.",".$hidden_yarn_id.",".str_replace("'","",$$cbo_buyer).",'".str_replace("'","",$$txtremarks)."',".str_replace("'","",$$txtamount).",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
			$rID=sql_insert("com_balk_yarn_allocate",$field_array,$data_array,1);
		}
		
		//echo $rID;die;
		
		//echo "insert into com_balk_yarn_allocate($field_array) values $data_array";die;
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$id);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".str_replace("'",'',$id);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'",'',$id);
			}
		}
		disconnect($con);
		die;

	}
	
	if ($operation==1)  // Update Here
	{
		//echo $txt_entry_id;die;
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if($hidden_yarn_id!="")
		{
			$up_row=execute_query("delete from com_balk_yarn_allocate where product_id=$hidden_yarn_id");
			
			$id=return_next_id( "id", "com_balk_yarn_allocate", 1 ) ;
			$field_array="id,product_id,buyer_id,remarks,allocate_qnty,inserted_by,insert_date";
			
			$data_array="";
			for($i=1;$i<=$total_row;$i++)
			{
			if($i!=1)$id=$id+1;
			$cbo_buyer="cbobuyername_".$i;
			$txtamount="txtamount_".$i;
			$txtremarks="txtremarks_".$i;
			if ($i!=1) $data_array .=",";
			$data_array	.="(".$id.",".$hidden_yarn_id.",".str_replace("'","",$$cbo_buyer).",'".str_replace("'","",$$txtremarks)."',".str_replace("'","",$$txtamount).",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
			$rID=sql_insert("com_balk_yarn_allocate",$field_array,$data_array,1);
			
		}
		
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$id);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1**".str_replace("'",'',$id);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'",'',$id);
			}
		}
		disconnect($con);
		die;
	}
	
	
}


?>

