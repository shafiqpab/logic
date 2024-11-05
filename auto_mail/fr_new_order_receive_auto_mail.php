<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
require '../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
require_once('setting/mail_setting.php');

$user_library=return_library_array("select id,user_full_name from user_passwd where valid=1","id","user_full_name");
$company_library=return_library_array("select id,company_name from lib_company where id in(1,2,3,4,5,6) and  status_active=1 and is_deleted=0","id","company_name");
$buyer_library=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
//$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
$supplier_library = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$team_info_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
$agent_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );


$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),1))),'','',1);
$day_after_tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),2))),'','',1);
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),0))),'','',1);
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
$prev_fifteen_date = change_date_format(date('Y-m-d H:i:s', strtotime('-15 day', strtotime($current_date))),'','',1); 		
$select_fill="to_char(b.update_date,'DD-MM-YYYY HH12:MI:SS')";


//$prev_date="4-Nov-2017";
//$company_library=array(3=>'Test Company');



foreach($company_library as $compid=>$compname)
{
	$flag=0;
	$countRecords=0;

	$sql_count = " select count(*) as rows_num from  wo_po_details_master a, wo_po_break_down b 
		where a.job_no=b.job_no_mst and a.company_name = '$compid' and b.status_active=1 
		and b.insert_date between '".$prev_date."' and '".$current_date."' and a.is_deleted=0	and a.status_active=1";
	$result_count = sql_select( $sql_count );
		
	//echo $result_count['rows_num'];
	foreach( $result_count as $row) 
	{
		$num = $row[csf('rows_num')];
	}
	//echo $num; 
	//if($num>0){}
	//echo 'fffffffffffffffffffffffffffffffffffffffffff';   die;

	ob_start();	
	?>
    
    <table>
        <tr>
            <td align="center">
                <strong style="font-size:24px;"> <? echo $compname; ?></strong>
            </td>
        </tr>
        <tr>
            <td align="center"><strong>Insert Order Code List Of ( Date : <? echo date('d-m-Y');?> )</strong></td>
        </tr>
        <tr>
            <td>
            
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead>
                    <tr bgcolor="#999999">
                        <th width="200"><strong>O.CODE</strong></th>
                        <th width="100"><strong>O.PROD</strong></th>
                        <th width="100"><strong>O.CUST</strong></th>
                        <th width="100"><strong>O^DD:1</strong></th>
                        <th width="100"><strong>O^DQ:1</strong></th>
                        <th width="100">O^DR:1</th>
                        <th width="100"><strong>O.CONTRACT_QTY</strong></th>
                        <th width="100"><strong>O.STATUS</strong></th>
						<th width="100"><strong>O.TIME</strong></th>
                        <th width="100">O.SPRICE</th>
                        <th width="100">O.SCOST</th>
                        <th width="100"><strong>O.MCOST</strong></th>
                        <th width="100">O.UDBUYER_STYLE_QTY</th>
                        <th width="100">O.UDFACTORY</th>
                        <th width="100">O.UDGSM</th>
						<th width="100">O.UDMERCHANDISER</th>
                        <th width="100"><strong>O.UDPRE_EFFICIENCY</strong></th>
                        <th width="100"><strong>O.UDLEAD TIME</strong></th>
                        <th width="100"><strong>O.UDSEASON</strong></th>
                        <th width="100"><strong>O.UDSHIP MODE</strong></th>
                        <th width="100"><strong>O.UD1st MAT ETA</strong></th>
						<th width="100"><strong>O.UDAGENT</strong></th>
						<th width="100"><strong>O.UDBUY</strong></th>
						<th width="100"><strong>O.UDPR. LEAD TIME</strong></th>
						<th width="100"><strong>O.EVBASE</strong></th>
                        
                        </tr>
                   </thead>
					<?
					
					$cm_cost_arr=return_library_array( "select job_no,cm_cost from wo_pre_cost_dtls", "job_no", "cm_cost"  );
					
					$sql = "SELECT DISTINCT
						j.company_name,
						j.job_quantity,
						lb.buyer_name,
						j.style_ref_no,
						j.IS_REPEAT,
						(
							CASE
								WHEN j.IS_REPEAT = 1 THEN
									'YES'
								ELSE
									'No'
							END
						)   as O_UDBUY,
						( p.shipment_date - p.po_received_date )        AS lead_time,
						CASE
                            WHEN j.total_set_qnty > 1 THEN
                                  j.style_ref_no
                                  || '::'
                                  || p.po_number
                                  || '::'
                                  || lc.color_name
                                  || '::'
                                  || to_char(p.shipment_date, 'DD/MM/YYYY')
                                  || '::'
                                  || gi.item_name
                            ELSE
                                j.style_ref_no
                                  || '::'
                                  || p.po_number
                                  || '::'
                                  || lc.color_name
                                  || '::'
                                  || to_char(p.shipment_date, 'DD/MM/YYYY')
                        END AS O_CODE,
						lc.color_name,
						j.job_no,
						( lb.buyer_name
						  || '_'
						  || j.style_ref_no
						  || '_'
						  || SUM(csb.order_quantity) )                                AS buyer_style_qty,
						p.po_number,
						gi.item_name,
						(
							CASE
								WHEN j.total_set_qnty > 1 THEN
									'SET'
								ELSE
									'PCS'
							END
						)                                             AS is_set,
						j.total_set_qnty,
						(
							CASE
								WHEN j.total_set_qnty > 1 THEN
										CASE
											WHEN sn.season_name IS NULL THEN
												j.style_ref_no
												|| '::'
												|| gi.item_name
											ELSE
												j.style_ref_no
												|| '::'
												|| sn.season_name
												|| '::'
												|| gi.item_name
										END
								ELSE
									CASE
										WHEN sn.season_name IS NULL THEN
												j.style_ref_no
										ELSE
											j.style_ref_no
											|| '::'
											|| sn.season_name
									END
							END
						)                                             AS p_code,
						to_char(p.shipment_date, 'DD/MM/YYYY')  as shipment_date,
						(
							CASE
								WHEN p.is_confirmed <> 1 THEN
									'P'
								ELSE
									'F'
							END
						)                                             AS status,
						j.ship_mode,
						p.po_quantity,
						p.plan_cut,
						p.unit_price,
						(
							CASE
								WHEN p.is_confirmed <> 1 THEN
									'Projection'
								ELSE
									'Confirm'
							END
						)                                             AS confirm_status,
						(
							CASE
								WHEN sn.season_name IS NULL THEN
									CAST('0 SSN' AS NVARCHAR2(50))
								ELSE
									sn.season_name
							END
						)                                             AS season,
						sn.season_name,
						j.agent_name,  j.dealing_marchant, j.factory_marchant,
						up.team_member_name,
						prm.sew_smv,
						prm.sew_effi_percent,
						prcd.cm_cost,
						p.shipment_date  as cust_ship_date,
						p.pub_shipment_date,
						p.po_received_date,
						p.insert_date,
						p.po_number_prev,
						p.pub_shipment_date_prev,
						j.style_ref_no_prev,
						j.gmts_item_id_prev,
						csb.country_ship_date_prev,
						csb.color_number_id_prev,
						SUM(csb.order_quantity)                       AS Color_QUANTITY
					from
							 wo_po_details_master j
						inner join wo_po_break_down            p on p.job_no_mst = j.job_no
						inner join wo_po_color_size_breakdown  csb on p.id = csb.po_break_down_id
						inner join lib_garment_item            gi on gi.id = csb.item_number_id
						inner join lib_color                   lc on csb.color_number_id = lc.id
						inner join lib_buyer                   lb on lb.id = j.buyer_name
						inner join lib_mkt_team_member_info                 up on up.id = j.dealing_marchant
						left join wo_pre_cost_mst             prm on prm.job_no = j.job_no
						left join wo_pre_cost_dtls            prcd on prcd.job_no = j.job_no
						left join lib_buyer_season            sn on j.season_buyer_wise = sn.id
					WHERE 
						j.company_name IN ($compid) AND trunc(p.insert_date) between trunc(sysdate-1) and TRUNC(sysdate-1) AND j.company_name=j.working_company_id
					
					GROUP BY
							j.company_name,
							j.job_quantity,
							lb.buyer_name,
							j.style_ref_no,
							j.is_repeat,
							(
								CASE
									WHEN j.is_repeat = 1 THEN
										'YES'
									ELSE
										'No'
								END
							),
							( p.shipment_date - p.po_received_date ),
							CASE
								WHEN j.total_set_qnty > 1 THEN
										j.style_ref_no
										|| '::'
										|| p.po_number
										|| '::'
										|| lc.color_name
										|| '::'
										|| to_char(p.shipment_date, 'DD/MM/YYYY')
										|| '::'
										|| gi.item_name
								ELSE
									j.style_ref_no
									|| '::'
									|| p.po_number
									|| '::'
									|| lc.color_name
									|| '::'
									|| to_char(p.shipment_date, 'DD/MM/YYYY')
							END,
							lc.color_name,
							j.job_no,
							( lb.buyer_name
							  || '_'
							  || j.style_ref_no
							  || '_'
							  || p.po_quantity ),
							p.po_number,
							gi.item_name,
							(
								CASE
									WHEN j.total_set_qnty > 1 THEN
										'SET'
									ELSE
										'PCS'
								END
							),
							j.total_set_qnty,
							(
								CASE
									WHEN j.total_set_qnty > 1 THEN
											CASE
												WHEN sn.season_name IS NULL THEN
													j.style_ref_no
													|| '::'
													|| gi.item_name
												ELSE
													j.style_ref_no
													|| '::'
													|| sn.season_name
													|| '::'
													|| gi.item_name
											END
									ELSE
										CASE
											WHEN sn.season_name IS NULL THEN
													j.style_ref_no
											ELSE
												j.style_ref_no
												|| '::'
												|| sn.season_name
										END
								END
							),
							to_char(p.shipment_date, 'DD/MM/YYYY'),
							(
								CASE
									WHEN p.is_confirmed <> 1 THEN
										'P'
									ELSE
										'F'
								END
							),
							j.ship_mode,
							p.po_quantity,
							p.plan_cut,
							p.unit_price,
							(
								CASE
									WHEN p.is_confirmed <> 1 THEN
										'Projection'
									ELSE
										'Confirm'
								END
							),
							(
								CASE
									WHEN sn.season_name IS NULL THEN
										CAST('0 SSN' AS NVARCHAR2(50))
									ELSE
										sn.season_name
								END
							),
							sn.season_name,
							j.agent_name,
							j.dealing_marchant,
							j.factory_marchant,
							up.team_member_name,
							prm.sew_smv,
							prm.sew_effi_percent,
							prcd.cm_cost,
							p.shipment_date,
							p.pub_shipment_date,
							p.po_received_date,
							p.insert_date,
							p.po_number_prev,
							p.pub_shipment_date_prev,
							j.style_ref_no_prev,
							j.gmts_item_id_prev,
							csb.country_ship_date_prev,
							csb.color_number_id_prev
							
							ORDER BY j.style_ref_no";
						
					$result = sql_select( $sql );
					//echo $sql; echo '<br>';
					$i=1; 
					foreach( $result as $row) 
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					
						?>
								
						<tr bgcolor="<? echo $bgcolor ; ?>">
							<td align="center"><? echo $row[csf('O_CODE')];?></td>
							<td align="center"><? echo $row[csf('p_code')];?></td>
							<td align="center"><? echo $row[csf('buyer_name')];?></td>
							<td><? echo $row[csf('shipment_date')];?></td>
							<td><? echo $plancut = ($row[csf('Color_QUANTITY')] + ((3*$row[csf('Color_QUANTITY')])/100));?></td>
							<td align="center">
								<?
									$ship=$row[csf('cust_ship_date')];
									$po_rcv_date=$row[csf('po_received_date')];
									$dt = new DateTime($ship);
									$date = $dt->format('m/d/Y'); $date1=date_create($date);	$date2=date_create($po_rcv_date);
									
									$diff=date_diff($date2,$date1); 	$print=$diff->format("%R%a");	$days_diff =  substr($print, 1);
												
									if($days_diff>120){$day=40;}else if($days_diff>90){$day=40;}else if($days_diff>75){$day=35;}else if($days_diff>60){$day=30;}else if($days_diff>45){$day=22;}else if($days_diff>30){$day=17;}else{$day=0;}			
									$dt3 = new DateTime($ship);
									$date_ship = $dt3->format('Y-m-d');
									$date3=date_create($date_ship);
									date_sub($date3,date_interval_create_from_date_string("$day days"));
									$m_inhouse_date = date_format($date3,"d-m-Y");
									$date_inhouse=date_create($m_inhouse_date);
									echo  $date_inhouse = date_format($date_inhouse,"d/m/Y");
								?>
							</td>
							<td align="center"><? echo $row[csf('Color_QUANTITY')];?></td>
							<td align="center"><? echo $row[csf('status')];?></td>
							<td align="center"><? echo $team_info_arr[$row[csf('factory_marchant')]];?></td>
							<td><? echo $row[csf('unit_price')]; ?></td>
							<td align="right"> <? echo $row[csf('cm_cost')]; ?> </td>
							<td align="right"> <? echo $row[csf('cm_cost')]; ?></td>
							<td align="center"> <?  echo $row[csf('buyer_style_qty')]; ?> </td>
							<td align="right"><? echo $company_library[$row[csf('company_name')]]; ?></td>
							<td align="right"><?    ?></td>
							<td align="right"><?  echo $team_info_arr[$row[csf('dealing_marchant')]]; ?></td>
							<td align="right"><? echo $row[csf('sew_effi_percent')];?></td>
							<td align="right">
<? 
									$ship=$row[csf('cust_ship_date')];
									$dt = new DateTime($ship);
									$date = $dt->format('m/d/Y'); $date1=date_create($date);	$date2=date_create($m_inhouse_date);
						
									$diff=date_diff($date2,$date1); 	
									$print=$diff->format("%R%a"); 
									echo substr($print, 1);
									  //left($print,1);
									//$row[csf('lead_time')];//  in-housedate-shipdate
											?></td>
							<td align="center"><? echo $row[csf('season')];?></td>
							<td align="center"><? echo $shipment_mode[$row[csf('ship_mode')]];?></td>
							<td><? echo  $date_inhouse;?></td>
							<td><? echo $agent_arr[$row[csf('agent_name')]];?></td>
							<td><? echo $row[csf('O_UDBUY')];?></td>
							<td><? echo $row[csf('lead_time')];?></td>
							<td><? 
							$date_po=date_create($row[csf('po_received_date')]);
							echo date_format($date_po,"d/m/Y");?></td>							
					   </tr>
						<?
						$i++;
						$flag=1;
					}
					?>
                 </table>
            </td>
        </tr>
    </table>
	
	<br><br>
	
	<table>
        <tr>
            <td align="center"><strong>Insert Product Code List Of ( Date : <? echo date('d-m-Y');?> )</strong></td>
        </tr>
        <tr>
            <td>
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead>
                    <tr bgcolor="#999999">
                        <th width="200"><strong>P.CODE</strong></th>
                        <th width="100"><strong>P.DESCRIP</strong></th>
                        <th width="100"><strong>P.TYPE</strong></th>
                        <th width="100"><strong>P.CUST</strong></th>
                        <th width="100"><strong>P^WC:20</strong></th>
                        <th width="100">P^WC:30</th>
                        <th width="100"><strong>P^WC:40</strong></th>
                        <th width="100"><strong>P^WC:50</strong></th>
						<th width="100"><strong>P^WC:70</strong></th>
                        <th width="100">P^WC:80</th>
                        <th width="100">P^WC:90</th>
                        </tr>
                   </thead>
					<?				
					$sql_pcode = "SELECT A.*, B.* FROM
							(
								SELECT
								SUM(c.production_qnty) AS production_qnty,
								a.job_no_mst,
								c.production_type,
								c.status_active
							FROM
									 wo_po_break_down a
								INNER JOIN wo_po_color_size_breakdown      b ON a.job_no_mst = b.job_no_mst
								INNER JOIN pro_garments_production_dtls    c ON b.id = c.color_size_break_down_id
							WHERE
									c.production_type = 5 AND trunc(a.insert_date) between trunc(sysdate-600) and TRUNC(sysdate-1)
								AND c.status_active = 1
							GROUP BY
								a.job_no_mst,
								c.production_type,
								c.status_active
							)A
							RIGHT JOIN
							(
								SELECT DISTINCT
									j.company_name,
									j.job_no,
									lb.buyer_name,
									j.style_ref_no,
									j.style_description,
									gi.item_name,
									j.total_set_qnty,
									(
										CASE
											WHEN j.total_set_qnty > 1 THEN
													CASE
														WHEN sn.season_name IS NULL THEN
															j.style_ref_no
															|| '::'
															|| gi.item_name
														ELSE
															j.style_ref_no
															|| '::'
															|| sn.season_name
															|| '::'
															|| gi.item_name
													END
											ELSE
												CASE
													WHEN sn.season_name IS NULL THEN
															j.style_ref_no
													ELSE
														j.style_ref_no
														|| '::'
														|| sn.season_name
												END
										END
									) AS p_code,
									sn.season_name,
									(
										CASE
											WHEN a.embro <> 1 THEN
												'0'
											ELSE
												'1'
										END
									) as embro,
									(
										CASE
											WHEN a.wash <> 1 THEN
												'0'
											ELSE
												'1'
										END
									) as wash,
									(
										CASE
											WHEN a.embelishment <> 1 THEN
												'0'
											ELSE
												'1'
										END
									) as embelishment,
									a.smv_pcs
								FROM
									wo_po_details_master j
									INNER JOIN wo_po_break_down                           WP ON WP.JOB_NO_MST = j.job_no
									INNER JOIN lib_buyer                                lb ON lb.id = j.buyer_name
									LEFT JOIN lib_buyer_season                         sn ON j.season_buyer_wise = sn.id
									INNER JOIN wo_po_details_mas_set_details            a ON j.job_no = a.job_no
									INNER JOIN lib_garment_item                         gi ON a.gmts_item_id = gi.id
								WHERE
									j.company_name IN ($compid) AND trunc(WP.insert_date) between trunc(sysdate-1) and TRUNC(sysdate-1)
									AND a.smv_pcs <> 0 AND j.company_name=j.working_company_id
									
							) B ON A.job_no_mst = B.job_no order by b.style_ref_no";
					$result_pcode = sql_select( $sql_pcode );
										
					//echo $sql; echo '<br>';
					$i=1; 
					foreach( $result_pcode as $row) 
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

						?>
								
						<tr bgcolor="<? echo $bgcolor ; ?>">
							<td align="center"><? echo $row[csf('p_code')];?></td>
							<td align="center"><? echo $row[csf('style_description')];?></td>
							<td align="center"><? echo $row[csf('item_name')];?></td>
							<td><? echo $row[csf('buyer_name')];?></td>
							<td><? echo $row[csf('embelishment')];?></td>
							<td><? echo $row[csf('embelishment')]; ?></td>
							<td align="center"><? echo $row[csf('embro')];?></td>
							<td align="center"><? echo $row[csf('embro')];?></td>
							
							<td align="right"> <? echo $row[csf('smv_pcs')]; ?> </td>

							<td align="center"><? echo $row[csf('wash')] ?></td>
							<td><? echo $row[csf('wash')]; ?></td>
					   </tr>
						<?
						$i++;
						$flag=1;
					}
					?>
                 </table>
            </td>
        </tr>
    </table>

<?

	$to="";	
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=8 and b.mail_user_setup_id=c.id and a.company_id=$compid AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		//if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
	
	$subject = "New Order List for First React of ".$company_arr[$compid];
	$mail_body = "Please see the attached file for New Order List for First React of ".$company_arr[$compid];
	
	$message="";	
	$header=mailHeader();
	$message=ob_get_contents();
	
	ob_clean();			
	$att_file_arr=array();
	$filename="New_Order_List_".$company_arr[$compid].".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$message);
	$att_file_arr[]=$filename.'**'.$filename;
		
	$to=$to.", ".'al-amin@team.com.bd, joy@team.com.bd, azizul.haq@team.com.bd';
	
	
	
	if($compid==1)
	{
		$to=$to.", ".'raihan.uddin@team.com.bd, minhajul.arefin@gramtechknit.com, ie.shahadat@gramtechknit.com';
		if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail, $att_file_arr );}
	}
	elseif($compid==2){
		$to=$to.", ".'raihan.uddin@team.com.bd, ibrahim@team.com.bd, azmal.huda@team.com.bd, mainul.islam@team.com.bd, tuhin.Rasul@team.com.bd, shah.alam@marsstitchltd.com, tanveer.hasan@team.com.bd';
		if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail, $att_file_arr );}
	}
	elseif($compid==3){
		$to=$to.", ".'raihan.uddin@team.com.bd, pavel@brothersfashion-bd.com, emdad@brothersfashion-bd.com, tuhin.Rasul@team.com.bd, bfl_scm@brothersfashion-bd.com, abir@brothersfashion-bd.com, tanveer.hasan@team.com.bd';
		if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail, $att_file_arr );}
		//echo $message;
	}
	elseif($compid==4){
		$to=$to.", ".'raihan.uddin@team.com.bd, sohel@4ajacket.com, zillur.frp@4ajacket.com, ashraful@4ajacket.com, sajib@team.com.bd';
		if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail, $att_file_arr );}
	}
	elseif($compid==5){
		$to=$to.", ".'raihan.uddin@team.com.bd, anwar@cbm-international.com, amir@cbm-international.com, nazmul@cbm-international.com, tuhin.Rasul@team.com.bd';
		if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail, $att_file_arr );}
	}
	else{
		$to=$to.", ".'al-amin@team.com.bd, tanveer.hasan@team.com.bd';
		if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail, $att_file_arr );}
	}
 	
	
	
	//if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail, $att_file_arr );}
	/*
	if($compid==3)
	{
		$to=$to.", ".'al-amin@team.com.bd, tanveer.hasan@team.com.bd, joy@team.com.bd';
		//echo $message;
		if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
	}
	else{
			//$to=$to.", ".'al-amin@team.com.bd';
		}
	*/
	
 //$message=ob_get_contents();
//echo $message;
}


?> 