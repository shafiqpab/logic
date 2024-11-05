<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------- Start-------------------------------------//
if($db_type==0) $select_field="group";
else if($db_type==2) $select_field="wm";
else $select_field="";//defined Later

if ($action=="lcsc_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$data=explode("_",$data);
	$lcsc_id=$data[0];
	$lcsc_type=$data[1];
	$type=$data[2];
    ?>

	<script>

		function fn_show_check()
		{
 			show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $beneficiary; ?>+'_'+'<? echo $all_lc_sc; ?>', 'create_lcsc_search_list_view', 'search_div', 'pre_export_finance_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
 			$("#lc_or_sc").val(document.getElementById('cbo_search_by').value);	
		}

		var selected_id = new Array();

		
		var currencyID="";
		function js_set_value(str, currency, id) 
		{
			if(currencyID=="")
			{
				currencyID=currency;
				$("#hid_currency_name").val(currencyID);
			}					

			$('#lcsc_id').val( id );
			show_lcsc_wise_entry();
 		}


		function fn_total()
		{
			//total sum
			var totalRow = $("#tbl_list_search tr").length-1;
 			math_operation("total_sum_amount", "txtAmount_", "+", totalRow);
		}

		function show_lcsc_wise_entry()
		{
 			var lcsc_id=$('#lcsc_id').val();
			if($('#cbo_search_by').val()==1) var etype=1; else var etype=2;
			show_list_view ( lcsc_id+'_'+etype+'_'+'1', 'lcsc_popup', 'search_div', 'pre_export_finance_controller', '');
 		}

		function hidden_field_reset()
		{
			$('#all_lcsc_id').val('');
			$('#save_string').val('');
			$('#tot_amount').val('');
			selected_id = new Array();
		}

		function fnc_close()
		{
			var save_string='';
			var total_amount=0;
			var all_lcsc_id='';
			$("#tbl_list_search").find('tr').each(function()
			{
				var txtLcScID=$(this).find('input[name="txtLcScID[]"]').val();
				var txtAmount=$(this).find('input[name="txtAmount[]"]').val();
				if(txtAmount*1>0)
				{
					if(save_string=="")
					{
						save_string=txtLcScID+"**"+txtAmount;
						all_lcsc_id = txtLcScID;
					}
					else
					{
						save_string+=","+txtLcScID+"**"+txtAmount;
						all_lcsc_id += ','+txtLcScID;
					}
					total_amount +=txtAmount*1;
				}

			});

			$('#save_string').val( save_string );
			$('#tot_amount').val( total_amount );
			$('#all_lcsc_id').val( all_lcsc_id );
			$('#lc_or_sc').val( $('#cbo_search_by').val() );

			parent.emailwindow.hide();
		}
    </script>
	</head>

	<body>
	<form name="searchdescfrm"  id="searchdescfrm">
	<fieldset style="width:600px;margin-left:10px">
    	<input type="hidden" name="save_string" id="save_string" value="">
        <input type="hidden" name="tot_amount" id="tot_amount" value="">
        <input type="hidden" name="all_lcsc_id" id="all_lcsc_id" value="">
        <input type="hidden" name="lc_or_sc" id="lc_or_sc" value="">

		<?
		if($type=="")
		{
		    ?>
			<input type="hidden" name="hid_currency_name" id="hid_currency_name" value="<? echo $currency_name; ?>">
	        <table cellpadding="0" cellspacing="0" width="620" class="rpt_table">
				<thead>
	 				<th>Search By</th>
					<th>Search</th>
					<th>
						<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
						<input type="hidden" name="lcsc_id" id="lcsc_id" value="">
					</th>
				</thead>
				<tr class="general">
					<td align="center">
						<?
							$search_by_arr=array(1=>"LC Number",2=>"SC Number");
							echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", $lc_or_sc,$dd,0 );
						?>
					</td>
					<td align="center">
						<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
					</td>
					<td align="center">
						<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check()" style="width:100px;"/>
					</td>
				</tr>
			</table>
			<div id="search_div" style="margin-top:10px">

	            <?
			    if($all_lc_sc!="")
				{
					?>
	                <div style="margin-left:10px; margin-top:10px; margin-left:100px">
	                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400">
	                        <thead>
	                            <th width="200">LC/SC No</th>
	                            <th width="200">Amount</th>
	                        </thead>
	                    </table>
	                    <div style="width:420px; max-height:280px; overflow-y:scroll" id="list_container" align="left">
	                        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400" id="tbl_list_search">
	                            <?
	                            $i=1;
								if($lc_or_sc==1)
								{
									$sql = "select id,export_lc_no as lcsc,buyer_name,lc_date as lcsc_date,currency_name,lc_value as lcsc_value from com_export_lc where status_active=1 and id in ($all_lc_sc)";
								}
								else
								{
									$sql = "select id,contract_no as lcsc,buyer_name,contract_date as lcsc_date,currency_name,contract_value as lcsc_value from com_sales_contract where status_active=1 and id in ($all_lc_sc)";
								}

	                            //echo $sql;
	                            $explSaveData = explode(",",$save_data);
	                            $nameArray=sql_select($sql);
	                            $totaAmt=0;
								foreach($nameArray as $row)
	                            {
	                                if ($i%2==0)
	                                    $bgcolor="#E9F3FF";
	                                else
	                                    $bgcolor="#FFFFFF";

	                                $woQnty = explode("**",$explSaveData[$i-1]);
	                                if($woQnty[0]==$row[csf('id')]) $qnty = $woQnty[1]; else $qnty = "";
	                                $totaAmt+=$qnty;
	                                ?>
	                                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
	                                    <td width="200">
	                                        <p><? echo $row[csf('lcsc')]; ?></p>
	                                        <input type="hidden" name="txtLcScID[]" id="txtLcScID_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
	                                    </td>
	                                    <td width="200" align="center">
	                                        <input type="text" name="txtAmount[]" id="txtAmount_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $qnty; ?>" onKeyUp="fn_total()" >
	                                    </td>
	                                </tr>
	                                <?
	                                $i++;
	                            }
	                            ?>
	                            <tfoot>
	                                <th>Total</th>
	                                <td align="center"><input type="text" name="total_sum_amount" id="total_sum_amount" class="text_boxes_numeric" style="width:80px" value="<? echo $totaAmt;?>"></td>
	                            </tfoot>
	                        </table>
	                    </div>
	                    <table width="400" id="table_id">
	                         <tr>
	                            <td align="center" >
	                                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px"/>
	                            </td>
	                        </tr>
	                    </table>
	                </div>
	                <?
				}
				?>
	        </div>
		    <?
		}
		else
		{
		    ?>
	 		<div style="margin-left:10px; margin-top:10px; margin-left:100px">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400">
					<thead>
						<th width="200">LC/SC No</th>
						<th width="200">Amount</th>
					</thead>
				</table>
				<div style="width:420px; max-height:280px; overflow-y:scroll" id="list_container" align="left">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400" id="tbl_list_search">
						<?
						$i=1;
						if($lcsc_type==1)
						{
							$sql = "select id,export_lc_no as lcsc,buyer_name,lc_date as lcsc_date,currency_name,lc_value as lcsc_value from com_export_lc where status_active=1 and id in ($lcsc_id)";
						}
						else
						{
							$sql = "select id,contract_no as lcsc,buyer_name,contract_date as lcsc_date,currency_name,contract_value as lcsc_value from com_sales_contract where status_active=1 and id in ($lcsc_id)";
						}

	 					$nameArray=sql_select($sql);
						foreach($nameArray as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

						 ?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								<td width="200">
									<p><? echo $row[csf('lcsc')]; ?></p>
									<input type="hidden" name="txtLcScID[]" id="txtLcScID_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
	 							</td>
								<td width="200" align="center">
									<input type="text" name="txtAmount[]" id="txtAmount_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" onKeyUp="fn_total()" >
								</td>
							</tr>
						<?
						$i++;
						}
						?>
	                    	<tfoot>
	                        	<th>Total</th>
	                            <td align="center"><input type="text" name="total_sum_amount" id="total_sum_amount" class="text_boxes_numeric" style="width:80px" value="" readonly /></td>
	                        </tfoot>
					</table>
				</div>
				<table width="400" id="table_id">
					 <tr>
						<td align="center" >
							<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
						</td>
					</tr>
				</table>
			</div>
		    <?
		}
		?>
	</fieldset>
	</form>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}



if($action=="create_lcsc_search_list_view")
{
	$data = explode("_",$data);

	$search_string=trim($data[0]);
	$search_by=$data[1];
	$company_id =$data[2];
	$all_lc_sc=$data[3];

 	$hidden_lcsc_id=explode(",",$all_lc_sc);

	if($search_by==1)
	{
		$sql = "select id,export_lc_no as lcsc,buyer_name,lc_date as lcsc_date,currency_name,lc_value as lcsc_value from com_export_lc where status_active=1";
	}
	else
	{
		$sql = "select id,contract_no as lcsc,buyer_name,contract_date as lcsc_date,currency_name,contract_value as lcsc_value from com_sales_contract where status_active=1";
	}
	//echo $sql;die;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="130"><? if($search_by==1) echo "LC"; else echo "SC"; ?></th>
                <th width="150">Buyer</th>
                <th width="90">LC Date</th>
                <th width="80">Currency</th>
                <th width="">LC Value</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_list_search" >
                <?
				$buyer_arr = return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');

				$i=1; $lcsc_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0) $bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";

					if(in_array($selectResult[csf('id')],$hidden_lcsc_id))
					{
						if($lcsc_row_id=="") $lcsc_row_id=$i; else $lcsc_row_id.=",".$i;
					}
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>,<? echo $selectResult[csf('currency_name')];?>,<? echo $selectResult[csf('id')];?>)">
                        <td width="30" align="center"><?php echo "$i"; ?>
                        <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
                        </td>
                        <td width="130"><p><? echo $selectResult[csf('lcsc')]; ?></p></td>
                        <td width="150"><p><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>
                        <td width="90"><? echo change_date_format($selectResult[csf('lcsc_date')]); ?></td>
                        <td width="80"><? echo $currency[$selectResult[csf('currency_name')]]; ?></td>
                        <td width=""><? echo $selectResult[csf('lcsc_value')]; ?></td>
                    </tr>
                    <?
                    $i++;
				}
			    ?>
				<input type="hidden" name="txt_lcsc_row_id" id="txt_lcsc_row_id" value="<?php echo $lcsc_row_id; ?>"/>
            </table>
        </div>        
	</div>
    <?
    exit();
}



if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_loan_expire_date = str_replace("'", '', $txt_loan_expire_date);
	$txt_loan_expire_date = date("d-M-Y", strtotime($txt_loan_expire_date));
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

 		if(str_replace("'","",$txt_system_number)=="") //insert
		{
			//master table entry here START---------------------------------------//
			$id=return_next_id("id", "com_pre_export_finance_mst", 1);

			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later

			$new_return_number=explode("*",return_mrr_number( str_replace("'","",$cbo_beneficiary_name), '', 'PFE', date("Y",time()), 5, "select system_number_prefix,system_number_prefix_num from com_pre_export_finance_mst where beneficiary_id=$cbo_beneficiary_name and $year_cond=".date('Y',time())." order by id DESC", "system_number_prefix", "system_number_prefix_num" ));
 			$field_array="id, system_number_prefix, system_number_prefix_num, system_number, beneficiary_id, loan_date, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',".$cbo_beneficiary_name.",".$txt_loan_date.",'".$user_id."','".$pc_date_time."')";
			//echo $field_array."<br>".$data_array;die;
			//$rID=sql_insert("com_pre_export_finance_mst",$field_array,$data_array,0);
			//master table entry here END---------------------------------------//
 		}
		else  	//update
		{
			$new_return_number[0]=str_replace("'","",$txt_system_number);
			$id=str_replace("'","",$txt_system_id);
			//master table UPDATE here START----------------------//
 			$field_array="beneficiary_id*loan_date*updated_by*update_date";
			$data_array="".$cbo_beneficiary_name."*".$txt_loan_date."*'".$user_id."'*'".$pc_date_time."'";
			//echo $field_array."<br>".$data_array;die;
			//$rID=sql_update("com_pre_export_finance_mst",$field_array,$data_array,"id",$id,0);
			//master table UPDATE here END---------------------------------------//
		}


 		//dtls table entry here START---------------------------------------//
		$dtlsid=return_next_id("id", "com_pre_export_finance_dtls", 1);
		$field_array_dtls="id,mst_id,loan_type,loan_number,bank_account_id,loan_amount,currency_id,conversion_rate,equivalent_fc,inserted_by,insert_date,loan_tenor,loan_expire_date";
		$data_array_dtls="(".$dtlsid.",".$id.",".$cbo_loan_type.",".$txt_loan_number.",".$cbo_bank_acc.",".$txt_loan_amount.",".$hid_currency_name.",".$txt_conversion_rate.",".$txt_equivalent_fc.",'".$user_id."','".$pc_date_time."',".$txt_loan_tenor.",'".$txt_loan_expire_date."')";
		//echo $field_array."<br>".$data_array;die;

		//dtls table entry here END---------------------------------------//


		//com_pre_export_lc_wise_dtls table entry here START---------------------------------------//
		$trid=return_next_id("id", "com_pre_export_lc_wise_dtls", 1);
		$field_array_lc_wise = "id,pre_export_dtls_id,export_type,lc_sc_id,currency_id,amount,conversion_rate,equivalent_fc";
		$data_array_lc_wise = "";
		$save_data=str_replace("'","",$save_data);
		$save_data_arr = explode(",",$save_data);
		foreach($save_data_arr as $val)
		{
			$exVal = explode("**",$val);
			$lcsc_id = $exVal[0];
			$lcsc_value = $exVal[1];
			if($data_array_lc_wise!="") $data_array_lc_wise .= ",";
			$data_array_lc_wise .= "(".$trid.",".$dtlsid.",".$lc_or_sc.",".$lcsc_id.",".$hid_currency_name.",".$lcsc_value.",".$txt_conversion_rate.",".$txt_equivalent_fc.")";
 			$trid=$trid+1;
		}

		$trrID=true;
		if(count($save_data_arr)>0)
		{
			//echo $field_array."<br>".$data_array;die;
			$trrID=sql_insert("com_pre_export_lc_wise_dtls",$field_array_lc_wise,$data_array_lc_wise,0);
 		}
		//com_pre_export_lc_wise_dtls table entry here END---------------------------------------//

		if(str_replace("'","",$txt_system_number)=="")
		{
			$rID=sql_insert("com_pre_export_finance_mst",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("com_pre_export_finance_mst",$field_array,$data_array,"id",$id,0);
		}

		$dtlsrID=sql_insert("com_pre_export_finance_dtls",$field_array_dtls,$data_array_dtls,1);
		//echo "20**".$rID." && ".$dtlsrID." && ".$trrID;mysql_query("ROLLBACK");die;

		if($db_type==0)
		{
			if($rID && $dtlsrID && $trrID)
			{
				mysql_query("COMMIT");
				echo "0**".$id."**".$new_return_number[0];
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID && $trrID)
			{
				oci_commit($con);
				echo "0**".$id."**".$new_return_number[0];
			}
			else
			{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if(str_replace("'","",$update_id)=="") { echo "10**";disconnect($con);die; }

 		$id=str_replace("'","",$txt_system_id);
		//master table UPDATE here START----------------------//
		$field_array_mst="beneficiary_id*loan_date*updated_by*update_date";
		$data_array_mst="".$cbo_beneficiary_name."*".$txt_loan_date."*'".$user_id."'*'".$pc_date_time."'";
		//echo $field_array."<br>".$data_array;die;
		//$rID=sql_update("com_pre_export_finance_mst",$field_array_mst,$data_array_mst,"id",$id,0);
		//master table UPDATE here END---------------------------------------//


 		//dtls table UPDATE here START---------------------------------------//
		$dtlsid=str_replace("'","",$update_id);
		$field_array_dtls="loan_type*loan_number*bank_account_id*loan_amount*currency_id*conversion_rate*equivalent_fc*updated_by*update_date*loan_tenor*loan_expire_date";
		$data_array_dtls="".$cbo_loan_type."*".$txt_loan_number."*".$cbo_bank_acc."*".$txt_loan_amount."*".$hid_currency_name."*".$txt_conversion_rate."*".$txt_equivalent_fc."*'".$user_id."'*'".$pc_date_time."'*".$txt_loan_tenor."*'".$txt_loan_expire_date."'";

		// echo $txt_loan_expire_date;
		// echo $field_array_dtls."<br>".$data_array_dtls;die;

		//dtls table UPDATE here END---------------------------------------//


		//delete previous entry

		//com_pre_export_lc_wise_dtls table entry here START---------------------------------------//
		$trid=return_next_id("id", "com_pre_export_lc_wise_dtls", 1);
		$field_array = "id,pre_export_dtls_id,export_type,lc_sc_id,currency_id,amount,conversion_rate,equivalent_fc";
		$data_array = "";
		$save_data=str_replace("'","",$save_data);
		$save_data_arr = explode(",",$save_data);
		
		foreach($save_data_arr as $val)
		{
			$exVal = explode("**",$val);
			$lcsc_id = $exVal[0];
			$lcsc_value = $exVal[1];
			$txt_conversion_rate = str_replace("'","",$txt_conversion_rate);
			$equivalentAmt = number_format(($lcsc_value/$txt_conversion_rate),$dec_place[5],".","");
			if($data_array!="") $data_array .= ",";
			$data_array .= "(".$trid.",".$dtlsid.",".$lc_or_sc.",".$lcsc_id.",".$hid_currency_name.",".$lcsc_value.",".$txt_conversion_rate.",".$equivalentAmt.")";
 			$trid=$trid+1;
		}

		$deleteTran = execute_query("delete from com_pre_export_lc_wise_dtls where pre_export_dtls_id=$dtlsid");

		$trrID=true;
		if(count($save_data_arr)>0)
		{
			//echo $field_array."<br>".$data_array;die;
			$trrID=sql_insert("com_pre_export_lc_wise_dtls",$field_array,$data_array,0);
 		}
		//com_pre_export_lc_wise_dtls table entry here END---------------------------------------//
		$dtlsrID=sql_update("com_pre_export_finance_dtls",$field_array_dtls,$data_array_dtls,"id",$dtlsid,1);
		//echo "20**".$rID." && ".$dtlsrID." && ".$trrID;mysql_query("ROLLBACK");die;
		$rID=sql_update("com_pre_export_finance_mst",$field_array_mst,$data_array_mst,"id",$id,0);
		//echo "10**$rID = $dtlsrID = $trrID = $deleteTran";
		if($db_type==0)
		{
			if($rID && $dtlsrID && $trrID && $deleteTran)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'", '', $txt_system_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID && $trrID && $deleteTran)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $txt_system_id);
			}
			else
			{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}

	else if ($operation==2)   // Delete Here
	{
		 //in future
	}
}

if($action=="mrr_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(mrr)
		{
			$("#hidden_system_number").val(mrr); // mrr number
			parent.emailwindow.hide();
		}
		function change_field_label(){

		}
	</script>

	</head>

	<body>
		<script>
		function change_search_item( item_type)
		{
			var fld = document.getElementById('cbo_search_by');
			var fld_data  =fld.options[fld.selectedIndex].text;
			//var msg_text="";

			var	msg_text="Please Enter "+fld_data;

			document.getElementById('search_by_td_up').innerHTML=msg_text;
			//alert(cntrl_type);
			if (item_type==1){
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes"	id="txt_search_common"/>'; return;
			}


		}
		</script>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>
						<th width="170">Search By</th>
						<th width="250" align="center" id="search_by_td_up">Enter Return Number</th>
						<th width="200">Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
						<td>
							<?
								$search_by = array(1=>'System Number', 2=>'Loan Number', 3=>'LC Number');
								$dd="change_search_item(this.value)";
								echo create_drop_down( "cbo_search_by", 150, $search_by, "", 0, "--Select--", 0,$dd,0 );
							?>
						</td>
						<td width="" align="center" id="search_by_td">
							<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $beneficiary_name; ?>, 'create_system_no_search_list_view', 'search_div', 'pre_export_finance_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
				</tr>
				<tr>
					<td align="center" height="40" valign="middle" colspan="5">
						<? echo load_month_buttons(1);  ?>
						<!-- Hidden field here-->
						<input type="hidden" id="hidden_system_number" value="" />
						<!--END-->
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


if($action=="create_system_no_search_list_view")
{
	$ex_data = explode("_",$data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$beneficiary_name = $ex_data[4];

	$sql_cond="";
	if($search_by==1)
	{
		if($search_common!="") $sql_cond .= " and a.system_number_prefix_num='$search_common'";
	}
	else if($search_by==2)
	{
		if($search_common!="") $sql_cond .= " and b.loan_number='$search_common'";
	}
    else if($search_by==3)
    {
        if($search_common!="") $sql_cond .= " and c.export_lc_no='$search_common'";
    }
	if($db_type==0)
	{
		if( $txt_date_from!="" || $txt_date_to!="" ) $sql_cond .= " and a.loan_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	}
	else if($db_type==2)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.loan_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}

	if(trim($beneficiary_name)!="") $sql_cond .= " and a.beneficiary_id='$beneficiary_name'";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";

	if ($search_by == 3) 
	{
		$sql="select c.export_lc_no, a.id, a.system_number_prefix_num, to_char(a.insert_date,'yyyy') as year, a.system_number, a.beneficiary_id, b.loan_number, a.loan_date, b.is_posted_account from com_export_lc c inner join com_pre_export_lc_wise_dtls d on d.lc_sc_id = c.id and d.export_type=1
   		 inner join com_pre_export_finance_dtls b on b.id = d.pre_export_dtls_id inner join com_pre_export_finance_mst a on a.id = b.mst_id where a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 $sql_cond order by id";
	}
	else
	{
		$sql="select c.export_lc_no, a.id, a.system_number_prefix_num, to_char(a.insert_date,'yyyy') as year, a.system_number, a.beneficiary_id, b.loan_number, a.loan_date, b.is_posted_account from com_export_lc c inner join com_pre_export_lc_wise_dtls d on d.lc_sc_id = c.id and d.export_type=1 
    	inner join com_pre_export_finance_dtls b on b.id = d.pre_export_dtls_id inner join com_pre_export_finance_mst a on a.id = b.mst_id where a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 $sql_cond 
		UNION ALL
		select c.contract_no as export_lc_no, a.id, a.system_number_prefix_num, to_char(a.insert_date,'yyyy') as year, a.system_number, a.beneficiary_id, b.loan_number, a.loan_date, b.is_posted_account from com_sales_contract c inner join com_pre_export_lc_wise_dtls d on d.lc_sc_id = c.id and d.export_type=2 
    	inner join com_pre_export_finance_dtls b on b.id = d.pre_export_dtls_id inner join com_pre_export_finance_mst a on a.id = b.mst_id where a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 $sql_cond
		order by id
		";
	}
    

	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$arr=array(2=>$company_arr);
 	echo create_list_view("list_view", "Year,System No,Beneficiary,LC Number,Loan Number,Loan Date","80,80,150,150,150,100","780","260",0, $sql , "js_set_value", "id,is_posted_account", "", 1, "0,0,beneficiary_id,0,0,0", $arr, "year,system_number_prefix_num,beneficiary_id,export_lc_no,loan_number,loan_date","pre_export_finance_controller","",'0,0,0,0,0,3',"");
 	exit();
}

if($action=="populate_master_from_data")
{

	$sql="SELECT a.id, a.system_number, a.beneficiary_id, a.loan_date, b.loan_tenor, b.loan_expire_date from com_pre_export_finance_mst a, com_pre_export_finance_dtls b where a.id=b.mst_id and a.id='$data'";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_system_number').val('".$row[csf("system_number")]."');\n";
		echo "$('#txt_system_id').val('".$row[csf("id")]."');\n";
 		echo "$('#cbo_beneficiary_name').val(".$row[csf("beneficiary_id")].");\n";
 		echo "$('#txt_loan_tenor').val('".$row[csf("loan_tenor")]."');\n";
 		echo "$('#txt_loan_expire_date').val('".$row[csf("loan_expire_date")]."');\n";
 		echo "$('#txt_loan_date').val('".change_date_format($row[csf("loan_date")])."');\n";
 		
   	}
	exit();
}


if($action=="show_dtls_list_view")
{

	$sql = "SELECT b.id,a.system_number,a.beneficiary_id,a.loan_date,b.loan_type,b.loan_number,b.bank_account_id,b.loan_amount,b.currency_id,b.conversion_rate,b.equivalent_fc, c.export_type, c.lc_sc_id, c.amount, d.export_lc_no
	from  com_pre_export_finance_mst a, com_pre_export_finance_dtls b, com_pre_export_lc_wise_dtls c, com_export_lc d
	where a.id=b.mst_id and b.id = c.pre_export_dtls_id and c.lc_sc_id=d.id and c.export_type=1 and a.status_active=1 and a.id=$data
	union all
	select b.id, a.system_number, a.beneficiary_id, a.loan_date, b.loan_type, b.loan_number, b.bank_account_id, b.loan_amount, b.currency_id, b.conversion_rate, b.equivalent_fc, c.export_type, c.lc_sc_id, c.amount, d.contract_no as export_lc_no
	from  com_pre_export_finance_mst a, com_pre_export_finance_dtls b, com_pre_export_lc_wise_dtls c, com_sales_contract d
	where a.id=b.mst_id and b.id = c.pre_export_dtls_id and c.lc_sc_id=d.id and c.export_type=2 and a.status_active=1 and a.id=$data";
	//echo $sql; //die;
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$result = sql_select($sql);
	$loan_data_array=array();
	foreach ($result as $value) {
		$loan_data_array[$value[csf("id")]][$value[csf("lc_sc_id")]]["id"]= $value[csf("id")];
		$loan_data_array[$value[csf("id")]][$value[csf("lc_sc_id")]]["lc_sc_id"]= $value[csf("lc_sc_id")];
		$loan_data_array[$value[csf("id")]][$value[csf("lc_sc_id")]]["system_number"]= $value[csf("system_number")];
		$loan_data_array[$value[csf("id")]][$value[csf("lc_sc_id")]]["lc_sc_no"]= $value[csf("lc_sc_no")];
		$loan_data_array[$value[csf("id")]][$value[csf("lc_sc_id")]]["beneficiary_id"]= $value[csf("beneficiary_id")];
		$loan_data_array[$value[csf("id")]][$value[csf("lc_sc_id")]]["loan_type"]= $value[csf("loan_type")];
		$loan_data_array[$value[csf("id")]][$value[csf("lc_sc_id")]]["loan_date"]= $value[csf("loan_date")];
		$loan_data_array[$value[csf("id")]][$value[csf("lc_sc_id")]]["loan_number"]= $value[csf("loan_number")];
		$loan_data_array[$value[csf("id")]][$value[csf("lc_sc_id")]]["bank_account_id"]= $value[csf("bank_account_id")];
		$loan_data_array[$value[csf("id")]][$value[csf("lc_sc_id")]]["loan_amount"]= $value[csf("amount")];
		$loan_data_array[$value[csf("id")]][$value[csf("lc_sc_id")]]["currency_id"]= $value[csf("currency_id")];
		$loan_data_array[$value[csf("id")]][$value[csf("lc_sc_id")]]["conversion_rate"]= $value[csf("conversion_rate")];
		$loan_data_array[$value[csf("id")]][$value[csf("lc_sc_id")]]["equivalent_fc"]= $value[csf("equivalent_fc")];
		$loan_data_array[$value[csf("id")]][$value[csf("lc_sc_id")]]["export_type"]= $value[csf("export_type")];
		$loan_data_array[$value[csf("id")]][$value[csf("lc_sc_id")]]["export_lc_no"]= $value[csf("export_lc_no")];
	}
	//var_dump($loan_data_array);//die;

 	//$arr=array(1=>$company_arr,3=>$ac_loan_type,5=>$commercial_head);
 	//echo create_list_view("list_view", "System No,Beneficiary,Loan Date,Loan Type,Loan No,LC/SC No,Bank Account,Loan Amt,Conv. Rate,Equ.FC","120,120,80,90,80,100,120,110,60,110","1050","260",0, $sql , "get_php_form_data", "id", "'child_form_input_data'", 1, "0,beneficiary_id,0,loan_type,0,bank_account_id,0,0,0,0", $arr, "system_number,beneficiary_id,loan_date,loan_type,loan_number,bank_account_id,loan_amount,conversion_rate,equivalent_fc,export_type,lc_sc_id","requires/pre_export_finance_controller","",'0,0,3,0,0,0,2,2,2',"7,loan_amount,0,equivalent_fc") ;

	?>
	<table id="rpt_tablelist_view" class="rpt_table" width="1000" cellspacing="0" cellpadding="0" border="1" align="left" rules="all">
		<thead>
			<tr>
				<th width="30">SL No</th>
				<th width="100">System No</th>
				<th width="120">Beneficiary</th>
				<th width="65">Loan Date</th>
				<th width="80">Loan Type</th>
				<th width="110">Loan No</th>
				<th width="140">LC/SC No</th>
				<th width="100">Bank Account</th>
				<th width="100">Loan Amt</th>
				<th width="60">Conv. Rate</th>
				<th>Equ.FC</th>
			</tr>
		</thead>
		<tbody>
		<?
		$i= 1;
		
		foreach ($loan_data_array as  $lc_sc_id) 
		{
			($i%2==0) ? $bgcolor="#E9F3FF" : $bgcolor="#FFFFFF";
			foreach ($lc_sc_id as $row) {
				
			$lc_sc_arr[1][$row["id"]]=$row["lc_sc_id"];
			$lc_sc_arr[2][$row["id"]]=$row["lc_sc_id"];
			?>
			<tr style="cursor:pointer" id="tr_<? echo $i;?>" bgcolor="<? echo $bgcolor;?>" onClick="get_php_form_data('<? echo $row["id"]; ?>','child_form_input_data','requires/pre_export_finance_controller')">
				<td align="center"><? echo $i;?></td>
				<td style="word-break:break-all;"><? echo $row["system_number"];?></td>
				<td style="word-break:break-all;"><? echo $company_arr[$row["beneficiary_id"]];?></td>
				<td style="word-break:break-all;" align="center"><? if($row["loan_date"] != "" && $row["loan_date"] !="0000-00-00") echo change_date_format($row["loan_date"]);?></td>
				<td style="word-break:break-all;"><? echo $commercial_head[$row["loan_type"]];?></td>
				<td style="word-break:break-all;"><? echo $row["loan_number"];?></td>
				<td style="word-break:break-all;"><? echo $row["export_lc_no"]; //lc_sc_id?></td>
				<td style="word-break:break-all;"><? echo $commercial_head[$row["bank_account_id"]];?></td>
				<td align="right"><? echo number_format($row["loan_amount"],2); $tot_loan_amt+=$row["loan_amount"];?></td>
				<td align="right"><? echo $row["conversion_rate"];?></td>
				<td><? echo $row["equivalent_fc"];?></td>
			</tr>
			<?
			$i++;
			}
		}
		?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="8">Total</th>
				<th align="right" id="value_tot_loan_amount"><? echo number_format($tot_loan_amt,2);?></th>
				<th align="right"></th>
				<th align="right" id="tot_equ_fc"></th>
			</tr>
		</tfoot>
	</table>
	<?

 	exit();
}


if($action=="child_form_input_data")
{
	$sql="SELECT a.id as mst_id,b.id, a.system_number, a.beneficiary_id, a.loan_date, b.loan_type, b.loan_tenor, b.loan_expire_date, b.loan_number, b.bank_account_id, b.loan_amount, b.currency_id, b.conversion_rate, b.equivalent_fc, b.is_posted_account 
	from com_pre_export_finance_mst a, com_pre_export_finance_dtls b
	where a.id=b.mst_id and a.status_active=1 and b.id=$data";
 	//echo $sql;//die;
	$result = sql_select($sql);
	
	
	foreach($result as $row)
	{

		echo "$('#txt_system_id').val('".$row[csf("mst_id")]."');\n";
		echo "$('#txt_system_number').val('".$row[csf("system_number")]."');\n";
 		echo "$('#cbo_loan_type').val('".$row[csf("loan_type")]."');\n";
		echo "$('#txt_loan_number').val('".$row[csf("loan_number")]."');\n";
		echo "$('#txt_loan_date').val('".date("d-m-Y",strtotime($row[csf("loan_date")]))."');\n";
		echo "$('#txt_loan_tenor').val('".$row[csf("loan_tenor")]."');\n";
		echo "$('#txt_loan_expire_date').val('".$row[csf("loan_expire_date")]."');\n";
		echo "$('#cbo_bank_acc').val('".$row[csf("bank_account_id")]."');\n";
		echo "$('#txt_loan_amount').val('".$row[csf("loan_amount")]."');\n";
		echo "$('#txt_conversion_rate').val('".$row[csf("conversion_rate")]."');\n";
 		echo "$('#txt_equivalent_fc').val('".$row[csf("equivalent_fc")]."');\n";
		
		
		
 		echo "$('#update_id').val(".$row[csf("id")].");\n";

		$dtlssql=sql_select("select pre_export_dtls_id, export_type, lc_sc_id, currency_id, amount, conversion_rate, equivalent_fc, is_posted_account from com_pre_export_lc_wise_dtls where pre_export_dtls_id='".$row[csf("id")]."'");
		$save_data_string = "";
		$all_lcsc_id_string = "";
		$lcOrc = "";
		$currencyName = "";$is_posted_accounts=0;
		foreach($dtlssql as $res)
		{
			if($save_data_string!="")
			{
				$save_data_string .= ",";
				$all_lcsc_id_string .= ",";
			}
			$save_data_string .= $res[csf("lc_sc_id")]."**".$res[csf("amount")];
			$all_lcsc_id_string .= $res[csf("lc_sc_id")];
			$lcOrc = $res[csf("export_type")];
			$currencyName = $res[csf("currency_id")];
			if($res[csf("is_posted_account")]>$is_posted_accounts) $is_posted_accounts = $res[csf("is_posted_account")];
		}
		echo "$('#is_posted_account').val('".$is_posted_accounts."');\n";
		if($is_posted_accounts==1){
			echo "$('#text_posted_account_td').text('Already Posted In Accounting.');\n";
		}else{
			echo "$('#text_posted_account_td').text('');\n";
		}
		echo "$('#save_data').val('".$save_data_string."');\n";
		echo "$('#all_lc_sc_id').val('".$all_lcsc_id_string."');\n";
		echo "$('#lc_or_sc').val(".$lcOrc.");\n";
		echo "$('#hid_currency_name').val(".$currencyName.");\n";
 	}
 	echo "set_button_status(1, permission, 'fnc_pre_export_finance_entry',1);\n";
  	exit();
}


?>
