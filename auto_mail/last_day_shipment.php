<?php 
//Developed by (Alamin Team IT)
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
require '../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
//require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

	$company_library =return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	
	$inspection_date_arr=return_library_array( "select po_break_down_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "inspection_date");
	$invoice_array=return_library_array( "select id,invoice_no from com_export_invoice_ship_mst", "id", "invoice_no"  );
	$shipping_mode_array=return_library_array( "select id,shipping_mode from com_export_invoice_ship_mst", "id", "shipping_mode"  );
	$lc_sc_type_arr=return_library_array( "select id,is_lc from com_export_invoice_ship_mst", "id", "is_lc"  );
	$lc_sc_id_array=return_library_array( "select id,lc_sc_id from com_export_invoice_ship_mst", "id", "lc_sc_id"  );
	$lc_num_arr=return_library_array( "select id,export_lc_no from com_export_lc", "id", "export_lc_no"  );
	$sc_num_arr=return_library_array( "select id,contract_no from com_sales_contract", "id", "contract_no"  );
	
	$forwarder_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
	$location_arr=return_library_array( "select id,location_name from lib_location", "id", "location_name");
	
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

	$supp_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
	$lib_country=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
	$item_library=return_library_array( "select id, item_name from lib_garment_item", "id", "item_name"  );
	
	foreach($company_library as $compid=>$compname)
	{
		$chalarr=array(); $ordarr=array();
		
		$order_sql="SELECT b.id, b.po_number, a.company_name, b.job_no_mst, a.style_ref_no, b.po_quantity, b.shipment_date, b.unit_price
					FROM   wo_po_break_down b INNER JOIN wo_po_details_master a ON b.job_no_mst = a.job_no";

		$order_sql_result=sql_select($order_sql);
		foreach($order_sql_result as $row)
		{
			$ordarr[$row[csf("id")]]['po_number']= $row[csf("po_number")];
			$ordarr[$row[csf("id")]]['style_ref_no']= $row[csf("style_ref_no")];
			$ordarr[$row[csf("id")]]['po_quantity']= $row[csf("po_quantity")];
			$ordarr[$row[csf("id")]]['shipment_date']= $row[csf("shipment_date")];
			$ordarr[$row[csf("id")]]['job_no']= $row[csf("job_no_mst")];
			$ordarr[$row[csf("id")]]['unit_price']= $row[csf("unit_price")];
		}
		
		$challan_sql_bk="SELECT a.ex_factory_date,a.ex_factory_qnty,a.carton_qnty,a.id,	a.item_number_id,a.po_break_down_id,a.garments_nature,a.shiping_mode,c.sys_number,c.company_id,
				c.location_id,c.challan_no AS challan_no,c.buyer_id,c.transport_supplier,c.delivery_date,c.lock_no,c.driver_name,c.truck_no,c.mobile_no,c.delivery_company_id,
				SUM(b.production_qnty) AS SHIP_QNTY, c.delivery_location_id, a.shiping_status, a.remarks
			FROM teamerp.pro_ex_factory_mst a
				INNER JOIN teamerp.pro_ex_factory_dtls            b ON a.id = b.mst_id
				INNER JOIN teamerp.pro_ex_factory_delivery_mst    c ON a.challan_no = c.id
			WHERE
				a.garments_nature = 3 AND a.ex_factory_date between SYSDATE-2 and sysdate-1 and c.delivery_company_id in ($compid)
			GROUP BY
				a.ex_factory_date,a.ex_factory_qnty,a.carton_qnty,a.id,	a.item_number_id,a.po_break_down_id,a.garments_nature,a.shiping_mode,c.sys_number,
				c.company_id,c.location_id,	c.challan_no,c.buyer_id,c.transport_supplier,c.delivery_date, c.lock_no, c.driver_name, c.truck_no, c.mobile_no, c.delivery_company_id,
				c.delivery_location_id, a.shiping_status, a.remarks
			Order by a.ex_factory_date desc";
			
		$challan_sql="SELECT
						a.id,    a.ex_factory_date,    a.ex_factory_qnty,    a.carton_qnty,    a.item_number_id,    a.po_break_down_id,    a.garments_nature, a.shiping_mode,   
						 c.sys_number,    c.company_id,    c.location_id,    c.challan_no,    c.buyer_id,    c.transport_supplier,    c.delivery_date,   c.lock_no, 
						c.driver_name,    c.truck_no,    c.mobile_no,    c.delivery_company_id,    c.delivery_location_id,    a.shiping_status,    a.remarks, 
						c.sys_number_prefix_num, a.total_carton_qnty
					FROM         pro_ex_factory_mst a, pro_ex_factory_delivery_mst c 
					WHERE 
						 a.status_active=1 
						AND c.status_active=1
						AND c.id = a.delivery_mst_id
						AND to_char(a.ex_factory_date, 'YYYY') = to_char(sysdate, 'YYYY')
						AND a.ex_factory_date between SYSDATE-2 and sysdate-1
						AND c.delivery_company_id IN ( $compid )
					ORDER BY  a.ex_factory_date DESC";
			
		//echo $challan_sql;
		$challan_sql_result=sql_select($challan_sql);
		foreach($challan_sql_result as $row)
		{
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['shiping_mode']=$row[csf("shiping_mode")];
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['carton_qnty']=$row[csf("total_carton_qnty")];
			//$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['carton_qnty']=$row[csf("carton_qnty")];
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['challan_no']=$row[csf("challan_no")];
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['ex_factory_date']=$row[csf("ex_factory_date")];
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['forwarder']=$row[csf("forwarder")];
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['truck_no']=$row[csf("truck_no")];
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['driver_name']=$row[csf("driver_name")];
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['mobile_no']=$row[csf("mobile_no")];
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['buyer_id']=$row[csf("buyer_id")];
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['delivery_company_id']=$row[csf("delivery_company_id")];
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['transport_supplier']=$row[csf("transport_supplier")];
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['lock_no']=$row[csf("lock_no")];
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['remarks']=$row[csf("remarks")];
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['mobile_no']=$row[csf("mobile_no")];
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['shiping_status']=$row[csf("shiping_status")];
			$chalarr[$row[csf("sys_number")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['SHIP_QNTY']=$row[csf("SHIP_QNTY")];
		}
		
		$sql_gate_pass = "SELECT a.company_id, a.sys_number,  a.challan_no, a.basis,  (a.out_date || '-' ||  a.time_hour || ':' ||  a.time_minute) as out_date_time, a.vhicle_number,  a.do_no,
						  a.mobile_no,  a.driver_name,  a.security_lock_no,  a.remarks
						  FROM inv_gate_pass_mst a WHERE  a.basis = 12 AND a.company_id in($compid)";						  
		$gate_pass_sql_result=sql_select($sql_gate_pass);
		foreach($gate_pass_sql_result as $row)
		{
			$gate_pass_arr[$row[csf("challan_no")]]['out_date_time']=$row[csf("out_date_time")];
			$gate_pass_arr[$row[csf("challan_no")]]['gate_pass_no']=$row[csf("sys_number")];
			$gate_pass_arr[$row[csf("challan_no")]]['vhicle_number']=$row[csf("vhicle_number")];
			$gate_pass_arr[$row[csf("challan_no")]]['do_no']=$row[csf("do_no")];
			$gate_pass_arr[$row[csf("challan_no")]]['security_lock_no']=$row[csf("security_lock_no")];
			$gate_pass_arr[$row[csf("challan_no")]]['driver_name']=$row[csf("driver_name")];
			$gate_pass_arr[$row[csf("challan_no")]]['mobile_no']=$row[csf("mobile_no")];
			$gate_pass_arr[$row[csf("challan_no")]]['remarks']=$row[csf("remarks")];
		}
		
		//echo "<pre>";		print_r($chalarr);		echo "</pre>";
		$lc_sql="SELECT	a.wo_po_break_down_id, b.contract_no as sales_contract_no, null as mst_lc_no
				FROM com_sales_contract_order_info a INNER JOIN com_sales_contract b ON a.com_sales_contract_id = b.id
				WHERE b.status_active = 1
					
				UNION  

				SELECT a.wo_po_break_down_id,null as sales_contract_no,	b.export_lc_no as mst_lc_no
				FROM com_export_lc_order_info a	INNER JOIN com_export_lc b ON b.id = a.com_export_lc_id
				WHERE	b.status_active = 1";

		$lc_sql_result=sql_select($lc_sql);
		foreach($lc_sql_result as $row)
		{
			$lsarr[$row[csf("wo_po_break_down_id")]]['mst_lc_no']= $row[csf("mst_lc_no")];
			$lsarr[$row[csf("wo_po_break_down_id")]]['sales_contract_no']= $row[csf("sales_contract_no")];

		}
		
		
		ob_start();
		
		?>
        <div style="width:2200x;">
                <table width="2200"  >
                    <tr>
                    <td colspan="21" class="form_caption"><strong style="font-size:16px;">Yeasterday Shipment Report of <? echo $company_arr[$compid]; ?></strong></td>
                    </tr>
                </table>
                <table width="2190" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
						<th width="165">Del Company</th>
                    	<th width="100">Challan</th>   
						<th width="100">Job No</th>              
						<th width="100">Style Ref</th>                    	
                    	<th width="100">Buyer Name</th>
                    	<th width="110">Order NO</th>
						<th width="110">Item Name</th>                    	
                    	<th width="100">LC/SC NO</th>
                    	<th width="70">Shipment Date</th>
                    	<th width="70">Ex-Fac. Date</th>
                    	<th width="70">Shipping Mode</th>
                    	<th width="80">PO Qty Pcs</th>
                    	<th width="70">Unit Price</th>
                    	<th width="80">Ex-Fact. Qty.</th>
                    	<th width="80">Carton Qty</th>
                    	<th width="80">Lock No</th>
                    	<th width="80">Vehicle No</th>
                    	<th width="130">Driver Info</th>
						<th width="125">Trasport Company</th>
                    	<th width="120">Ex-Fact Status</th>
						<th width="80">Out Date Time</th>
						<th width="80">Truck In Time</th>
						<th width="80">Gate Pass No</th>
						<th width="80">Gate Pass Remarks</th>
                    	<th>Del Remarks</th>
                    </thead>
					<tbody>
					<?
					foreach($chalarr as $chal_id=>$chal_data)
					{
						foreach($chal_data as $po_id=>$po_data)
						{
							foreach($po_data as $item_id=>$dr)
							{
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="165"><? echo $company_library[$dr['delivery_company_id']]; ?></td>
										<td width="100"><? echo $chal_id; ?></td>   
										<td width="100"><? echo $ordarr[$po_id]['job_no']; ?></td>              
										<td width="100"><? echo $ordarr[$po_id]['style_ref_no']; ?></td>                    	
										<td width="100"><? echo $buyer_arr[$dr['buyer_id']]; ?></td>
										<td width="110"><? echo $ordarr[$po_id]['po_number']; ?></td>
										<td width="110"><? echo $item_library[$item_id]; ?></td>
										
										<td width="100">
										<? 
											if($lsarr[$po_id]['mst_lc_no']=="")
											{
												echo $lsarr[$po_id]['sales_contract_no'];						
											}
											else
											{
												echo $lsarr[$po_id]['mst_lc_no'];
											}
										?></td>
										<td width="70" align="center"><? echo $ordarr[$po_id]['shipment_date'];  ?></td>
										<td width="70" align="center"><? echo $dr['ex_factory_date']; ?></td>
										<td width="70" align="center"><? echo $shipment_mode[$dr['shiping_mode']]; ?></td>
										<td width="80" align="center"><? echo $ordarr[$po_id]['po_quantity'];  ?></td>
										<td width="70" align="center"><? echo $ordarr[$po_id]['unit_price']; ?></td>
										<td width="80" align="center"><? echo $dr['ex_factory_qnty']; ?></td>
										<td width="80" align="center"><? echo $dr['carton_qnty']; ?></td>
										<td width="80" align="center"><? echo $dr['lock_no']; ?></td>
										<td width="80" align="center"><? echo $dr['truck_no']; ?></td>
										<td width="130"><? echo $dr['driver_name'].'<br>'.$dr['mobile_no']; ?></td>
										<td width="125"><? echo $supp_library[$dr['transport_supplier']]; ?></td>
										<td width="120" align="center"><? echo $shipment_status[$dr['shiping_status']]; ?></td>
										<td width="80" align="center"><? echo $gate_pass_arr[$chal_id]['out_date_time']; ?></td>
										<td width="80" align="center"><? echo $gate_pass_arr[$chal_id]['do_no']; ?></td>
										<td width="80" align="center"><? echo $gate_pass_arr[$chal_id]['gate_pass_no']; ?></td>
										<td width="80" align="center"><? echo $gate_pass_arr[$chal_id]['remarks']; ?></td>
										<td><? echo $dr['remarks']; ?></td>
									</tr>
								<?
							}
						}
					}
					?>
					
					</tbody>

                </table>
        </div>
		<br><br>
	   <?php
	   
		
		$to="";$message="";
		/*
		$sql_mail="SELECT distinct a.company_id, c.email_address,    a.mail_item,    c.user_id,    c.user_type
				FROM   mail_group_mst a, mail_group_child b, user_mail_address  c
				WHERE  b.mail_group_mst_id = a.id   AND b.mail_user_setup_id = c.id and a.company_id=$compid";
		//echo $sql_mail;
		$i=0;
		$mail_sql_res=sql_select($sql_mail);
		
		foreach($mail_sql_res as $row)
		{
			if($row[csf('email_address')] != 'mizan@team.com.bd')
			{
				if ($to=="")  
					$to=$row[csf('email_address')]; 
				else $to=$to.", ".$row[csf('email_address')]; 
			}
		}
		*/
		
		$header=mailHeader();
		
		$subject="Notification of Yesterday Shipment Qty ".$company_arr[$compid];
		$message=ob_get_contents();
		
		
		ob_clean();	
		
		$att_file_arr=array();
		$filename="Yesterday_shipment_report_".$company_arr[$compid].".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,$message);
		$att_file_arr[]=$filename.'**'.$filename;
		
		$mail_body = "Please see the attached file for yesterday shipment from ".$company_arr[$compid];

		
			$to='al-amin@team.com.bd, mofizur.rahman@team.com.bd, sayem@team.com.bd, nasir@team.com.bd';
		if($compid==1)
		{
			$to=$to.", ".'raihan.uddin@team.com.bd, merchandiser@gramtechknit.com, rasel@gramtechknit.com, azam.khan@team.com.bd, murshed.alam@team.com.bd, subhasish.biswas@team.com.bd, uzzal.dakua@gramtechknit.com,noman.rejwan@gramtechknit.com,mizan.rahman@gramtechknit.com,shahriar@gramtechknit.com,tipu@team.com.bd,rupon@gramtechknit.com, azizul.haq@team.com.bd';
			if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
		}
		elseif($compid==2){
			$to=$to.", ".'raihan.uddin@team.com.bd, jamir.khan@team.com.bd, md.shafiuzzaman@team.com.bd, tauhidul.islam@team.com.bd, md.mahbubul@team.com.bd, abdur.rouf@team.com.bd, rahman.sohel@team.com.bd, nahiyan.talukdar@team.com.bd, hamimulla.abid@team.com.bd, azmal.huda@team.com.bd, mir.forhad@team.com.bd, tuhin.Rasul@team.com.bd,  shah.alam@marsstitchltd.com, ibrahim@team.com.bd, azam.khan@team.com.bd, murshed.alam@team.com.bd, subhasish.biswas@team.com.bd, majharul.anwar@marsstitchltd.com, rayhan.rahman@marsstitchltd.com, jakir.hossain@marsstitchltd.com';
			if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
		}
		elseif($compid==3){
			$to=$to.", ".'raihan.uddin@team.com.bd, bfl_merchandisers@brothersfashion-bd.com, kutub@brothersfashion-bd.com, tanveer.hasan@team.com.bd, export.bfl@brothersfashion-bd.com, azam.khan@team.com.bd, murshed.alam@team.com.bd, subhasish.biswas@team.com.bd';
			if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
		}
		elseif($compid==4){
			$to=$to.", ".'raihan.uddin@team.com.bd, tuhin.Rasul@team.com.bd, zahedul@4ajacket.com,enamul@4ajacket.com,abdur.rahim@4ajacket.com,store3@4ajacket.com,zillur.frp@4ajacket.com,ashraful@4ajacket.com,shandhi.rozario@4ajacket.com,anwar.hossain@4ajacket.com,sajib@team.com.bd,
			zahedul@4ajacket.com,enamul@4ajacket.com,abdur.rahim@4ajacket.com,store3@4ajacket.com,zillur.frp@4ajacket.com, ashraful@4ajacket.com, shandhi.rozario@4ajacket.com, anwar.hossain@4ajacket.com, azam.khan@team.com.bd, murshed.alam@team.com.bd, subhasish.biswas@team.com.bd';
			if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
		}
		elseif($compid==5){
			
			$to=$to.", ".'raihan.uddin@team.com.bd, cbm_merchandisers@cbm-international.com, anwar@cbm-international.com, joy@team.com.bd, azam.khan@team.com.bd, murshed.alam@team.com.bd, subhasish.biswas@team.com.bd';
			if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
		}
		elseif($compid==6){
			$to=$to.", ".'md.alimujjaman@team.com.bd, raihan.uddin@team.com.bd, azam.khan@team.com.bd, murshed.alam@team.com.bd, subhasish.biswas@team.com.bd';
			if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
		}
		else{
			$to=$to.", ".'al-amin@team.com.bd';
			if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
		}
		
		
		/*
		azam.khan@team.com.bd, murshed.alam@team.com.bd, subhasish.biswas@team.com.bd, mofizur.rahman@team.com.bd, sayem@team.com.bd, nasir@team.com.bd';
		if($compid==4){
			$to=$to.", ".'sajib@team.com.bd, al-amin@team.com.bd';
		}
		*/
		
		//$to=$to.", ".'al-amin@team.com.bd, commercial.exp@team.com.bd, sayem@team.com.bd, nasir@team.com.bd, amirul.islam@team.com.bd, raihan.uddin@team.com.bd, export.bfl@brothersfashion-bd.com';
		// allmerchandiser@marsstitchltd.com, cbm_merchandisers@cbm-international.com, bfl_merchandisers@brothersfashion-bd.com, allmerchant@4ajacket.com, merchandiser@gramtechknit.com';
		//echo $to;echo '<pre>'; //die;	
		
		//if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
		//echo $message;
			
	}
	?>