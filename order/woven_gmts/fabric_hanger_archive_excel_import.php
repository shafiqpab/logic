<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$permission=$_SESSION['page_permission'];

//$specialCharactersArr = array(0=>'*', 1=>'\'', 2=>'£', 3=>'$', 4=>'&', 5=>'(', 6=>')', 7=>'#', 8=>'~', 9=>'|', 10=>'=', 11=>'_', 12=>'"', 13=>'`', 14=>'^', 15=>'\\');
		
	function check_special_character($string)
	{
		$special_charater='*\'£$&()#~|=_"`^\\';
		$specialCharactersArr = str_split($special_charater);
		$splitStringArr=str_split($string);
		$result=array_diff($specialCharactersArr,$splitStringArr);
		if (count($result)<count($specialCharactersArr)) return 1;
		else return 0;
	}

	$cdate=date("d-m-Y");
	include('excel_reader.php');
	$output = `uname -a`;
	if( isset( $_POST["submit"] ) )
	{	
		
		//error_reporting(E_ALL);
		//ini_set('display_errors', '1');	
		extract($_REQUEST);

		
		foreach (glob("files/"."*.xls") as $filename){			
			@unlink($filename);
		}
		foreach (glob("files/"."*.xlsx") as $filename){			
			@unlink($filename);
		}

		$source_excel = $_FILES['uploadfile']['tmp_name'];
		$targetzip ='files/'.$_FILES['uploadfile']['name'];
		$file_name=$_FILES['uploadfile']['name'];
		//echo $source_excel.'**'.$targetzip.'**'.$file_name;die;
		unset($_SESSION['excel']);
		if (move_uploaded_file($source_excel, $targetzip)) 
		{
			
			$excel = new Spreadsheet_Excel_Reader($targetzip); 
			//echo $excel->sheets[0]['numRows'].'DX';die;
			$card_colum=0; $m=1; 
			$all_data_arr=array(); 
			

			for ($i = 1; $i <= $excel->sheets[0]['numRows']; $i++) 
			{
				if($m==1)
				{
					for ($j = 1; $j <= $excel->sheets[0]['numCols']; $j++) 
					{
					}
					$m++;
				}
				else
				{
					$company=$buyer=$dispo_no=$fabric_type=$finish_width=$fabric_construction=$fabric_composition=$fabric_gsm=$finish_type=$wash_type=$print_type=$sample_ref_type=$floor=$room=$rack=$shelf=$bin=$status='';
					$str_rep=array("*",  "=", "\r", "\n", "#");
					//$str_rep= preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $excel->sheets[0]['cells'][$i]); 
					if (isset($excel->sheets[0]['cells'][$i][1])) $company = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][1]);
					if (isset($excel->sheets[0]['cells'][$i][2])) $buyer = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][2]);
					if (isset($excel->sheets[0]['cells'][$i][3])) $dispo_no = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][3]);
					if (isset($excel->sheets[0]['cells'][$i][4])) $fabric_type = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][4]);
					if (isset($excel->sheets[0]['cells'][$i][5])) $finish_width = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][5]);
					if (isset($excel->sheets[0]['cells'][$i][6])) $fabric_construction = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][6]);
					if (isset($excel->sheets[0]['cells'][$i][7])) $fabric_composition  =  preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $excel->sheets[0]['cells'][$i][7]));//trim(preg_replace('/\s/u', ' ', $strtyu));
					if (isset($excel->sheets[0]['cells'][$i][8])) $fabric_gsm = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][8]);
					if (isset($excel->sheets[0]['cells'][$i][9])) $finish_type= $excel->sheets[0]['cells'][$i][9];
					if (isset($excel->sheets[0]['cells'][$i][10])) $wash_type = $excel->sheets[0]['cells'][$i][10];
					if (isset($excel->sheets[0]['cells'][$i][11])) $print_type = $excel->sheets[0]['cells'][$i][11];
					if (isset($excel->sheets[0]['cells'][$i][12])) $sample_ref_type = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][12]);
					if (isset($excel->sheets[0]['cells'][$i][13])) $floor = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][13]);
					if (isset($excel->sheets[0]['cells'][$i][14])) $room = trim($excel->sheets[0]['cells'][$i][14]);
					if (isset($excel->sheets[0]['cells'][$i][15])) $rack = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][15]);
					if (isset($excel->sheets[0]['cells'][$i][16])) $shelf = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][16]);
					if (isset($excel->sheets[0]['cells'][$i][17])) $bin = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][17]);
					if (isset($excel->sheets[0]['cells'][$i][18])) $status = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][18]);
					//echo $company.'==A';die;

					if(trim($company)!="")
					{

					
					$all_data_arr[$i][1]['company']=trim($company);
					$all_data_arr[$i][2]['buyer']=trim($buyer);
					$all_data_arr[$i][3]['dispo_no']=trim($dispo_no);
					$all_data_arr[$i][4]['fabric_type']=trim($fabric_type);
					$all_data_arr[$i][5]['finish_width']=trim($finish_width);
					$all_data_arr[$i][6]['fabric_construction']=trim($fabric_construction);
					$all_data_arr[$i][7]['fabric_composition']=trim($fabric_composition);
					$all_data_arr[$i][8]['fabric_gsm']=trim($fabric_gsm);
					$all_data_arr[$i][9]['finish_type']=trim($finish_type);
					$all_data_arr[$i][10]['wash_type']=trim($wash_type);
					$all_data_arr[$i][11]['print_type']=trim($print_type);
					$all_data_arr[$i][12]['sample_ref_type']=trim($sample_ref_type);
					$all_data_arr[$i][13]['floor']=trim($floor);
					$all_data_arr[$i][14]['room']=trim($room);
					$all_data_arr[$i][15]['rack']=trim($rack);
					$all_data_arr[$i][16]['shelf']=trim($shelf);
					$all_data_arr[$i][17]['bin']=trim($bin);
					$all_data_arr[$i][18]['status']=trim($status);

					}

				}
			}
			//echo '<pre>';print_r($all_data_arr);die;

			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}


			$company_library=return_library_array("select company_name, id from lib_company where status_active=1 and is_deleted=0","company_name","id");
			$company_array=return_library_array( "select id, company_name from lib_company",'id','company_name');
            $buyer_name_arr=return_library_array( "select buyer_name,id from lib_buyer",'buyer_name','id');
			//$lib_yarn_count_fab_deterArr=return_library_array( "select construction,id from lib_yarn_count_determina_mst", "construction", "id");
			$sql_fab="SELECT ID, TYPE, CONSTRUCTION from  lib_yarn_count_determina_mst where is_deleted=0   order by id DESC";
            $resultFab = sql_select($sql_fab);
			foreach($sql_fab as $row )
			{
				$lib_yarn_count_fab_deterArr[$row['CONSTRUCTION']]=$row['ID'];
				if($row['TYPE']!="")
				{
					$lib_yarn_count_fab_type_deterArr[$row['TYPE']]=$row['TYPE'];
				}
				
			}

			$lib_yarn_count_fab_compositionArr=return_library_array( "select composition_name,composition_name from lib_composition_array", "composition_name", "composition_name");
			//$finish_types = array(1=>"Regular",2=>"Peach",3=>"Brush");
			$finish_types = array("Regular"=>1,"Peach"=>2,"Brush"=>3);
			// $floor_arr=return_library_array( "select b.floor_id as id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id   and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	        // group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc",'id','floor_room_rack_name');
			$floor_arr=return_library_array( "select  a.floor_room_rack_name,b.floor_id as id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id   and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	        group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc",'floor_room_rack_name','id');

            $room_arr=return_library_array( "select  a.floor_room_rack_name,b.room_id as id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id    and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	        group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc",'floor_room_rack_name','id');
            $rack_arr=return_library_array( "select  a.floor_room_rack_name,b.rack_id as id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id   and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	        group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc",'floor_room_rack_name','id');
            $shelf_arr=return_library_array( "select  a.floor_room_rack_name,b.shelf_id as id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id    and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	        group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc",'floor_room_rack_name','id');

			$bin_arr=return_library_array( "select a.floor_room_rack_name,b.bin_id as id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id    and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	        group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc",'floor_room_rack_name','id');

			$emblishment_wash_typeArr = return_library_array("select EMB_NAME,EMB_ID from  LIB_EMBELLISHMENT_NAME where EMB_TYPE=3 and status_active=1 and is_deleted=0 order by EMB_NAME", "EMB_NAME", "EMB_ID");
			$emblishment_print_typeArr = return_library_array("select EMB_NAME,EMB_ID from  LIB_EMBELLISHMENT_NAME where EMB_TYPE=1 and status_active=1 and is_deleted=0 order by EMB_NAME", "EMB_NAME", "EMB_ID");
			//$sample_ref_typesArr = array(1=>"SSM-Yarn Dyed Sample",2=>"SSD-Solid Dyed Sample",3=>"SSR-Rotary Print Sample",4=>"SSP-Digital Print Sample");
			$sample_ref_typesArr = array("SSM-Yarn Dyed Sample"=>1,"SSD-Solid Dyed Sample"=>2,"SSR-Rotary Print Sample"=>3,"SSP-Digital Print Sample"=>4);

 
		
			// $sql_prod_res=sql_select( "select id,company_id,buyer_id,dispo_no,fabric_hanger_date,location_id,fabric_type,finish_width,fab_construction,determination_id,fab_composition,fabric_gsm,finish_type,wash_type,print_type,floor_id,room,rack,shelf,bin,sample_ref_type,system_number,status_active from wo_fabric_hanger_archive_mst where status_active=1 and is_deleted=0");

			// foreach ($sql_prod_res as $val)
			// {
			// 	$company = $val['COMPANY_ID'];
			// 	$buyer = $val['buyer_id'];
			// 	$dispo_no = $val['dispo_no'];
			// 	$fh_date = $val['fabric_hanger_date'];
			// 	$location = $val['location_id'];
			// 	$fabric_type = $val['fabric_type'];
			// 	$finish_width = $val['finish_width'];
			// 	$compfabric_constructionany = $val['fab_construction'];
			// 	$fb_determination = $val['determination_id'];
			// 	$fabric_composition = $val['fab_composition'];
			// 	$fabric_gsm = $val['fabric_gsm'];
			// 	$finish_type = $val['finish_type'];
			// 	$wash_type = $val['wash_type'];
			// 	$print_type = $val['print_type'];
			// 	$floor = $val['floor_id'];
			// 	$room = $val['room'];
			// 	$rack = $val['rack'];
			// 	$shelf = $val['shelf'];
			// 	$bin = $val['bin'];
			// 	$sample_ref_type = $val['sample_ref_type'];
			// 	$system_number = $val['system_number'];
			// 	$status = $val['status_active'];
			// }
			// unset($sql_prod_res);
			// //echo '<pre>';print_r($product_data_arr);die;
			
			// $duplicate_product_arr=array();
			// $duplicate_dyesproduct_arr=array();
			$row_num_excel=1;
			$flag=1;
			$is_excel_insert=1;
			$is_excel_update=2;

			// Validation Part
			foreach($all_data_arr as $column_val)
			{
				// $company_id=$company_library[$column_val[1]['company']];
				// $buyer_name=$buyer_name_arr[$column_val[2]['buyer']];
				// $dispo_no=$column_val[3]['dispo_no'];
				// $fabric_type=$column_val[4]['fabric_type'];
				// $finish_width=$column_val[5]['finish_width'];
				// $fabric_construction=$column_val[6]['fabric_construction'];
				// $fabric_composition=$column_val[7]['fabric_composition'];
				// $fabric_gsm=$column_val[8]['fabric_gsm'];
				// $finish_type=$finish_types[$column_val[9]['finish_type']];
				// $wash_type=$emblishment_wash_typeArr[$column_val[10]['wash_type']];
				// $print_type=$emblishment_print_typeArr[$column_val[11]['print_type']];
				// $sample_ref_type=$sample_ref_typesArr[$column_val[12]['sample_ref_type']];
				// $floor=$floor_arr[$column_val[13]['floor']];
				// $room=$room_arr[$column_val[14]['room']];
				// $rack=$rack_arr[$column_val[15]['rack']];
				// $shelf=$shelf_arr[$column_val[16]['shelf']];
				// $status='Active';
				// $status_id=1;	
				
				$row_num_excel++;
				$company_id=$company_library[$column_val[1]['company']];
				if ($column_val[1]['company']=="" || $company_id==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Company Name ['.$column_val[1]["company"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				$buyer_name=$buyer_name_arr[$column_val[2]['buyer']];			
				if ($column_val[2]['buyer']=="" || $buyer_name==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Buyer field ['.$column_val[2]["buyer"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				$dispo_no=$column_val[3]['dispo_no'];
				if ($column_val[3]['dispo_no']=="" || $dispo_no==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Dispo no ['.$column_val[3]["dispo_no"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
					 
				$fabric_type=$lib_yarn_count_fab_type_deterArr[$column_val[4]['fabric_type']];
				if ($column_val[4]['fabric_type']=="" || $fabric_type==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Fabric type ['.$column_val[4]["fabric_type"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				$finish_width=$column_val[5]['finish_width'];
				if ($column_val[5]['finish_width']=="" || $finish_width==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Finish width ['.$column_val[5]["finish_width"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				$fabric_construction=$lib_yarn_count_fab_deterArr[$column_val[6]['fabric_construction']];
				if ($column_val[6]['fabric_construction']=="" || $fabric_construction==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Fabric construction ['.$column_val[6]["fabric_construction"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				$fabric_composition=$lib_yarn_count_fab_compositionArr[$column_val[7]['fabric_composition']];
				if ($column_val[7]['fabric_composition']=="" || $fabric_composition==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Fabric composition ['.$column_val[7]["fabric_composition"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				$fabric_gsm=$column_val[8]['fabric_gsm'];
				if ($column_val[8]['fabric_gsm']=="" || $fabric_gsm==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Fabric gsm ['.$column_val[8]["fabric_gsm"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				$finish_type=$column_val[9]['finish_type'];
				if ($column_val[9]['finish_type']=="" || $finish_type==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Finish type ['.$column_val[9]["finish_type"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				$wash_type=$column_val[10]['wash_type'];
				if ($column_val[10]['wash_type']=="" || $wash_type==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Wash type ['.$column_val[10]["wash_type"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				$print_type=$column_val[11]['print_type'];
				if ($column_val[11]['print_type']=="" || $print_type==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Print type ['.$column_val[11]["print_type"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				$sample_ref_type=$column_val[12]['sample_ref_type'];
				if ($column_val[12]['sample_ref_type']=="" || $sample_ref_type==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Sample ref type ['.$column_val[12]["sample_ref_type"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				$floor=$floor_arr[$column_val[13]['floor']];
				if ($column_val[13]['floor']=="" || $floor==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Floor ['.$column_val[13]["floor"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				$room=$room_arr[$column_val[14]['room']];
				if ($column_val[14]['room']=="" || $room==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Room ['.$column_val[14]["room"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				$rack=$rack_arr[$column_val[15]['rack']];
				if ($column_val[15]['rack']=="" || $rack==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Rack ['.$column_val[15]["rack"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				$shelf=$shelf_arr[$column_val[16]['shelf']];
				if ($column_val[16]['shelf']=="" || $shelf==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Shelf ['.$column_val[16]["shelf"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				$bin=$bin_arr[$column_val[17]['bin']];
				if ($column_val[17]['bin']=="" || $bin==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Bin ['.$column_val[17]["bin"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				$status=$column_val[18]['status'];
				if ($column_val[18]['status']=="" || $status==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Status ['.$column_val[18]["status"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
			}

				$row_num_excel=1; $add_comma=1;$k=1;
				foreach($all_data_arr as $column_val)
				{
						$row_num_excel++;

						$company_id=$company_library[$column_val[1]['company']];
						$buyer_name=$buyer_name_arr[$column_val[2]['buyer']];
						$dispo_no=$column_val[3]['dispo_no'];
						$fabric_type=$lib_yarn_count_fab_type_deterArr[$column_val[4]['fabric_type']];
						$finish_width=$column_val[5]['finish_width'];
						$fabric_construction=$lib_yarn_count_fab_deterArr[$column_val[6]['fabric_construction']];
						$fabric_composition=$lib_yarn_count_fab_compositionArr[$column_val[7]['fabric_composition']];
						$fabric_gsm=$column_val[8]['fabric_gsm'];
						$finish_type=$finish_types[$column_val[9]['finish_type']];
						$wash_type=$emblishment_wash_typeArr[$column_val[10]['wash_type']];
						$print_type=$emblishment_print_typeArr[$column_val[11]['print_type']];
						$sample_ref_type=$sample_ref_typesArr[$column_val[12]['sample_ref_type']];
						$floor=$floor_arr[$column_val[13]['floor']];
						$room=$room_arr[$column_val[14]['room']];
						$rack=$rack_arr[$column_val[15]['rack']];
						$shelf=$shelf_arr[$column_val[16]['shelf']];
						$bin=$bin_arr[$column_val[17]['bin']];
						$status='Active';
						$status_id=1;	
						
						$duplicate_dyesprod_keys=trim($column_val[1]['company'])."**".trim($column_val[2]['buyer'])."**".trim($column_val[3]['dispo_no'])."**".trim($column_val[4]['fabric_type'])."**".trim($column_val[5]['finish_width'])."**".trim($fabric_construction)."**".trim($fabric_composition)."**".trim($fabric_gsm)."**".trim($finish_type)."**".trim($wash_type)."**".trim($print_type)."**".trim($sample_ref_type)."**".trim($column_val[13]['floor'])."**".trim($column_val[14]['room'])."**".trim($column_val[15]['rack'])."**".trim($column_val[16]['shelf'])."**".trim($column_val[17]['bin'])."**".trim($status);
								$duplicate_product_arr[]=$duplicate_dyesprod_keys;

					// if ($stock_qty != '' && $stock_qty != '0')  
					// {

									
									if($db_type==0) $year_cond="YEAR(insert_date)";
									else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
									else $year_cond="";//defined Later
									$date_field=date('d-M-Y');
									$id=return_next_id( "id", "wo_fabric_hanger_archive_mst", 0 );
									$field_array="id,system_number_prefix, system_number_prefix_num, system_number,company_id,buyer_id,dispo_no,fabric_hanger_date,location_id,fabric_type,finish_width,fab_construction,fab_composition,determination_id,fabric_gsm,finish_type,wash_type,print_type,floor_id,room,rack,shelf,bin,sample_ref_type,inserted_by,insert_date,status_active,is_deleted";
									$new_return_number=explode("*",return_mrr_number( str_replace("'","",$company_id), '', 'FHA', date("Y",time()), 5, "select system_number_prefix,system_number_prefix_num from wo_fabric_hanger_archive_mst where company_id=$company_id and $year_cond=".date('Y',time())." order by id DESC", "system_number_prefix", "system_number_prefix_num" ));

									
									if($fabric_type=="") $fabric_type=0;
										if($finish_type=="") $finish_type=0;
										if($print_type=="") $print_type=0;
										if($floor=="") $floor=0;
										if($room=="") $room=0;
										if($rack=="") $rack=0;
										if($shelf=="") $shelf=0;
										if($sample_ref_type=="") $sample_ref_type=0;
										if($buyer_name=="") $buyer_name=0;
										if($company_id=="") $company_id=0;
										if($dispo_no=="") $dispo_no=0;
										if($finish_width=="") $finish_width=0;
										if($fabric_gsm=="") $fabric_gsm=0;
										if($fabric_construction=="") $fabric_construction=0;
										if($fabric_composition=="") $fabric_composition=0;
										if($wash_type=="") $wash_type=0;
										if($bin=="") $bin=0;
										if($status_id=="") $status_id=0;

										$new_return_no=$new_return_number[2];
										$new_return_no_full=$new_return_number[0];

									// if ($k==1) 
									//  {
									// 	//$new_return_no;
									// 	$new_return_no=$new_return_number[2];
									// 	$new_return_no_full=$new_return_number[0];
									// 	$k++;
									//  }
									// else {
									// 	$new_return_no=$new_return_no+1;
									// 	$new_return_no_full=$new_return_no_full+1;
									// } 
									if ($add_comma!=1) $data_array .=",";
									$data_array.="(".$id.",'".$new_return_number[1]."','".$new_return_no."','".$new_return_no_full."',".$company_id.",".$buyer_name.",'".$dispo_no."','".$date_field."',0,'".$fabric_type."','".$finish_width."','".$fabric_construction."','".$fabric_composition."',0,'".$fabric_gsm."',".$finish_type.",".$wash_type.",".$print_type.",".$floor.",".$room.",".$rack.",".$shelf.",".$bin.",".$sample_ref_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$status_id."',0)"; 

									$add_comma++; 
									$id=$id+1;
									 //echo "insert into wo_fabric_hanger_archive_mst($field_array)values".$data_array; 


									 
							
							
					 
				}
				if ($data_array!="")
		 		{
					 
					 $rID=sql_insert("wo_fabric_hanger_archive_mst",$field_array,$data_array,1);
					 if($rID==1) $flag=1;else $flag=0;
				}
				//echo "10**=".$flag;die;
				//print_r($duplicate_dyesproduct_arr);die;
				
				
			 
			// echo '<pre>';print_r($duplicate_product_arr); 	
			//echo "10**$count";oci_rollback($con);disconnect($con);die;
			$all_datas='';
			if (!empty($duplicate_product_arr))
			{
				$all_datas.='<div style="width:100%">';
				$all_datas.='<table border="0" class="rpt_table" rules="all" width="2020" cellpadding="0" cellspacing="0" style="font-size: 16px;">
						<tr>
							<td colspan="10"><strong> Item List:</strong></td>
						</tr>
					</table>';
				$all_datas.='<table border="1" class="rpt_table" rules="all" width="2020" cellpadding="0" cellspacing="0" style="background-color:#bbb; font-size: 14px;">
						<tr>
							<th width="30">SL</th>
							<th width="130">Company</th>
							<th width="120">Buyer Name</th>
							<th width="120">Dispo No</th>
							<th width="120">Fabric Type</th>
							<th width="100">Finish Width</th>
							<th width="150">Fabric Construction</th>
							<th width="150">Fabric Composition</th>
							<th width="100">Fabric GSM</th>
							<th width="100">Finish Type</th>
							<th width="100">Wash Type</th>
							<th width="100">Print Type</th>
							<th width="100">Sample Ref Type</th>
							<th width="100">Floor</th>
							<th width="100">Room</th>
							<th width="100">Rack</th>
							<th width="100">Shelf</th>
							<th width="100">Bin</th>
							<th width="100">Status</th>
						</tr>
					</table>
					<table border="1" class="rpt_table" rules="all" width="2020" cellpadding="0" cellspacing="0" style="font-size: 14px;">';
						$i=1;					
						foreach ($duplicate_product_arr as $value)
						{
							$exp_val=explode('**', $value);	
							//$duplicate_dyesprod_keys=trim($column_val[1]['company'])."**".trim($column_val[2]['buyer'])."**".trim($column_val[3]['dispo_no'])."**".trim($column_val[4]['fabric_type'])."**".trim($column_val[5]['finish_width'])."**".trim($fabric_construction)."**".trim($fabric_composition)."**".trim($fabric_gsm)."**".trim($finish_type)."**".trim($wash_type)."**".trim($print_type)."**".trim($sample_ref_type)."**".trim($floor)."**".trim($room)."**".trim($rack)."**".trim($shelf)."**".trim($status);
							$all_datas.='<tr>
								<td width="30">'.$i.'</td>
								<td width="130" style="word-break:break-all">'.$exp_val[0].'</td>
								<td width="120" style="word-break:break-all">'.$exp_val[1].'</td>
								<td width="120" style="word-break:break-all">'.$exp_val[2].'</td>
								<td width="120" style="word-break:break-all">'.$exp_val[3].'</td>
								<td width="100" style="word-break:break-all">'.$exp_val[4].'</td>
								<td width="150" style="word-break:break-all">'.$exp_val[5].'</td>
								<td width="150" style="word-break:break-all">'.$exp_val[6].'</td>
								<td width="100" style="word-break:break-all">'.$exp_val[7].'</td>
								<td width="100" style="word-break:break-all">'.$exp_val[8].'</td>
								<td width="100" style="word-break:break-all">'.$exp_val[9].'</td>
								<td width="100" style="word-break:break-all">'.$exp_val[10].'</td>
								<td width="100" style="word-break:break-all">'.$exp_val[11].'</td>
								<td width="100" style="word-break:break-all">'.$exp_val[12].'</td>
								<td width="100" style="word-break:break-all">'.$exp_val[13].'</td>
								<td width="100" style="word-break:break-all">'.$exp_val[14].'</td>
								<td width="100" style="word-break:break-all">'.$exp_val[15].'</td>
								<td width="100" style="word-break:break-all">'.$exp_val[16].'</td>
								<td width="100" style="word-break:break-all">'.$exp_val[17].'</td>
								
							</tr>';
							$i++;
						}
					$all_datas.='</table></div>';
			}
			// echo "10**$flag";oci_rollback($con);die;
			// echo "10** insert into inv_receive_master ($field_array1) values $data_array1";
			//echo "10**$flag*******$flg insert into inv_transaction ($field_array_trams) values $data_array_trans";oci_rollback($con);die;

			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File Upload Successfully...</p><br/>';
					echo $all_datas;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File is not Uploaded...</p><br/>';
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File Upload Successfully...</p><br/>';
					echo $all_datas;
				}
				else
				{
					oci_rollback($con);
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File is not Uploaded...</p><br/>';		
				}
			}
			disconnect($con);
			die;
		}
		else
		{
			echo "Failed";	
		}
		die;
	}
?>	