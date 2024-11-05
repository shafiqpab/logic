<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create All Item Inquiry
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	03-02-2018
Updated by 		:	
Update date		: 	 
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if($action=="synchronize_stock")
{
	extract($_REQUEST);
	$con = connect();
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	//echo "$cbo_company_id test $cbo_item_category_id";die;
	
	$row_transaction=sql_select("select prod_id, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal_qnty 
	from inv_transaction 
	where company_id=$cbo_company_id and item_category=$cbo_item_category_id and status_active=1 and is_deleted=0 
	group by prod_id
	order by prod_id");
	$prod_trans_data=array();
	foreach($row_transaction as $row)
	{
		$prod_trans_data[$row[csf("prod_id")]]=$row[csf("bal_qnty")];
	}
	//echo "<pre>";print_r($prod_trans_data);die;
	$row_prod=sql_select("select id, avg_rate_per_unit, current_stock, allocated_qnty from product_details_master where company_id=$cbo_company_id and item_category_id=$cbo_item_category_id and status_active=1 and is_deleted=0 order by id");
	$upPropotionID=true;
	foreach($row_prod as $row)
	{
		if($cbo_item_category_id==1)
		{
			$current_stock=number_format($prod_trans_data[$row[csf("id")]],4,".","");
			$current_value=number_format(($current_stock*$row[csf("avg_rate_per_unit")]),6,".","");
			$available_qnty=number_format(($current_stock-$row[csf("allocated_qnty")]),4,".","");
			//echo $available_qnty."=".$row[csf("id")];die;
			$upProdID=execute_query("update product_details_master set current_stock='".$current_stock."', stock_value='".$current_value."', available_qnty='".$available_qnty."', updated_by=1, update_date='".$pc_date_time."' where id=".$row[csf("id")]);
			
			if($upPropotionID) $upPropotionID=1 ; else {echo "update product_details_master set current_stock='".$current_stock."', stock_value='".$current_value."', available_qnty='".$available_qnty."', updated_by=1, update_date='".$pc_date_time."' where id=".$row[csf("id")];die;}
		}
		else
		{
			if( number_format($prod_trans_data[$row[csf("id")]],4,".","") != number_format($row[csf("current_stock")],4,".","") )
			{
				$current_stock=number_format($prod_trans_data[$row[csf("id")]],4,".","");
				$current_value=number_format(($current_stock*$row[csf("avg_rate_per_unit")]),6,".","");
				$available_qnty=number_format(($current_stock-$row[csf("allocated_qnty")]),4,".","");
				//echo $available_qnty."=".$row[csf("id")];die;
				$upProdID=execute_query("update product_details_master set current_stock='".$current_stock."', stock_value='".$current_value."', available_qnty='".$available_qnty."', updated_by=1, update_date='".$pc_date_time."' where id=".$row[csf("id")]);
				
				if($upPropotionID) $upPropotionID=1 ; else {echo "update product_details_master set current_stock='".$current_stock."', stock_value='".$current_value."', available_qnty='".$available_qnty."', updated_by=1, update_date='".$pc_date_time."' where id=".$row[csf("id")];die;}
			}
		}
	}
	
	
	if($db_type==2)
	{
		if($upPropotionID)
		{
			oci_commit($con); 
			echo "Data Synchronize is completed successfully";
			die;
		}
		else
		{
			oci_rollback($con);
			echo "Data Synchronize is not completed successfully";
			die;
		}
	}
	else
	{
		if($upPropotionID)
		{
			mysql_query("COMMIT");
			echo "Data Synchronize is completed successfully";
			die;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "Data Synchronize is not completed successfully";
			die;
		}
	}
	
}

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("All Item Inquiry Info","../", 1, 1, $unicode);
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function synchronize_stock()
{
	if( form_validation('cbo_company_id*cbo_item_category_id','Company*Item Category')==false )
	{
		return;
	}
	var data="action=synchronize_stock"+get_submitted_data_string('cbo_company_id*cbo_item_category_id',"../");
	//var data="action=synchronize_stock&prod_id="+prod_id;
	freeze_window(3);
	http.open("POST","all_item_inquiry.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_synchronize_stock_reponse;
}

function fn_synchronize_stock_reponse()
{	
	if(http.readyState == 4) 
	{	 
		var response=trim(http.responseText);
		alert(response);
		release_freezing();
	}
} 
 

 
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../",$permission);  ?>    		 
    <form name="storeItemInquiry_1" id="storeItemInquiry_1" autocomplete="off" > 
    <div style="width:100%;" align="center">
        <h3 style="width:630px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div style="width:100%;" id="content_search_panel">
            <fieldset style="width:630px;">
                <table class="rpt_table" width="630" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th class="must_entry_caption">Company</th> 
                            <th class="must_entry_caption">Item Category</th>                               
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
							<? 
                            	echo create_drop_down( "cbo_company_id", 200, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>                            
                        </td>
                        <td> 
							<? echo create_drop_down( "cbo_item_category_id", 200, $item_category,"",1, "--- Select Item Category ---", $selected,"",0,"","","","12,24,25,28,30"); ?>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Synchronize" onClick="synchronize_stock()" style="width:100px" class="formbutton" />
                        </td>
                    </tr>
                </table> 
            </fieldset> 
		</div>
    </div>
    <br /> 
    <div id="report_container2" style="margin-left:5px"></div> 
    </form>    
</div>  
</body> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>


<?


?>
