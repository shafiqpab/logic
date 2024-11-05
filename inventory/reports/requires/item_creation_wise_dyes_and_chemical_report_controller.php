 <?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//load drop down Buyer
if ($action=="load_drop_down_model")
{
	$data = explode("_", $data);

	if ($data[1]!=0) {

		$cat_con = " and item_category_id=$data[1]";
	}
	else{

		$cat_con = '';
	}

	$model_arr=return_library_array( "select distinct model from product_details_master where status_active=1 and is_deleted=0 $cat_con  and company_id=$data[0] and model is not null",'model','model');

	//if(count($model_arr)==1) $selected = key($model_arr); else $selected=0;

	//echo create_drop_down( "cbo_model", 155, $model_arr,"", 1, "-- All Model --", $selected, "",0 );
	echo create_drop_down( "cbo_model", 155, $model_arr,"", 1, "-- All Model --", 1, "",1 );

	exit();
}


if ($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_name  	= str_replace("'","",$cbo_company_id);
	$item_cat_id  	= str_replace("'","",$cbo_item_cat_id);
	$model  		= $cbo_model;
	$txt_date  		= str_replace("'","",$txt_date);
	$rpt_type 		= str_replace("'","",$rpt_type);


	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');


	if ($company_name==0) {
		
		echo "Please Select Company Name First!!!";
		die;
	}

	if ($company_name==0) {
		
		echo "Please Select Item Category First!!!";

		die;
	}


	ob_start();
	if ($rpt_type==1)
	{

		$model_con = '';
		$cat_con = '';

		if(str_replace("'","",$item_cat_id)!=0)
		{
			$cat_con = "and item_category_id=$item_cat_id";
		}
		else
		{
			$cat_con = "and item_category_id in(5,6,7,23)";
		}

		if(str_replace("'","",$model)!='')
		{
			//$model_con = " and model=$model";
			$model_con = " and MODEL_NAME_UPDATE=$model";
		}
		
		if($model=="'0'")
		{
			$model_con = "";
		}

		$year_date_cond="";

		if($db_type==0)
		{
			if($txt_date!='') $year_date_cond=" and (date(insert_date)='$txt_date' OR date(insert_date)='$update_date')"; else $year_date_cond="";
		}
		else if($db_type==2)
		{
			if($txt_date!='') $year_date_cond=" and (TO_CHAR(insert_date,'dd-mm-yyyy')='$txt_date' OR TO_CHAR(update_date,'dd-mm-yyyy')='$txt_date')"; else $year_date_cond="";
		}

		// $sql_mother = "SELECT COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, SUB_GROUP_NAME, ITEM_CODE, ITEM_DESCRIPTION, ITEM_SIZE, ITEM_NUMBER, MODEL, min(ID) as MIN_PROD_ID from product_details_master 
		// where status_active=1 and is_deleted=0 and  company_id=$company_name $cat_con $model_con and model is not null
		// group by COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, SUB_GROUP_NAME, ITEM_CODE, ITEM_DESCRIPTION, ITEM_SIZE, ITEM_NUMBER, MODEL";

		$sql_mother = "SELECT COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, SUB_GROUP_NAME, ITEM_CODE, ITEM_DESCRIPTION, ITEM_SIZE, ITEM_NUMBER, MODEL, min(ID) as MIN_PROD_ID from product_details_master 
		where status_active=1 and is_deleted=0 and  company_id=$company_name $cat_con $model_con 
		group by COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, SUB_GROUP_NAME, ITEM_CODE, ITEM_DESCRIPTION, ITEM_SIZE, ITEM_NUMBER, MODEL";

		//echo $sql_mother;
		$dtlsArray_mother=sql_select($sql_mother);

		$product_mother_id=array();
		foreach($dtlsArray_mother as $row)
		{
			$prod_key=$row["COMPANY_ID"]."*".$row["ITEM_CATEGORY_ID"]."*".$row["ITEM_GROUP_ID"]."*".trim($row["SUB_GROUP_NAME"])."*".trim($row["ITEM_CODE"])."*".trim($row["ITEM_DESCRIPTION"])."*".trim($row["ITEM_SIZE"])."*".trim($row["ITEM_NUMBER"])."*".trim($row["MODEL"]);
			$product_mother_id[$prod_key]=$row["MIN_PROD_ID"];
		}
		unset($dtlsArray_mother);
		//echo "<pre>";print_r($product_mother_id);die;
		// $sql = "SELECT ID, COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, SUB_GROUP_NAME, ITEM_CODE, ITEM_DESCRIPTION, ITEM_SIZE, ITEM_NUMBER, MODEL, CURRENT_STOCK  from product_details_master where status_active=1 and is_deleted=0 and round(current_stock, 4)>0 and  company_id=$company_name $cat_con $model_con and model is not null order by id asc";

		$sql = "SELECT ID, COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, SUB_GROUP_NAME, ITEM_CODE, ITEM_DESCRIPTION, ITEM_SIZE, ITEM_NUMBER, MODEL, CURRENT_STOCK,MODEL_NAME_UPDATE  from product_details_master where status_active=1 and is_deleted=0 and round(current_stock, 4)>0 and  company_id=$company_name $cat_con $model_con order by id asc";
		//round(current_stock, 8)>0 and
		$dtlsArray=sql_select($sql);
		?>
		<fieldset style="width:1300px">
			<table cellpadding="0" cellspacing="0" width="1300">
				<tr  class="form_caption" style="border:none;">
					<td align="center" colspan="6" width="100%"  style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" colspan="6" width="100%"  style="font-size:16px">
						<strong>
							<?
							echo $company_arr[str_replace("'","",$cbo_company_id)];
							?>
						</strong>
					</td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" colspan="6" width="100%"  style="font-size:14px"><strong> <? if($txt_date!="") echo "Date : ".change_date_format(str_replace("'","",$txt_date)) ;?></strong></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300" class="rpt_table" align="left" >
				<thead>
					<th width="30">Sl No</th>
					<th width="120">Company</th>
					<th width="150">Item Category</th>
					<th width="120">Item Group</th>
                    <th width="100">Item Sub-Group</th>
                    <th width="80">Item Code</th>
					<th width="180">Item Description</th>
                    <th width="80">Item Size</th>
                    <th width="80">Item Number</th>
                    <th width="80">Model</th>
                    <th width="80">Update Model</th>
					<th width="50">Master Product ID</th>
                    <th width="50">Child Product ID</th>
					<th>Stock Qty</th>
				</thead>			
				<tbody id="tbl_list_search">
					<?
					$i=1;
					foreach($dtlsArray as $key=>$row)
					{
						$prod_key=$row["COMPANY_ID"]."*".$row["ITEM_CATEGORY_ID"]."*".$row["ITEM_GROUP_ID"]."*".trim($row["SUB_GROUP_NAME"])."*".trim($row["ITEM_CODE"])."*".trim($row["ITEM_DESCRIPTION"])."*".trim($row["ITEM_SIZE"])."*".trim($row["ITEM_NUMBER"])."*".trim($row["MODEL"]);
						$mother_prod_id=$product_mother_id[$prod_key];						
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td align="center"><? echo $i;?></td>
							<td align="center"><? echo $company_arr[$row["COMPANY_ID"]];?></td>
							<td align="center"><? echo $item_category[$row["ITEM_CATEGORY_ID"]];?></td>
							<td align="center"><? echo $item_group_arr[$row["ITEM_GROUP_ID"]];?></td>
							<td align="center"><? echo $row["SUB_GROUP_NAME"];?></td>
							<td align="center"><? echo $row["ITEM_CODE"];?></td>
							<td align="center"><? echo $row["ITEM_DESCRIPTION"];?></td>
							<td align="center"><? echo $row["ITEM_SIZE"];?></td>
							<td align="center"><? echo $row["ITEM_NUMBER"];?></td>
                            <td align="center"><? echo $row["MODEL"] ?></td>
                            <td align="center"><? echo $row["MODEL_NAME_UPDATE"] ?></td>
							<td align="center"><? echo $mother_prod_id;?></td>
							<td align="center"><? echo $row["ID"];?></td>
							<td align="right"><? echo number_format($row["CURRENT_STOCK"],4);?></td>
						</tr><?							
						$i++;							
					}
					?>
				</tbody>
			</table>
        </fieldset>
        <?
	}

	$html = ob_get_contents();
	ob_clean();

	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename####$rpt_type";
	exit();
}