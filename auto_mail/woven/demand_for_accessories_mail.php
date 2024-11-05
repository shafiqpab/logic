<?
date_default_timezone_set("Asia/Dhaka");
require_once('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
require_once('../setting/mail_setting.php');
extract($_REQUEST);

list($update_id,$company_id,$template_id)=explode('__',$data);


		$supplier_arr =return_library_array('SELECT id, SUPPLIER_NAME FROM LIB_SUPPLIER where is_deleted=0 and status_active =1','id','SUPPLIER_NAME');
		
		
		$data_array=sql_select("select id as ID, sys_number as SYS_NUMBER, buyer_id as BUYER_ID, deling_merchant_id as DELING_MERCHANT_ID, job_id as JOB_ID, style_ref_no as STYLE_REF_NO, demand_date as DEMAND_DATE,inserted_by as INSERTED_BY,REMARKS,CS_REQ_DATE from scm_demand_mst where id='$update_id' and is_deleted=0 and status_active=1");
		$inserted_by=$data_array[0]['INSERTED_BY'];
		
		
		$buyer_arr =return_library_array('SELECT id, buyer_name FROM lib_buyer where is_deleted=0 and status_active =1','id','buyer_name');
		$merchant_arr =return_library_array('SELECT b.id,b.team_member_name from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and a.PROJECT_TYPE=2 and b.status_active =1 and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0','id','team_member_name');

		$data_dtls_array=sql_select("SELECT a.DELING_MERCHANT_ID,a.TEAM_LEADER_ID,a.INSERTED_BY,b.id as ID,b.main_group_id as MAIN_GROUP_ID,b.item_group_id as ITEM_GROUP_ID, b.pre_cost_dtls_id as PRE_COST_DTLS_ID, b.brand_supplier as BRAND_SUPPLIER, b.item_description as ITEM_DESCRIPTION, b.nominate_supplier_id as NOMINATE_SUPPLIER_ID, b.uom as UOM, b.req_qty as REQ_QTY, b.stock_qty as STOCK_QTY, b.req_rate as REQ_RATE, b.req_amount as REQ_AMOUNT ,b.job_id as JOB_ID, b.job_no as JOB_NO, b.sub_date as SUB_DATE,b.REMARKS
		from SCM_DEMAND_MST a,scm_demand_dtls b where a.id=b.mst_id and b.mst_id='$update_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
		
		
		
		$supplier_data=sql_select("SELECT id as ID, contact_no as CONTACT_NO, email as EMAIL from lib_supplier where is_deleted=0 and status_active=1");
		$supp_info=array();
		foreach($supplier_data as $value){
			$supp_info[$value['ID']]['contact']=$value['CONTACT_NO'];
			$supp_info[$value['ID']]['email']=$value['EMAIL'];
		}
		$sql_sc = sql_select("SELECT  b.id as ID, d.contract_no as CONTRACT_NO
		from wo_po_break_down a, wo_po_details_master b, com_sales_contract_order_info c,com_sales_contract d
		where a.job_no_mst = b.job_no and a.id=c.wo_po_break_down_id and c.com_sales_contract_id=d.id and b.id in(".$data_array[0]['JOB_ID'].") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by b.id, d.contract_no ");
		$sql_lc = sql_select("SELECT  b.id as ID, d.export_lc_no as EXPORT_LC_NO
		from wo_po_break_down a, wo_po_details_master b, com_export_lc_order_info c,com_export_lc d
		where a.job_no_mst = b.job_no and a.id=c.wo_po_break_down_id and c.com_export_lc_id=d.id and b.id in(".$data_array[0]['JOB_ID'].") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by b.id, d.export_lc_no");

		$sql_sc_arr=array();$sql_lc_arr=array();
		if(count($sql_sc)>0){
			foreach($sql_sc as $val){
				$sql_sc_arr[$val['ID']]=$val['CONTRACT_NO'];
			}
		}

		if(count($sql_lc)>0){
			foreach($sql_lc as $val){
				$sql_lc_arr[$val['ID']]=$val['EXPORT_LC_NO'];
			}
		}
		//$main_group_arr = return_library_array("select id, main_group_name from lib_main_group where is_deleted=0","id","main_group_name");
		$item_arr = return_library_array("select id, item_name from lib_item_group where is_deleted=0","id","item_name");
		
		$userSql="SELECT ID,USER_FULL_NAME,USER_EMAIL from user_passwd where STATUS_ACTIVE=1";
		$userSqlRes = sql_select($userSql);
		foreach($userSqlRes as $row)
		{
			$user_lib_name[$row[ID]]=$row[USER_FULL_NAME];
			$user_mail_arr[$row[ID]]=$row[USER_EMAIL];

		}
		//$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
		
		
		
		
		$main_group_sql = "select ID, MAIN_GROUP_NAME,ITEM_GROUP_ID,USER_ID from lib_main_group where is_deleted=0";
		$main_group_sql_res = sql_select($main_group_sql);
		foreach($main_group_sql_res as $row)
		{
			$main_group_arr[$row[ID]]=$row[MAIN_GROUP_NAME];
			
			foreach(explode(',',$row[ITEM_GROUP_ID]) as $item_group_id){
				foreach(explode(',',$row[USER_ID]) as $user_id){
					$group_user_id_arr[$row[ID]][$item_group_id][$user_id]=$user_id;
				}
			}

		}
		
		
$inser_user_id = $data_dtls_array[0][INSERTED_BY];	
$team_member_id = $data_dtls_array[0][DELING_MERCHANT_ID];	
$team_leader_id = $data_dtls_array[0][TEAM_LEADER_ID];	
 

		
$tema_leader_arr = return_library_array("select id, TEAM_LEADER_EMAIL from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 and id=$team_leader_id","id","TEAM_LEADER_EMAIL");
$tema_member_arr = return_library_array("select b.id,b.TEAM_MEMBER_EMAIL from lib_mkt_team_member_info b where    b.status_active =1 and b.is_deleted=0 and id=$team_member_id","id","TEAM_MEMBER_EMAIL");
		
 $defaultMailAdd=$user_mail_arr[$inser_user_id].','.$tema_leader_arr[$team_leader_id].','.$tema_member_arr[$team_member_id];		
		
 		
		
		foreach($data_dtls_array as $row)
		{
			$dtls_id_arr[$row['ID']]=$row['ID'];
			if($row['NOMINATE_SUPPLIER_ID']==0){
				$nominate_supplier[0]="Non-Nominated";
			}else{
				$nominate_supplier=array();
				$supp_contact=array();
				$supp_email=array();
				foreach(explode(',',$row['NOMINATE_SUPPLIER_ID']) as $nsi){
					$nominate_supplier[$nsi]= $supplier_arr[$nsi];
					$supp_contact[$nsi]=$supp_info[$nsi]['contact'];
					$supp_email[$nsi]=$supp_info[$nsi]['email'];
				}
			}

			foreach(explode(',',$row['JOB_ID']) as $value)
			{
				if($sql_sc_arr[$value]!=''){$master_lc_sc_arr[$value]=$sql_sc_arr[$value];}
				if($sql_lc_arr[$value]!=''){$master_lc_sc_arr[$value]=$sql_lc_arr[$value];}
			}
			$cs_qty=$row['REQ_QTY']-$row['STOCK_QTY'];
			
			//print_r($group_user_id_arr[$row['MAIN_GROUP_ID']][$row['ITEM_GROUP_ID']]);
			
			
			//Data ready by group lib setup...........................................................
				$allDataArr[$row['ITEM_GROUP_ID']]=array(
					"Master LC Number" =>implode(',',$master_lc_sc_arr),
					"Job NO" =>$row['JOB_NO'],
					"Buyer Style" =>$data_array[0]['STYLE_REF_NO'],
					"Samp sub date" =>change_date_format($row['SUB_DATE']),
					"Main Group" =>$main_group_arr[$row['MAIN_GROUP_ID']],
					"Items Name" =>$item_arr[$row['ITEM_GROUP_ID']],
					"Item Ref/Code" =>$row['BRAND_SUPPLIER'],
					"Items Details" =>$row['ITEM_DESCRIPTION'],
					"UOM" =>$unit_of_measurement[$row['UOM']],
					"Required Qty" => number_format($row['REQ_QTY'],2),
					"Leftover Stock Qty" =>number_format($row['STOCK_QTY'],2),
					"For CS qty" =>number_format($cs_qty,2),
					"Costing Price" =>number_format($row['REQ_AMOUNT'],2),
					"Nominated/ Non-Nominated" =>$nominate_supplier,
					"Vendor Contact number" =>$supp_contact,
					"Vendor Mail ID" =>$supp_email,
					"Remarks" =>$row['REMARKS'],
					"ID" =>$row['ID']
				);
			
			
			//Data ready by group lib setup...........................................................
			foreach($group_user_id_arr[$row['MAIN_GROUP_ID']][$row['ITEM_GROUP_ID']] as $user_id){
				$dataArr[$user_id][$row['ITEM_GROUP_ID']]=array(
					"Master LC Number" =>implode(',',$master_lc_sc_arr),
					"Job NO" =>$row['JOB_NO'],
					"Buyer Style" =>$data_array[0]['STYLE_REF_NO'],
					"Samp sub date" =>change_date_format($row['SUB_DATE']),
					"Main Group" =>$main_group_arr[$row['MAIN_GROUP_ID']],
					"Items Name" =>$item_arr[$row['ITEM_GROUP_ID']],
					"Item Ref/Code" =>$row['BRAND_SUPPLIER'],
					"Items Details" =>$row['ITEM_DESCRIPTION'],
					"UOM" =>$unit_of_measurement[$row['UOM']],
					"Required Qty" => number_format($row['REQ_QTY'],2),
					"Leftover Stock Qty" =>number_format($row['STOCK_QTY'],2),
					"For CS qty" =>number_format($cs_qty,2),
					"Costing Price" =>number_format($row['REQ_AMOUNT'],2),
					"Nominated/ Non-Nominated" =>$nominate_supplier,
					"Vendor Contact number" =>$supp_contact,
					"Vendor Mail ID" =>$supp_email,
					"Remarks" =>$row['REMARKS'],
					"ID" =>$row['ID']
				);
				
			}
			
		}
				
		//var_dump($dataArr);
				
	 
	$imgSql="select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where  FORM_NAME = 'demand_for_accessories' ".where_con_using_array($dtls_id_arr,1,'MASTER_TBLE_ID')."";
	//echo $imgSql;die;
	$imgSqlResult=sql_select($imgSql);
	$uploadedImagArr=array();
	foreach($imgSqlResult as $row)
	{
		$uploadedImagArr[$row[MASTER_TBLE_ID]]=$row[IMAGE_LOCATION];	
	}
	 
	 
	 
	 
		$headerArr=array("Master LC Number","Job NO","Buyer Style","Samp sub date","Main Group","Items Name","Item Ref/Code","Items Details","UOM","Required Qty","Leftover Stock Qty","For CS qty","Costing Price","Nominated/ Non-Nominated","Vendor Contact number","Vendor Mail ID","Remarks");
		
		
		$width = 1280;
		
		
		
		foreach($dataArr as $user_id=>$userItemsArr){
		ob_start();
		?>
        Dear Sir/Madam,<br />
        Please fined the below demand of trims/accessories for your kind information.<br />
          <table width="<?=$width;?>">
                <tr>
                    <td>
                        <strong>Buyer: &nbsp;</strong>
                        <strong><?= $buyer_arr[$data_array[0]['BUYER_ID']]; ?></strong>
                    </td>
                    <td><strong>Demand No.: &nbsp;</strong>
                    <strong><?= $data_array[0]['SYS_NUMBER']; ?></strong></td>
                    <td>
                        <strong>Merchandiser: &nbsp;</strong>
                        <strong><?= $merchant_arr[$data_array[0]['DELING_MERCHANT_ID']]; ?></strong>
                    </td>
                    <td>
                        <strong>Date: &nbsp;</strong>
                        <strong><?= change_date_format($data_array[0]['DEMAND_DATE']); ?></strong>
                    </td>
                    <td>
                        <strong>CS Required Date: &nbsp;</strong>
                        <strong><?= change_date_format($data_array[0]['CS_REQ_DATE']); ?></strong>
                    </td>
                    
                </tr>
                <tr>
                	<td>Remarks:</td>
                    <td colspan="8"><?=$data_array[0]['REMARKS'];?></td>
                </tr>
         </table>
        
        <table cellspacing="0" border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <tr>
                    <th colspan="<?=count($headerArr)?>" align="center" ><strong>DEMAND/ REQUISITION</strong></th>
                </tr>
                <tr>
                    <? foreach($headerArr as $title){ ?>
                    <th><?=$title;?></th>
                    <? } ?>
                </tr>
            </thead>
            <tbody>
                <? 
				$i=1;
				$att_file_arr=array();
				foreach($userItemsArr as $row){
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
				?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <? foreach($headerArr as $title){ 
					if($title=="Nominated/ Non-Nominated" || $title=="Vendor Contact number" ||$title=="Vendor Mail ID" ){
						echo "<td>";
						foreach($row[$title] as $nsn){
							echo $nsn;
							if(end($row[$title])!=$nsn){echo "<hr>";}
						}
						echo "</td>";
					}
					else{echo "<td>".$row[$title]."</td>";}
					?>
                    <? } ?>
                </tr>
                <?  
					if($uploadedImagArr[$row[ID]]){
						$att_file_arr[]='../../'.$uploadedImagArr[$row[ID]].'**'.$uploadedImagArr[$row[ID]];
					}
				$i++;
				} ?>
            </tbody>
        </table>
        <? echo signature_table(235, $company_id, "1300",$template_id,50,$user_lib_name[$inserted_by]); ?>
        <br>
        
        <?
		
			$message = ob_get_contents();
			ob_clean();
			$to=$user_mail_arr[$user_id];
			$subject="DEMAND/ REQUISITION";
			$header=mailHeader();
			if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr );}
			
			//print_r($att_file_arr);
			//echo $to.$message;
			
		}
		
		
		
		
		ob_start();
		?>
        Dear Sir/Madam,<br />
        Please fined the below demand of trims/accessories for your kind information.<br />
          <table width="<?=$width;?>">
                <tr>
                    <td>
                        <strong>Buyer: &nbsp;</strong>
                        <strong><?= $buyer_arr[$data_array[0]['BUYER_ID']]; ?></strong>
                    </td>
                    <td><strong>Demand No.: &nbsp;</strong>
                    <strong><?= $data_array[0]['SYS_NUMBER']; ?></strong></td>
                    <td>
                        <strong>Merchandiser: &nbsp;</strong>
                        <strong><?= $merchant_arr[$data_array[0]['DELING_MERCHANT_ID']]; ?></strong>
                    </td>
                    <td>
                        <strong>Date: &nbsp;</strong>
                        <strong><?= change_date_format($data_array[0]['DEMAND_DATE']); ?></strong>
                    </td>
                    <td>
                        <strong>CS Required Date: &nbsp;</strong>
                        <strong><?= change_date_format($data_array[0]['CS_REQ_DATE']); ?></strong>
                    </td>
                </tr>
                <tr>
                	<td>Remarks:</td>
                    <td colspan="8"><?=$data_array[0]['REMARKS'];?></td>
                </tr>
         </table>
        
        <table cellspacing="0" border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <tr>
                    <th colspan="<?=count($headerArr)?>" align="center" ><strong>DEMAND/ REQUISITION</strong></th>
                </tr>
                <tr>
                    <? foreach($headerArr as $title){ ?>
                    <th><?=$title;?></th>
                    <? } ?>
                </tr>
            </thead>
            <tbody>
                <? 
				$i=1;
				$att_file_arr=array();
				foreach($allDataArr as $row){
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
				?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <? foreach($headerArr as $title){ 
					if($title=="Nominated/ Non-Nominated" || $title=="Vendor Contact number" ||$title=="Vendor Mail ID" ){
						echo "<td>";
						foreach($row[$title] as $nsn){
							echo $nsn;
							if(end($row[$title])!=$nsn){echo "<hr>";}
						}
						echo "</td>";
					}
					else{echo "<td>".$row[$title]."</td>";}
					?>
                    <? } ?>
                </tr>
                <?
					if($uploadedImagArr[$row[ID]]){
						$att_file_arr[]='../../'.$uploadedImagArr[$row[ID]].'**'.$uploadedImagArr[$row[ID]];
					} 
				$i++;
				} ?>
            </tbody>
        </table>
        <? echo signature_table(235, $company_id, "1300",$template_id,50,$user_lib_name[$inserted_by]); ?>
        <br>
        
        <?
		
			$message = ob_get_contents();
			ob_clean();
			$to= $defaultMailAdd;
			$subject="DEMAND/ REQUISITION";
			$header=mailHeader();
			if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr );}
			
			//print_r($att_file_arr);
			//echo $to.$message;
			 
		
		
		?>