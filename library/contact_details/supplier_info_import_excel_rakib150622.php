<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
//echo '<pre>';print_r($_SESSION);
$permission=$_SESSION['page_permission'];

echo load_html_head_contents("Item Import","../../", 1, 1, $unicode,1,'');

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

	$source = $_FILES['uploadfile']['tmp_name'];
	$targetzip ='files/'.$_FILES['uploadfile']['name'];
	$file_name=$_FILES['uploadfile']['name'];
    //echo $source.'**'.$targetzip.'**'.$file_name;die;
	unset($_SESSION['excel']);
	if (move_uploaded_file($source, $targetzip)) 
	{
		$excel = new Spreadsheet_Excel_Reader($targetzip);
		$card_colum=0; $m=1; 
		$all_data_arr=array(); 

		for ($i = 1; $i <= $excel->sheets[0]['numRows']; $i++) 
		{
			if($m==1)
			{
				for ($j = 1; $j <= $excel->sheets[0]['numCols']; $j++) 
				{
					//$k++;
					//echo "\"".$data->sheets[0]['cells'][$i][4]."\",";
					//$card_colum=$excel->sheets[0]['cells'][$i][$j];
					
					//echo $card_colum.'=='.$i.'=='.$j.'<br>';
					/*$date_fld2=$data->sheets[0]['cells'][$i][$date_fld];
					$in_out_time=$data->sheets[0]['cells'][$i][$time_fld_len[0]].",".$data->sheets[0]['cells'][$i][$time_fld_len[1]];*/
					//print_r($in_out_time_arr);
					//$date_time_colum=$data->sheets[0]['cells'][$i][4];
				}
				$m++;
			}
			else
			{
				$supplier_name=$short_name=$contact_person=$designation=$contact_no=$email=$http_www=$address1='';
				$address2=$address3=$address4=$country=$supplier_type=$tag_company=$link_to_buyer=$credit_limit_days='';
				$credit_limit_amount=$currancy=$discount_method=$security_deducted=$vat_to_be_deducted=$ait_to_be_deducted='';
				$remark=$individual=$supplier_nature=$status=$tag_buyer=$supplier_ref=$owner_info=$tin_number=$vat_number='';
				//echo '<pre>';print_r($excel->sheets[0]['cells'][$i]);die;
				
				$str_rep=array("*",  "=", "\r", "\n", "#");
				//$str_rep= preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $excel->sheets[0]['cells'][$i]); 
				if (isset($excel->sheets[0]['cells'][$i][1])) $supplier_name = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][1]));
				if (isset($excel->sheets[0]['cells'][$i][2])) $short_name = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][2]));
				if (isset($excel->sheets[0]['cells'][$i][3])) $contact_person = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][3]));
				if (isset($excel->sheets[0]['cells'][$i][4])) $designation = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][4]));
				if (isset($excel->sheets[0]['cells'][$i][5])) $contact_no = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][5]));
				if (isset($excel->sheets[0]['cells'][$i][6])) $email = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][6]));
				if (isset($excel->sheets[0]['cells'][$i][7])) $http_www  = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][7]));
				if (isset($excel->sheets[0]['cells'][$i][8])) $address1 = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][8]));
				if (isset($excel->sheets[0]['cells'][$i][9])) $address2= trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][9]));
				if (isset($excel->sheets[0]['cells'][$i][10])) $address3 = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][10]));
				if (isset($excel->sheets[0]['cells'][$i][11])) $address4 = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][11]));
				if (isset($excel->sheets[0]['cells'][$i][12])) $country = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][12]));
				if (isset($excel->sheets[0]['cells'][$i][13])) $supplier_type = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][13]));
				if (isset($excel->sheets[0]['cells'][$i][14])) $tag_company = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][14]));
				if (isset($excel->sheets[0]['cells'][$i][15])) $link_to_buyer = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][15]));
				if (isset($excel->sheets[0]['cells'][$i][16])) $credit_limit_days = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][16]));
				if (isset($excel->sheets[0]['cells'][$i][17])) $credit_limit_amount = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][17]));
				if (isset($excel->sheets[0]['cells'][$i][18])) $currancy = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][18]));
				if (isset($excel->sheets[0]['cells'][$i][19])) $discount_method = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][19]));
				if (isset($excel->sheets[0]['cells'][$i][20])) $security_deducted = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][20]));
				if (isset($excel->sheets[0]['cells'][$i][21])) $vat_to_be_deducted = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][21]));
				if (isset($excel->sheets[0]['cells'][$i][22])) $ait_to_be_deducted = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][22]));
				if (isset($excel->sheets[0]['cells'][$i][23])) $remark = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][23]));
				if (isset($excel->sheets[0]['cells'][$i][24])) $individual = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][24]));
				if (isset($excel->sheets[0]['cells'][$i][25])) $supplier_nature = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][25]));
				if (isset($excel->sheets[0]['cells'][$i][26])) $status = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][26]));
				if (isset($excel->sheets[0]['cells'][$i][27])) $tag_buyer = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][27]));			
				if (isset($excel->sheets[0]['cells'][$i][28])) $supplier_ref = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][28]));		
				if (isset($excel->sheets[0]['cells'][$i][29])) $owner_info = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][29]));		
				if (isset($excel->sheets[0]['cells'][$i][30])) $tin_number = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][30]));		
				if (isset($excel->sheets[0]['cells'][$i][31])) $vat_number = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][31]));	

				$all_data_arr[$i][1]['supplier_name']=$supplier_name;
				$all_data_arr[$i][2]['short_name']=$short_name;
				$all_data_arr[$i][3]['contact_person']=$contact_person;
				$all_data_arr[$i][4]['designation']=$designation;
				$all_data_arr[$i][5]['contact_no']=$contact_no;
				$all_data_arr[$i][6]['email']=$email;
				$all_data_arr[$i][7]['http_www']=$http_www;
				$all_data_arr[$i][8]['address1']=$address1;
				$all_data_arr[$i][9]['address2']=$address2;
				$all_data_arr[$i][10]['address3']=$address3;
				$all_data_arr[$i][11]['address4']=$address4;
				$all_data_arr[$i][12]['country']=$country;
				$all_data_arr[$i][13]['supplier_type']=$supplier_type;
				$all_data_arr[$i][14]['tag_company']=$tag_company;
				$all_data_arr[$i][15]['link_to_buyer']=$link_to_buyer;
				$all_data_arr[$i][16]['credit_limit_days']=$credit_limit_days;
				$all_data_arr[$i][17]['credit_limit_amount']=$credit_limit_amount;
				$all_data_arr[$i][18]['currancy']=$currancy;
				$all_data_arr[$i][19]['discount_method']=$discount_method;
				$all_data_arr[$i][20]['security_deducted']=$security_deducted;
				$all_data_arr[$i][21]['vat_to_be_deducted']=$vat_to_be_deducted;
				$all_data_arr[$i][22]['ait_to_be_deducted']=$ait_to_be_deducted;
				$all_data_arr[$i][23]['remark']=$remark;
				$all_data_arr[$i][24]['individual']=$individual;
				$all_data_arr[$i][25]['supplier_nature']=$supplier_nature;
				$all_data_arr[$i][26]['status']=$status;
				$all_data_arr[$i][27]['tag_buyer']=$tag_buyer;
				$all_data_arr[$i][28]['supplier_ref']=$supplier_ref;
				$all_data_arr[$i][29]['owner_info']=$owner_info;
				$all_data_arr[$i][30]['tin_number']=$tin_number;
				$all_data_arr[$i][31]['vat_number']=$vat_number;
			
			}
		}

		//echo '<pre>';print_r($all_data_arr);die;
		?>
		<script>
		var permission='<? echo $permission; ?>';			
		if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
		function fnc_excel_import(operation) 
		{
			//freeze_window(operation);
			var row_num = $('#table_body tr').length;
			var data_all = "";
			for (var i=1; i<=row_num; i++)
			{

				var supplier_name=$("#supplier_name_"+i).attr('title');
				var short_name=$("#short_name_"+i).attr('title');
				var supplier_type=$("#supplier_type_"+i).attr('title');
				var tag_company=$("#tag_company_"+i).attr('title');

				if ( supplier_name=='' || short_name=='' || supplier_type=='' || tag_company=='' )
				{
					alert('Plz Fill Up Mandatory Field!!');
					return;
				}

				data_all += '&supplier_name_' + i + '=' + encodeURIComponent(trim($('#supplier_name_'+i).attr('title'))) + '&short_name_' + i + '=' + encodeURIComponent(trim($('#short_name_'+i).attr('title'))) + '&contact_person_' + i + '=' + encodeURIComponent(trim($('#contact_person_'+i).attr('title'))) + '&designation_' + i + '=' + encodeURIComponent(trim($('#designation_'+i).attr('title'))) + '&contact_no_' + i + '=' + trim($('#contact_no_'+i).attr('title')) + '&email_' + i + '=' + encodeURIComponent(trim($('#email_'+i).attr('title'))) + '&http_www_' + i + '=' + encodeURIComponent(trim($('#http_www_'+i).attr('title'))) + '&address1_' + i + '=' + encodeURIComponent(trim($('#address1_'+i).attr('title'))) + '&address2_' + i + '=' + encodeURIComponent(trim($('#address2_'+i).attr('title'))) + '&address3_' + i + '=' + encodeURIComponent(trim($('#address3_'+i).attr('title'))) + '&address4_' + i + '=' + encodeURIComponent(trim($('#address4_'+i).attr('title'))) + '&country_' + i + '=' + encodeURIComponent(trim($('#country_'+i).attr('title'))) + '&supplier_type_' + i + '=' + trim($('#supplier_type_'+i).attr('title')) + '&tag_company_' + i + '=' + trim($('#tag_company_'+i).attr('title')) + '&link_to_buyer_' + i + '=' + trim($('#link_to_buyer_'+i).attr('title')) + '&credit_limit_days_' + i + '=' + trim($('#credit_limit_days_'+i).attr('title')) + '&credit_limit_amount_' + i + '=' + trim($('#credit_limit_amount_'+i).attr('title')) + '&currancy_' + i + '=' + trim($('#currancy_'+i).attr('title')) + '&discount_method_' + i + '=' + trim($('#discount_method_'+i).attr('title')) + '&security_deducted_' + i + '=' + trim($('#security_deducted_'+i).attr('title')) + '&vat_to_be_deducted_' + i + '=' + trim($('#vat_to_be_deducted_'+i).attr('title')) + '&ait_to_be_deducted_' + i + '=' + trim($('#ait_to_be_deducted_'+i).attr('title')) + '&remark_' + i + '=' + encodeURIComponent(trim($('#remark_'+i).attr('title'))) + '&individual_' + i + '=' + trim($('#individual_'+i).attr('title')) + '&supplier_nature_' + i + '=' + trim($('#supplier_nature_'+i).attr('title')) + '&status_' + i + '=' + trim($('#status_'+i).attr('title')) + '&tag_buyer_' + i + '=' + trim($('#tag_buyer_'+i).attr('title')) + '&supplier_ref_' + i + '=' + encodeURIComponent(trim($('#supplier_ref_'+i).attr('title'))) + '&owner_info_' + i + '=' + encodeURIComponent(trim($('#owner_info_'+i).attr('title'))) + '&tin_number_' + i + '=' + encodeURIComponent(trim($('#tin_number_'+i).attr('title'))) + '&vat_number_' + i + '=' + encodeURIComponent(trim($('#vat_number_'+i).attr('title')));
				
			}

			var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all;
			//alert(data);return;
			freeze_window(operation);	
			http.open("POST","requires/supplier_info_import_excel_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_excel_import_reponse;
	
		}

		function fnc_excel_import_reponse()
		{
			if(http.readyState == 4) 
			{
				var reponse=trim(http.responseText).split('**');
				//alert (reponse);
				alert(reponse[1]);
				release_freezing();
			}
		}

		</script>    	
		</html>
		</head>
      	<body onLoad="set_hotkey();">
      	<div style="width:100%;" align="left">
      		<!-- Important Field outside Form -->  
      		<?  echo load_freeze_divs ("../../",$permission, 1, ''); ?>
      		<fieldset style="width:3410px;">
      		<form name="excelImport_1" id="excelImport_1" autocomplete="off"> 
      			<table width="3390" cellspacing="0" border="1" class="rpt_table" rules="all" id="">      
			        <thead>
			            <tr>
			                <th width="120" class="must_entry_caption">Supplier Name</th>
			                <th width="120" class="must_entry_caption">Short Name</th>
			                <th width="100">Contact Person</th>
			                <th width="100">Designation</th>
			                <th width="100">Contact No</th>
			                <th width="100">Email</th>
			                <th width="120">http://www.</th>
			                <th width="120">Address1</th>

			                <th width="120">Address2</th>
			                <th width="120">Address3</th>
			                <th width="120">Address4</th>
			                <th width="100">Country</th>
			                <th width="150" class="must_entry_caption">Supplier Type</th>
			                <th width="150" class="must_entry_caption">Tag Company</th>			               
			                <th width="100">Link to Buyer</th>
			                <th width="100">Credit Limit (Days)</th>

			                <th width="100">Credit Limit (Amount)</th>
			                <th width="100">Currancy</th>
			                <th width="100">Discount Method</th>
			                <th width="100">Security deducted</th>
			                <th width="100">VAT to be deducted</th>
			                <th width="100">AIT to be deducted</th>			                
			                <th width="150">Remark</th>
			                <th width="100">Individual</th>

			                <th width="100">Supplier Nature</th>
			                <th width="100">Status</th>
			                <th width="100">Tag Buyer</th>	
			                <th width="100">Supplier Ref.</th>	
			                <th width="100">Owner Info</th>	
			                <th width="100">TIN Number</th>	
			                <th width="100">VAT Number</th>
			            </tr>
			        </thead>
			    </table>
			    <div style="width:3410px; overflow-y:scroll; max-height:500px" id="scroll_body">
			    	<table width="3390" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body"> 
			        <tbody>
			        	<?
			        	$company_library=return_library_array("select company_name, id from lib_company where status_active=1 and is_deleted=0","company_name","id");
			        	$buyer_library=return_library_array("select buyer_name, id from lib_buyer where status_active=1 and is_deleted=0","buyer_name","id");
			        	$party_type_supplier_arr = array_flip($party_type_supplier);
			        	$i=1;
			        	//die;
			        	foreach ($all_data_arr as $row_index => $column_val) 
			        	{
				        	?>
	            			<tr id="tr_<?= $i; ?>">
	            				<td width="120" id="supplier_name_<?= $i; ?>" title="<?= $column_val[1]['supplier_name']; ?>"><? echo $column_val[1]['supplier_name']; ?></td>
				                <td width="120" id="short_name_<?= $i; ?>" title="<?= $column_val[2]['short_name']; ?>"><? echo $column_val[2]['short_name']; ?></td>
				                <td width="100" id="contact_person_<?= $i; ?>" title="<?= $column_val[3]['contact_person']; ?>"><? echo $column_val[3]['contact_person']; ?></td>
				                <td width="100" id="designation_<?= $i; ?>" title="<?= $column_val[4]['designation']; ?>"><? echo $column_val[4]['designation']; ?></td>
				                <td width="100" id="contact_no_<?= $i; ?>" title="<?= $column_val[5]['contact_no']; ?>"><? echo $column_val[5]['contact_no']; ?></td>
				                <td width="100" id="email_<?= $i; ?>" title="<?= $column_val[6]['email']; ?>"><? echo $column_val[6]['email']; ?></td>
				                <td width="120" id="http_www_<?= $i; ?>" title="<?= $column_val[7]['http_www']; ?>"><? echo $column_val[7]['http_www']; ?></td>
				                <td width="120" id="address1_<?= $i; ?>" title="<?= $column_val[8]['address1']; ?>"><? echo $column_val[8]['address1']; ?></td>

				                <td width="120" id="address2_<?= $i; ?>" title="<?= $column_val[9]['address2']; ?>"><? echo $column_val[9]['address2']; ?></td>
				                <td width="120" id="address3_<?= $i; ?>" title="<?= $column_val[10]['address3']; ?>"><? echo $column_val[10]['address3']; ?></td>
				                <td width="120" id="address4_<?= $i; ?>" title="<?= $column_val[11]['address4']; ?>"><? echo $column_val[11]['address4']; ?></td>
				                <td width="100" align="center" id="country_<?= $i; ?>" title="<?= $column_val[12]['country']; ?>"><? echo $column_val[12]['country']; ?></td>
				                <?
				                $supplier_type_ids='';				                
				                $exp_supplier_type=explode(',', $column_val[13]['supplier_type']);
								$supplier_type_id='';
								foreach ($exp_supplier_type as $supp_type) {
									$supplier_type_id .= $party_type_supplier_arr[$supp_type].',';
								}
								$supplier_type_ids=rtrim($supplier_type_id,',');
				                ?>
				                <td width="150" id="supplier_type_<?= $i; ?>" title="<?= $supplier_type_ids; ?>"><? echo $column_val[13]['supplier_type']; ?></td>
				                <?
				                $tag_company_ids='';
				                if ($column_val[14]['tag_company'] != '')
				                {
				                	$tag_company_id='';
				                	$exp_tag_company=explode(',', $column_val[14]['tag_company']);
									foreach ($exp_tag_company as $tagCompany) {
										$tag_company_id .= $company_library[$tagCompany].',';
									}
									$tag_company_ids=rtrim($tag_company_id,',');
				                }
				                else
				                {
				                	$tag_company_ids = implode(",",$company_library);
				                }
				                ?>
				                <td width="150" id="tag_company_<?= $i; ?>" title="<?= $tag_company_ids; ?>"><? echo $column_val[14]['tag_company']; ?>&nbsp;</td>					               
				                <td width="100" id="link_to_buyer_<?= $i; ?>" title="<?= $column_val[15]['link_to_buyer']; ?>"><? echo $column_val[15]['link_to_buyer']; ?></td>
				                <td width="100" align="right" id="credit_limit_days_<?= $i; ?>" title="<?= $column_val[16]['credit_limit_days']; ?>"><? echo $column_val[16]['credit_limit_days']; ?></td>


				                <td width="100" align="right" id="credit_limit_amount_<?= $i; ?>" title="<?= $column_val[17]['credit_limit_amount']; ?>"><? echo $column_val[17]['credit_limit_amount']; ?></td>
				                <td width="100" align="center" id="currancy_<?= $i; ?>" title="<?= $column_val[18]['currancy']; ?>"><? echo $column_val[18]['currancy']; ?></td>
				                <td width="100" align="center" id="discount_method_<?= $i; ?>" title="<?= $column_val[19]['discount_method']; ?>"><? echo $column_val[19]['discount_method']; ?></td>
				                <td width="100" align="center" id="security_deducted_<?= $i; ?>" title="<?= $column_val[20]['security_deducted']; ?>"><? echo $column_val[20]['security_deducted']; ?></td>
				                <td width="100" align="center" id="vat_to_be_deducted_<?= $i; ?>" title="<?= $column_val[21]['vat_to_be_deducted']; ?>"><? echo $column_val[21]['vat_to_be_deducted']; ?></td>
				                <td width="100" align="center" id="ait_to_be_deducted_<?= $i; ?>" title="<?= $column_val[22]['ait_to_be_deducted']; ?>"><? echo $column_val[22]['ait_to_be_deducted']; ?>&nbsp;</td>              
				                <td width="150" id="remark_<?= $i; ?>" title="<?= $column_val[23]['remark']; ?>"><? echo $column_val[23]['remark']; ?></td>
				                <td width="100" align="center" id="individual_<?= $i; ?>" title="<?= $column_val[24]['individual']; ?>"><? echo $column_val[24]['individual']; ?></td>


				                <td width="100" id="supplier_nature_<?= $i; ?>" title="<?= $column_val[25]['supplier_nature']; ?>"><? echo $column_val[25]['supplier_nature']; ?></td>
				                <td width="100" id="status_<?= $i; ?>" title="<?= $column_val[26]['status']; ?>"><? echo $column_val[26]['status']; ?></td>
				                <?
				                $tag_buyer_ids='';
				                $tag_buyer_id='';
				                if ($column_val[27]['tag_buyer'] != '')
				                {
				                	$exp_tag_buyer=explode(',', $column_val[27]['tag_buyer']);
									foreach ($exp_tag_buyer as $tagBuyer) {
										$tag_buyer_id .= $buyer_library[$tagBuyer].',';
									}
									$tag_buyer_ids=rtrim($tag_buyer_id,',');
				                }
				                ?>
				                <td width="100" id="tag_buyer_<?= $i; ?>" title="<?= $tag_buyer_ids; ?>"><? echo $column_val[27]['tag_buyer']; ?></td>  
				                <td width="100" id="supplier_ref_<?= $i; ?>" title="<?= $column_val[28]['supplier_ref']; ?>"><? echo $column_val[28]['supplier_ref']; ?></td>  
				                <td width="100" id="owner_info_<?= $i; ?>" title="<?= $column_val[29]['owner_info']; ?>"><? echo $column_val[29]['owner_info']; ?></td>  
				                <td width="100" id="tin_number_<?= $i; ?>" title="<?= $column_val[30]['tin_number']; ?>"><? echo $column_val[30]['tin_number']; ?></td>  
				                <td width="100" id="vat_number_<?= $i; ?>" title="<?= $column_val[31]['vat_number']; ?>"><? echo $column_val[31]['vat_number']; ?></td>  
				            </tr>
				            <?
				            $i++;
					    }
					    ?>           
            		</tbody>
            	</table>
            	<table width="1300">
	            	<tr style="border:none">
	                    <td align="center" colspan="7" class="button_container">
	                        <input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Save" onClick="fnc_excel_import(0);" />
	                    </td>
	                </tr>
	           </table>
            </div>
            </form>
        	</fieldset>
        </div>
        </body>
        <script src="../../includes/functions_bottom.js" type="text/javascript"></script>			
        <?
	}
	else
	{
		echo "Failed";	
	}
	die;
}
?>
