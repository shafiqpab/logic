<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];


if ($action=="ac_head_list_view")
{

	$cbo_type_arr=array(1=>"Cutting",2=>"Sewing",3=>"Finishing");

	//load_drop_down( 'requires/ac_head_wish_stranded_cost_set_up_controller',$('#cbo_process_type').val()+'__'+$('#cbo_company_id').val()+'__'+$('#cbo_from_year').val()+'__'+$('#cbo_from_month').val(), 'load_ac_head_tbl', 'ac_head_tbl' );
	$sql=("SELECT a.COMPANY_ID, a.PROCESS_YEAR, a.PROCESS_MONTH, a.PROCES_TYPE, b.AC_CODE , b.AC_DESCRIPTION,b.AMMOUNT, b.COST_PER_MIN, b.COST_PER_PC from lib_process_ac_head_standard_mst a, lib_process_ac_head_standard_dtls b where a.ID=b.MST_ID  and a.ID=$data and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0");

	$data_arr=sql_select($sql);
	
	$process_type=$data_arr[0]["PROCES_TYPE"];
	$com_id=$data_arr[0]["COMPANY_ID"];
	$process_year=$data_arr[0]["PROCESS_YEAR"];
	$process_month=$data_arr[0]["PROCESS_MONTH"];
	if($process_type==1)
	{
		$capacity_sql = "SELECT b.capacity_month_min , b.capacity_month_pcs from lib_cutt_capacity_calc_mst a, lib_cutt_capacity_year_dtls b where a.id=b.mst_id and a.comapny_id=$com_id and a.year=$process_year and b.month_id=$process_month and a.status_active = 1 and a.is_deleted = 0 and  b.status_active = 1 and b.is_deleted = 0";
	}
	elseif($process_type==2)
	{
		$capacity_sql="SELECT sum(b.capacity_month_min) as capacity_month_min, sum(b.capacity_month_pcs) as capacity_month_pcs  
		from lib_capacity_calc_mst a, lib_capacity_year_dtls b 
		where a.id=b.mst_id and a.comapny_id=$com_id and a.year=$process_year and b.month_id=$process_month and  a.status_active = 1 and a.is_deleted = 0 and  b.status_active = 1";
	}
	else
	{
		$capacity_sql="SELECT sum(b.CAPACITY_MONTH_MINT) as capacity_month_min, sum(b.CAPACITY_MONTH_PCS) as capacity_month_pcs  
		from LIB_FIN_GMTS_CAPACITY_CAL_MST a, LIB_FIN_GMTS_CAPA_YEAR_DTLS b 
		where a.id=b.mst_id and a.COMPANY_ID=$com_id and a.YEAR=$process_year and b.MONTH_ID=$process_month and a.FIN_TYPE=4 and  a.status_active = 1 and a.is_deleted = 0";
	}
	//echo $capacity_sql;
	$capacity_data=sql_select($capacity_sql);
	$capacity_min=$capacity_data[0][csf("capacity_month_min")];
	$capacity_pcs=$capacity_data[0][csf("capacity_month_pcs")];
	//echo $capacity_min."==".$capacity_pcs;
	?>
	<div>
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="750">
            <thead>
                <tr>
                    <th width="30">SL NO</th>
                    <th width="40">Year</th>
                    <th width="70">Month</th>
                    <th width="70">Process</th>
                    <th width="70">A/C Code</th>
                    <th width="220">A/C Description</th>
                    <th width="80">Amount (BDT)</th>
                    <th width="80">CPM</th>
                    <th>Cost/Pcs</th>
                </tr>
            </thead>
            <?php 
            $i=0;
            $total_amount=0;
            $total_cost_pc="";
            $total_cost_min="";
            foreach($data_arr as $row){

                $i++;		
                if( $i % 2 == 0 ) $bgcolor="#E9F3FF"; else $bgcolor = "#FFFFFF";
            
            ?>
            <tbody>
                <tr bgcolor="<?php echo $bgcolor?>" >
                    <td align="canter"><?php echo  $i?></td>
                    <td align="canter"><?php echo $year[$row["PROCESS_YEAR"]];?></td>
                    <td align="canter"><?php echo $months[$row["PROCESS_MONTH"]];?></td>
                    <td align="canter"><?php echo $cbo_type_arr[$row["PROCES_TYPE"]];?></td>
                    <td align="canter"><?php echo $row["AC_CODE"];?></td>
                    <td align="canter"><?php echo $row["AC_DESCRIPTION"];?></td>
                    <td align="right" ><?php echo number_format($row["AMMOUNT"],4,'.','');?></td>
                    <td align="right" title="<?= number_format((1000/5791500),8,'.','');?>"><?php echo number_format($row["COST_PER_MIN"],12,'.','');?></td>
                    <td align="right"><?php echo number_format($row["COST_PER_PC"],12,'.','');?></td>
                </tr>
            </tbody>
            <?php 
            $total_amount+=$row["AMMOUNT"];
            $total_cost_min+=number_format($row["COST_PER_MIN"],12);
            $total_cost_pc+=number_format($row["COST_PER_PC"],12);
            }			
            ?>
            <tfoot>
                <tr>
                    <th colspan="6">Total</th>
                    <th><?php echo number_format($total_amount,4,'.','');?></th>
                    <th><?php echo number_format(($total_amount/$capacity_min),12,'.','');?></th>
                    <th><?php echo number_format(($total_amount/$capacity_pcs),12,'.','');?></th>
                 </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}


if ($action=="load_ac_head_tbl")
{
	// print_r($data);
	$data=explode('__',$data);
	// echo "<pre>";
	//  print_r($data);
	$process_typ=$data[0];
	$company_id=$data[1];
	$year=$data[2];
	$month=$data[3];

	if($process_typ == 1)
	{
	    /*	$capacity_data=sql_select("SELECT sum(b.capacity_month_min) as capacity_month_min, sum(b.capacity_month_pcs) as capacity_month_pcs  from lib_capacity_calc_mst a,
		lib_capacity_year_dtls b where a.id=b.mst_id and a.comapny_id=$company_id and a.year=$year and b.month_id=$month and a.status_active = 1 and a.is_deleted = 0 and  b.status_active = 1 and b.is_deleted = 0"); */

		$capacity_data = sql_select("SELECT b.capacity_month_min , b.capacity_month_pcs from lib_cutt_capacity_calc_mst a, lib_cutt_capacity_year_dtls b where a.id=b.mst_id and a.comapny_id=$company_id and a.year=$year and b.month_id=$month and a.status_active = 1 and a.is_deleted = 0 and  b.status_active = 1 and b.is_deleted = 0");
		 
		$sql=" SELECT b.id as ID, b.ac_code as AC_CODE, b.ac_description as AC_DESCRIPTION
		FROM lib_account_group a, ac_coa_mst b
	   	WHERE a.id = b.ac_subgroup_id AND a.main_group = 7 AND a.status_active = 1 AND a.is_deleted = 0 AND b.is_deleted = 0 AND b.company_id = $company_id
	    ORDER BY b.ac_code";	

		$sql_prv_data=sql_select("SELECT a.ID, b.AMMOUNT, b.AC_CAO_ID, b.COST_PER_PC, b.COST_PER_MIN from lib_process_ac_head_standard_mst a, lib_process_ac_head_standard_dtls b where a.ID=b.MST_ID and a.PROCESS_YEAR=$year and a.COMPANY_ID=$company_id and  a.PROCESS_MONTH=$month and PROCES_TYPE=1 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0");

		$all_prv_data=array();
		foreach($sql_prv_data as $row){

			$all_prv_data[$row["AC_CAO_ID"]]["AMMOUNT"]=$row["AMMOUNT"];
			$all_prv_data[$row["AC_CAO_ID"]]["COST_PER_PC"]=$row["COST_PER_PC"];
			$all_prv_data[$row["AC_CAO_ID"]]["COST_PER_MIN"]=$row["COST_PER_MIN"];
	    }

        $new_conn = integration_params(3);
		$account_head=sql_select($sql,"",$new_conn) ; 
		
		// print_r($account_head);
		?>
		<div>
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="370">
				<thead>
					<tr>
					<th width="30">SL 
						
					<input type="hidden" id="txt_capacity_month_min" name="txt_capacity_month_min" value="<?= $capacity_data[0]["CAPACITY_MONTH_MIN"]; ?>">
					
					<input type="hidden" id="txt_capacity_month_pcs" name="txt_capacity_month_pcs" value="<?= $capacity_data[0]["CAPACITY_MONTH_PCS"]; ?>"> 

					<input type="hidden" id="txt_update_id" name="txt_update_id" value="<?= $sql_prv_data[0]["ID"]; ?>"> 
				 </th>

					<th width="60">A/C Code</th>
					<th width="180">Bill Description</th>
					<th>Amount (Tk)</th>
					</tr>
				</thead>
			</table>
				
				<div style="width:370px; max-height:470px;overflow-y:scroll;" >	 
                  <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" id="table_list_dtls">
				    <tbody>
					<?
					$i = 0;
					foreach($account_head as $value)
					{
							$i++;
						
								if( $i % 2 == 0 ) $bgcolor="#E9F3FF"; else $bgcolor = "#FFFFFF";
												
							?>
						<tr  bgcolor="<? echo $bgcolor; ?>">
							<td width="30" align="center"><? echo $i;?>  <input type="hidden" id="txt_id_<?echo $i;?>" name="txt_id[]" value="<? echo $value["ID"]?>"> </td>
							<td width="60" align="center"> <? echo $value["AC_CODE"]?> <input type="hidden" name="txt_acount_code[]" id="txt_acount_code<? echo $i;?>" value="<? echo $value["AC_CODE"]?>"> </td>
							<td width="180"><? echo $value["AC_DESCRIPTION"]?>  <input type="hidden" id="txt_ac_description_<? echo $i;?>" name="txt_ac_description[]" value="<? echo $value["AC_DESCRIPTION"]?>">  </td>
							<td align="center">
								
							<input type="text" id="txt_amount_<? echo $i;?>" value="<? echo $all_prv_data[$value["ID"]]["AMMOUNT"]; ?>" onblur="fnc_clc(<?= $i ;?>)"  style="width:70px;" name="txt_amount[]" class="text_boxes_numeric">

							<input type="hidden" id="txt_capacity_min_<? echo $i;?>" value="<? echo $all_prv_data[$value["ID"]]["COST_PER_MIN"];?>" style="width: 70px;" name="txt_capacity_min[]" class="text_boxes_numeric">
							<input type="hidden" id="txt_capacity_pcs_<? echo $i;?>" value="<? echo $all_prv_data[$value["ID"]]["COST_PER_PC"]; ?>" style="width: 70px;" name="txt_capacity_pcs[]" class="text_boxes_numeric">						
						</td>
						</tr>
						<?
					}
					?>
						<tr>
							<td colspan='3' align="right" style="font-weight:bold;">Total &nbsp;&nbsp;&nbsp;</td>
							<td  align="center" style="font-weight:bold;"><input type="text" id="txtamount_tot" name="txtminute_tot" style="width:70px; font-weight:bold;" class="text_boxes_numeric" readonly></td>
						</tr>
			  	 </tbody>
				</table>
			</div>
		</div>
				<?			
	}
	else if($process_typ ==2)
	{
		$capacity_data=sql_select("SELECT sum(b.capacity_month_min) as capacity_month_min, sum(b.capacity_month_pcs) as capacity_month_pcs  
		from lib_capacity_calc_mst a, lib_capacity_year_dtls b 
		where a.id=b.mst_id and a.comapny_id=$company_id and a.year=$year and b.month_id=$month and  a.status_active = 1 and a.is_deleted = 0 and  b.status_active = 1");
		 
		$sql=" SELECT b.id as ID, b.ac_code as AC_CODE, b.ac_description as AC_DESCRIPTION
		FROM lib_account_group a, ac_coa_mst b
	   	WHERE a.id = b.ac_subgroup_id AND a.main_group = 7 AND a.status_active = 1 AND a.is_deleted = 0 AND b.is_deleted = 0 AND b.company_id = $company_id
	    ORDER BY b.ac_code";	


		$sql_prv_data=sql_select("SELECT a.ID, b.AMMOUNT, b.AC_CAO_ID, b.COST_PER_PC, b.COST_PER_MIN from lib_process_ac_head_standard_mst a, lib_process_ac_head_standard_dtls b where a.ID=b.MST_ID and a.PROCESS_YEAR=$year and a.COMPANY_ID=$company_id and  a.PROCESS_MONTH=$month and PROCES_TYPE=2 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0");
	
		$all_prv_data=array();
		  foreach($sql_prv_data as $row){
	
			$all_prv_data[$row["AC_CAO_ID"]]["AMMOUNT"]=$row["AMMOUNT"];
			$all_prv_data[$row["AC_CAO_ID"]]["COST_PER_PC"]=$row["COST_PER_PC"];
			$all_prv_data[$row["AC_CAO_ID"]]["COST_PER_MIN"]=$row["COST_PER_MIN"];
	
		  }

        $new_conn = integration_params(3);
		$account_head=sql_select($sql,"",$new_conn) ; 
		
		// print_r($account_head);
		?>
		<div>
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="370">
				<thead>
					<tr>
					<th width="30">SL 
						
					<input type="hidden" id="txt_capacity_month_min" name="txt_capacity_month_min" value="<?= $capacity_data[0]["CAPACITY_MONTH_MIN"]; ?>">
					
					<input type="hidden" id="txt_capacity_month_pcs" name="txt_capacity_month_pcs" value="<?= $capacity_data[0]["CAPACITY_MONTH_PCS"]; ?>"> 
					<input type="hidden" id="txt_update_id" name="txt_update_id" value="<?= $sql_prv_data[0]["ID"]; ?>"> 

				
				</th>

					<th width="60">A/C Code</th>
					<th width="180">Bill Description</th>
					<th>Amount (Tk)</th>
					</tr>
				</thead>
			</table>
				
				<div style="width:370px; max-height:470px;overflow-y:scroll;" >	 
                  <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" id="table_list_dtls">
				    <tbody>
					<?
					$i = 0;
					foreach($account_head as $value)
					{
							$i++;
						
								if( $i % 2 == 0 ) $bgcolor="#E9F3FF"; else $bgcolor = "#FFFFFF";
												
							?>
						<tr  bgcolor="<? echo $bgcolor; ?>">
							<td width="30" align="center"><? echo $i;?>  <input type="hidden" id="txt_id_<?echo $i;?>" name="txt_id[]" value="<? echo $value["ID"]?>"> </td>
							<td width="60" align="center"> <? echo $value["AC_CODE"]?> <input type="hidden" name="txt_acount_code[]" id="txt_acount_code<?echo $i;?>" value="<? echo $value["AC_CODE"]?>"> </td>
							<td width="180"><? echo $value["AC_DESCRIPTION"]?>  <input type="hidden" id="txt_ac_description_<?echo $i;?>" name="txt_ac_description[]" value="<? echo $value["AC_DESCRIPTION"]?>">  </td>
							<td align="center">
								
							<input type="text" id="txt_amount_<? echo $i;?>" value="<? echo $all_prv_data[$value["ID"]]["AMMOUNT"]; ?>" onblur="fnc_clc(<?= $i ;?>)"  style="width:70px;" name="txt_amount[]" class="text_boxes_numeric">

							<input type="hidden" id="txt_capacity_min_<? echo $i;?>" value="<? echo $all_prv_data[$value["ID"]]["COST_PER_MIN"];?>" style="width: 70px;" name="txt_capacity_min[]" class="text_boxes_numeric">
							<input type="hidden" id="txt_capacity_pcs_<? echo $i;?>" value="<? echo $all_prv_data[$value["ID"]]["COST_PER_PC"];?>" style="width: 70px;" name="txt_capacity_pcs[]" class="text_boxes_numeric">						
						</td>
						</tr>
						<?
					}
					?>
						<tr>
							<td colspan='3' align="right" style="font-weight:bold;">Total &nbsp;&nbsp;&nbsp;</td>
							<td  align="center" style="font-weight:bold;"><input type="text" id="txtamount_tot" name="txtminute_tot" style="width:70px; font-weight:bold;" class="text_boxes_numeric" readonly></td>
						</tr>
			  	 </tbody>
				</table>
			</div>
		</div>
				<?
			
	}
	else if($process_typ ==3)
	{
		$capacity_sql="SELECT sum(b.CAPACITY_MONTH_MINT) as capacity_month_min, sum(b.CAPACITY_MONTH_PCS) as capacity_month_pcs  
		from LIB_FIN_GMTS_CAPACITY_CAL_MST a, LIB_FIN_GMTS_CAPA_YEAR_DTLS b 
		where a.id=b.mst_id and a.COMPANY_ID=$company_id and a.YEAR=$year and b.MONTH_ID=$month and a.FIN_TYPE=4 and  a.status_active = 1 and a.is_deleted = 0";
		//echo $capacity_sql;
		$capacity_data=sql_select($capacity_sql);
		 
		$sql=" SELECT b.id as ID, b.ac_code as AC_CODE, b.ac_description as AC_DESCRIPTION
		FROM lib_account_group a, ac_coa_mst b
	   	WHERE a.id = b.ac_subgroup_id AND a.main_group = 7 AND a.status_active = 1 AND a.is_deleted = 0 AND b.is_deleted = 0 AND b.company_id = $company_id
	    ORDER BY b.ac_code";	

		$sql_prv_data=sql_select("SELECT a.ID, b.AMMOUNT, b.AC_CAO_ID, b.COST_PER_PC, b.COST_PER_MIN from lib_process_ac_head_standard_mst a, lib_process_ac_head_standard_dtls b where a.ID=b.MST_ID and a.PROCESS_YEAR=$year and a.COMPANY_ID=$company_id and  a.PROCESS_MONTH=$month and PROCES_TYPE=3 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0");
	
		$all_prv_data=array();
		foreach($sql_prv_data as $row){
			$all_prv_data[$row["AC_CAO_ID"]]["AMMOUNT"]=$row["AMMOUNT"];
			$all_prv_data[$row["AC_CAO_ID"]]["COST_PER_PC"]=$row["COST_PER_PC"];
			$all_prv_data[$row["AC_CAO_ID"]]["COST_PER_MIN"]=$row["COST_PER_MIN"];
		}

        $new_conn = integration_params(3);
		$account_head=sql_select($sql,"",$new_conn) ; 
		
		// print_r($account_head);
		?>
		<div>
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="370">
				<thead>
					<tr>
					<th width="30">SL 
						
					<input type="hidden" id="txt_capacity_month_min" name="txt_capacity_month_min" value="<?= $capacity_data[0]["CAPACITY_MONTH_MIN"]; ?>">
					
					<input type="hidden" id="txt_capacity_month_pcs" name="txt_capacity_month_pcs" value="<?= $capacity_data[0]["CAPACITY_MONTH_PCS"]; ?>"> 
					<input type="hidden" id="txt_update_id" name="txt_update_id" value="<?= $sql_prv_data[0]["ID"]; ?>"> 

				 </th>

					<th width="60">A/C Code</th>
					<th width="180">Bill Description</th>
					<th>Amount (Tk)</th>
					</tr>
				</thead>
			</table>
				
				<div style="width:370px; max-height:470px;overflow-y:scroll;" >	 
                  <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" id="table_list_dtls">
				    <tbody>
					<?
					$i = 0;
					foreach($account_head as $value)
					{
							$i++;
						
								if( $i % 2 == 0 ) $bgcolor="#E9F3FF"; else $bgcolor = "#FFFFFF";
												
							?>
						<tr  bgcolor="<? echo $bgcolor; ?>">
							<td width="30" align="center"><? echo $i;?>  <input type="hidden" id="txt_id_<?echo $i;?>" name="txt_id[]" value="<? echo $value["ID"]?>"> </td>
							<td width="60" align="center"> <? echo $value["AC_CODE"]?> <input type="hidden" name="txt_acount_code[]" id="txt_acount_code<?echo $i;?>" value="<? echo $value["AC_CODE"]?>"> </td>
							<td width="180"><? echo $value["AC_DESCRIPTION"]?>  <input type="hidden" id="txt_ac_description_<?echo $i;?>" name="txt_ac_description[]" value="<? echo $value["AC_DESCRIPTION"]?>">  </td>
							<td align="center">
								
							<input type="text" id="txt_amount_<?echo $i;?>" value="<? echo $all_prv_data[$value["ID"]]["AMMOUNT"]; ?>" onblur="fnc_clc(<?= $i ;?>)"  style="width:70px;" name="txt_amount[]" class="text_boxes_numeric">

							<input type="hidden" id="txt_capacity_min_<? echo $i;?>" value="<? echo  $all_prv_data[$value["ID"]]["COST_PER_MIN"]; ?>" style="width: 70px;" name="txt_capacity_min[]" class="text_boxes_numeric">
							<input type="hidden" id="txt_capacity_pcs_<? echo $i;?>" value="<? echo $all_prv_data[$value["ID"]]["COST_PER_PC"];?>" style="width: 70px;" name="txt_capacity_pcs[]" class="text_boxes_numeric">						
						</td>
						</tr>
						<?
					}
					?>
						<tr>
							<td colspan='3' align="right" style="font-weight:bold;">Total &nbsp;&nbsp;&nbsp;</td>
							<td  align="center" style="font-weight:bold;"><input type="text" id="txtamount_tot" name="txtminute_tot" style="width:70px; font-weight:bold;" class="text_boxes_numeric" readonly></td>
						</tr>
			  	 </tbody>
				</table>
			</div>
		</div>
		<?
			
	}
	else{

	}
	exit();
}

//=================SAVE UPDATE DELETE==============
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$mst_id=return_next_id( "id", "lib_process_ac_head_standard_mst", 1 ) ; 			
	
		$field_array="id, company_id, process_year, process_month, proces_type, inserted_by, insert_date, is_deleted,status_active";
		$data_array="(".$mst_id.",".$cbo_company_id.",".$cbo_from_year.",".$cbo_from_month.",".$cbo_process_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',1)";

		$field_array_dtls="id, mst_id, ac_code, ac_description, ammount, cost_per_min, cost_per_pc, ac_cao_id, inserted_by, insert_date, is_deleted, status_active";

		$id_dtls=return_next_id("id", "lib_process_ac_head_standard_dtls", 1);

		for($i=1; $i<=$total_row; $i++){

			$txt_id              = "txt_id_".$i;
			$txt_acount_code     = "txt_acount_code_".$i;
			$txt_ac_description  = "txt_ac_description_".$i;
			$txt_amount          = "txt_amount_".$i;
			$txt_capacity_min    = "txt_capacity_min_".$i;
			$txt_capacity_pcs    = "txt_capacity_pcs_".$i;

			if($$txt_amount!=""){
			if ($i!=1) $data_array_dtls .=",";
			$data_array_dtls.="(".$id_dtls.",".$mst_id.",'".$$txt_acount_code."','".$$txt_ac_description."','".$$txt_amount."','".$$txt_capacity_min."','".$$txt_capacity_pcs."','".$$txt_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',1)";
			$id_dtls=$id_dtls+1;

			}

		}
		// echo "10**insert into lib_process_ac_head_standard_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);die;


		$rID=sql_insert("lib_process_ac_head_standard_mst",$field_array,$data_array,0);
		$rID1=sql_insert("lib_process_ac_head_standard_dtls ",$field_array_dtls,$data_array_dtls,0);

		// echo '10**'.$rID.'**'.$rID1;oci_rollback($con);die;

		if($db_type==0)
		{
			if($rID==1 && $rID1==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$mst_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1)
			{
				oci_commit($con);  
				echo "0**".$mst_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
		
	}
	//=================UPDATE==============
	else if ($operation==1)   // Update Here
	{

		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		$txt_update_id=str_replace("'",'',$txt_update_id);
		$field_array_dtls="id, mst_id, ac_code, ac_description, ammount, cost_per_min, cost_per_pc, ac_cao_id, inserted_by, insert_date, is_deleted, status_active";
		$id_dtls=return_next_id("id", "lib_process_ac_head_standard_dtls", 1);

		for($i=1; $i<=$total_row; $i++){

			$txt_id              = "txt_id_".$i;
			$txt_acount_code     = "txt_acount_code_".$i;
			$txt_ac_description  = "txt_ac_description_".$i;
			$txt_amount          = "txt_amount_".$i;
			$txt_capacity_min    = "txt_capacity_min_".$i;
			$txt_capacity_pcs    = "txt_capacity_pcs_".$i;

			if($$txt_amount!=""){
			if ($i!=1) $data_array_dtls .=",";
			$data_array_dtls.="(".$id_dtls.",".$txt_update_id.",'".$$txt_acount_code."','".$$txt_ac_description."','".$$txt_amount."','".$$txt_capacity_min."','".$$txt_capacity_pcs."','".$$txt_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',1)";
			$id_dtls=$id_dtls+1;

			}
		}
		//echo "10**".$data_array_dtls;oci_rollback($con);disconnect($con);die;
		$rID3=execute_query("delete from lib_process_ac_head_standard_dtls where mst_id =".$txt_update_id."",0);
		$rID1=sql_insert("lib_process_ac_head_standard_dtls ",$field_array_dtls,$data_array_dtls,0);

		//   echo "10**insert into lib_process_ac_head_standard_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);die;
		//   echo '</br>10**'.$rID.'**'.$rID1.'**'.$rID3;oci_rollback($con);die;


		if($db_type==0)
		{           
			if( $rID3=1 && $rID1==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID3=1  && $rID1==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'",'',$txt_update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
		
	}
	//=================DELETE==============
	else if ($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	}
	else if($operation==3) /// for copy
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		// ======================== check existing ========================
		$month = str_replace("'","",$cbo_from_month);
		$year = str_replace("'","",$cbo_from_year);
		$from_year = ($month == 12) ? $year+1 : $year;
		$nextMonth = ($month == 12) ? 1 : ++$month;

		$is_prev_entry = return_field_value("id","lib_process_ac_head_standard_mst","COMPANY_ID=$cbo_company_id and PROCESS_YEAR=$from_year and PROCESS_MONTH=$nextMonth  and PROCES_TYPE=$cbo_process_type and status_active=1 and is_deleted=0","id");
		// echo "10**select id from lib_process_ac_head_standard_mst where COMPANY_ID=$cbo_company_id and PROCESS_YEAR=$from_year and PROCESS_MONTH=$nextMonth  and PROCES_TYPE=$cbo_process_type and status_active=1 and is_deleted=0";die;
		if(isset($is_prev_entry))
		{
			echo "3**This month data already exist.";
			disconnect($con);
			die();
		}
		// echo "select id from lib_process_ac_head_standard_mst where COMPANY_ID=$cbo_company_id and PROCESS_YEAR=$from_year and PROCESS_MONTH=$nextMonth  and PROCES_TYPE=$cbo_process_type and status_active=1 and is_deleted=0";die;
		$mst_id=return_next_id( "id", "lib_process_ac_head_standard_mst", 1 ) ; 			
	
		$field_array="id, company_id, process_year, process_month, proces_type, inserted_by, insert_date, is_deleted,status_active";
		$data_array="(".$mst_id.",".$cbo_company_id.",".$from_year.",".$nextMonth.",".$cbo_process_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',1)";

		$field_array_dtls="id, mst_id, ac_code, ac_description, ammount, cost_per_min, cost_per_pc, ac_cao_id, inserted_by, insert_date, is_deleted, status_active";

		$id_dtls=return_next_id("id", "lib_process_ac_head_standard_dtls", 1);

		for($i=1; $i<=$total_row; $i++){

			$txt_id              = "txt_id_".$i;
			$txt_acount_code     = "txt_acount_code_".$i;
			$txt_ac_description  = "txt_ac_description_".$i;
			$txt_amount          = "txt_amount_".$i;
			$txt_capacity_min    = "txt_capacity_min_".$i;
			$txt_capacity_pcs    = "txt_capacity_pcs_".$i;

			if($$txt_amount!=""){
			if ($i!=1) $data_array_dtls .=",";
			$data_array_dtls.="(".$id_dtls.",".$mst_id.",'".$$txt_acount_code."','".$$txt_ac_description."','".$$txt_amount."','".$$txt_capacity_min."','".$$txt_capacity_pcs."','".$$txt_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',1)";
			$id_dtls=$id_dtls+1;

			}

		}
			//  echo "10**insert into lib_process_ac_head_standard_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);die;


		$rID=sql_insert("lib_process_ac_head_standard_mst",$field_array,$data_array,0);
		$rID1=sql_insert("lib_process_ac_head_standard_dtls ",$field_array_dtls,$data_array_dtls,0);

		//echo '10**'.$rID.'**'.$rID1;oci_rollback($con);die;

		if($db_type==0)
		{
			if($rID==1 && $rID1==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$mst_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1)
			{
				oci_commit($con);  
				echo "0**".$mst_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
		
	}
}  

?>

