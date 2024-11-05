<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if($action=="item_desc_popup")
{
	echo load_html_head_contents("Item Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;

    	function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );

			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
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
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

				toggle( document.getElementById( 'tr_' + str), '#FFFFCC' );

				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str );
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

    </script>
    <?
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	
	//$company=str_replace("'","",$company);
//	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
//	$txt_item_group_id=str_replace("'","",$txt_item_group_id);
//	$txt_item_acc=str_replace("'","",$txt_item_acc);
//	$txt_item_account_id=str_replace("'","",$txt_item_account_id);
//	$txt_item_acc_no=str_replace("'","",$txt_item_acc_no);
//	
//	$sql_cond="";
//	if($txt_item_group_id!="") $sql_cond=" and item_group_id in($txt_item_group_id)";
//	$item_sub_group_id=str_replace("'","",$txt_item_sub_group_id);
//	//echo $item_sub_group_id; die;
//	$item_sub_group_id=explode(',',$item_sub_group_id);
//	foreach($item_sub_group_id as $id=>$Key)
//	{
//		$item_sub_group_id[$id]="'".$Key."'";
//		$iteme_sub_group_multi_id.=$item_sub_group_id[$id].',';
//	}
//	$iteme_sub_group_multi_id=chop($iteme_sub_group_multi_id,',');
//	
//	if (str_replace("'","",$iteme_sub_group_multi_id)!=="") 
//	$sql_cond.=" and sub_group_code in($iteme_sub_group_multi_id)";
		

	$sql="SELECT id,item_account,item_category_id,item_group_id,item_description,supplier_id,sub_group_code, sub_group_name,item_code from  product_details_master where item_category_id in($item_category_id) and company_id=$cbo_company_id and status_active=1 and is_deleted=0 ";
	//echo $sql; die;
	$arr=array(2=>$general_item_category,3=>$itemgroupArr,7=>$supplierArr);
	echo  create_list_view("list_view", "Item Account,Item Code,Item Category,Item Group,Item Sub Group Code,Item Sub Group Name,Item Description,Supplier,Product ID", "70,70,110,130,130,130,150,100","1020","320",0, $sql , "js_set_value", "id,item_description", "", 1, "0,0,item_category_id,item_group_id,0,0,0,supplier_id,0", $arr , "item_account,item_code,item_category_id,item_group_id,sub_group_code,sub_group_name,item_description,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,00,0,0','',1) ;


	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";

	?>
    <script language="javascript" type="text/javascript">
	var item_acc_no_arr=item_acc_id_arr=item_acc_arr=new Array();
	var txt_item_acc_no='<? echo $txt_item_acc_no;?>';
	var txt_item_account_id='<? echo $txt_item_account_id;?>';
	var txt_item_acc='<? echo $txt_item_acc;?>';
	//alert(txt_item_acc_no);
	if(txt_item_acc_no !="")
	{
		item_acc_no_arr=txt_item_acc_no.split(",");
		item_acc_id_arr=txt_item_account_id.split(",");
		item_acc_arr=txt_item_acc.split(",");
		var item_account="";
		for(var k=0;k<item_acc_no_arr.length; k++)
		{
			item_account=item_acc_no_arr[k]+'_'+item_acc_id_arr[k]+'_'+item_acc_arr[k];
			js_set_value(item_account);
		}
	}
	</script>

    <?

	exit(); 
}

if($action=="create_product_search_list_view")
{
	//$data=explode('**',$data);
//	$company_id=$data[0];
//	$item_category_id=$data[1];
//	$search_by=$data[2];
//	$search_string=trim($data[3]);
//	
//	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
//	
//	if($company_id==0) $company_cond=""; else $company_cond=" and company_id=$company_id";
//	
//	if($item_category_id==1)
//	{
//		if($search_by==1) 
//			$search_field_cond=" and product_name_details like '%".$search_string."%'"; 
//		else if($search_by==2) 
//			$search_field_cond=" and id=$search_string"; 
//		else 
//			$search_field_cond=" and lot like '%".$search_string."%'";	
//		
//		$sql= "select id, company_id, item_category_id, product_name_details, lot, current_stock, avg_rate_per_unit, stock_value from product_details_master where status_active=1 and is_deleted=0 and item_category_id=$item_category_id $company_cond $search_field_cond order by id";
//		
//		$arr=array(0=>$company_arr,1=>$item_category);
//		echo create_list_view("tbl_list_search", "Company, Item Category, Product Id,Lot No,Product Details, Current Stock, Avg. Rate(Tk.), Stock Value", "60,80,70,80,240,90,90","890","260",0, $sql , "js_set_value", "id,product_name_details,company_id", "", 1, "company_id,item_category_id,0,0", $arr , "company_id,item_category_id,id,lot,product_name_details,current_stock,avg_rate_per_unit,stock_value", "",'','0,0,0,0,0,2,2,2','',0) ;
//	}
//	else
//	{
//		if($search_by==1) 
//			$search_field_cond=" and product_name_details like '%".$search_string."%'"; 
//		else if($search_by==2) 
//			$search_field_cond=" and id=$search_string";  
//			
//		$sql= "select id, company_id, item_category_id, product_name_details, current_stock, avg_rate_per_unit, stock_value from product_details_master where status_active=1 and is_deleted=0 and item_category_id=$item_category_id $company_cond $search_field_cond order by id";
//		
//		$arr=array(0=>$company_arr,1=>$item_category);
//		echo create_list_view("tbl_list_search", "Company, Item Category, Product Id,Product Details, Current Stock, Avg. Rate(Tk.), Stock Value", "60,110,70,280,90,90","890","260",0, $sql , "js_set_value", "id,product_name_details,company_id", "", 1, "company_id,item_category_id,0,0", $arr , "company_id,item_category_id,id,product_name_details,current_stock,avg_rate_per_unit,stock_value", "",'','0,0,0,0,2,2,2','',0) ;
//
//	}
//	
//   exit(); 
}


if ($action=="store_revaluation")
{
	extract($_REQUEST);
	$con = connect();
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	
	if($cbo_company_id==0 || $cbo_item_category_id==0)
	{
		echo "Company Or Item Category Missing";die;
	}
	
	$sql_dyes_trans="select b.PROD_ID, b.ID as TRANS_ID, b.CONS_QUANTITY, b.CONS_AMOUNT, b.TRANSACTION_TYPE 
	from inv_transaction b where b.company_id=$cbo_company_id and b.item_category=$cbo_item_category_id and b.status_active=1 and b.is_deleted=0 
	order by b.PROD_ID, b.ID";
	
	//echo $sql_dyes_trans;die;
	$result=sql_select($sql_dyes_trans);
	//echo count($result);die;
	$i=1;$k=1;
	//$update_field="cons_rate*cons_amount*updated_by*update_date";
	$upTransID=true;
	foreach($result as $row)
	{
		if($prod_check[$row["PROD_ID"]]=="")
		{
			//$rcv_data=array();
			$prod_check[$row["PROD_ID"]]=$row["PROD_ID"];
			$rcv_data[$row["PROD_ID"]]["qnty"]=0;
			$rcv_data[$row["PROD_ID"]]["amt"]=0;
		}
		
		if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
		{
			$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
			$rcv_data[$row["PROD_ID"]]["amt"]+=$row["CONS_AMOUNT"];
			$k=0;
		}
		else
		{
			if($k==0)
			{
				$runtime_rate=0;
				if($rcv_data[$row["PROD_ID"]]["qnty"] > 0 && $rcv_data[$row["PROD_ID"]]["amt"] > 0)
				{
					$runtime_rate=number_format(($rcv_data[$row["PROD_ID"]]["amt"]/$rcv_data[$row["PROD_ID"]]["qnty"]),8,'.','');
				}
			}
			$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),8,'.','');
			
			$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
			if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
			$rcv_data[$row["PROD_ID"]]["qnty"] -= $row["CONS_QUANTITY"];
			$rcv_data[$row["PROD_ID"]]["amt"] -= $issue_amount;
			$k++;
		}
	}
	
	/* ##### difine Porduct ID Product Part update  */
	$upProdID=true;
	foreach($rcv_data as $prod_id=>$prod_val)
	{
		$prod_agv_rate=0;
		if($prod_val["qnty"]>0 && $prod_val["amt"]>0) 
		{
			$prod_agv_rate=number_format($prod_val["amt"],8,'.','')/number_format($prod_val["qnty"],8,'.','');
		}
		$upProdID=execute_query("update product_details_master set current_stock='".number_format($prod_val["qnty"],8,'.','')."', stock_value='".number_format($prod_val["amt"],8,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,8,'.','')."' where id=$prod_id");
		if(!$upProdID) { echo "update product_details_master set current_stock='".number_format($prod_val["qnty"],8,'.','')."', stock_value='".number_format($prod_val["amt"],8,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,8,'.','')."' where id=$prod_id";oci_rollback($con); die;}
	}
	
	
	
	//mysql_query("ROLLBACK");
	//echo "10**".$rID. "&&". $rID2. "&&".$rID3. "&&". $rID4 . "&&". $rID5 . "&&". $rID6;die;
	
	if($db_type==0)
	{
		if($upTransID && $upProdID)
		{
			mysql_query("COMMIT");  
			echo "Data Revaluation is completed successfully";
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo "Data Revaluation is not completed successfully";
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($upTransID && $upProdID)
		{
			oci_commit($con);  
			echo "Data Revaluation is completed successfully";
		}
		else
		{
			oci_rollback($con);
			echo "Data Revaluation is not completed successfully";
		}
	}
	disconnect($con);
	die;
}

?>
