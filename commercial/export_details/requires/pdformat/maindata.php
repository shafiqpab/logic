<?
	require('invoice.php');
	//extract($_REQUEST);
	$SupplierArr=array();
	$sqlSupplier=sql_select("select s.id, s.supplier_name from lib_supplier s, lib_supplier_tag_company b where s.status_active =1 and s.is_deleted=0 and b.supplier_id=s.id and s.id in (select supplier_id from lib_supplier_party_type where party_type in (30,31,32))");
	foreach($sqlSupplier as $row){
		$SupplierArr[$row[csf('id')]]=$row[csf('supplier_name')];
	}
	
	$applicant_sql=sql_select( "select a.id, a.buyer_name, a.short_name, a.address_1 from lib_buyer a");
	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row[csf("id")]]["buyer_name"]=$row[csf("buyer_name")];
		$buyer_name_arr[$row[csf("id")]]["address_1"]=$row[csf("address_1")];
	}
	$bank_sql=sql_select( "select id, bank_name, address, swift_code from lib_bank");
	foreach($bank_sql as $row)
	{
		$bank_name_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_name_arr[$row[csf("id")]]["address"]=$row[csf("address")];
		$bank_name_arr[$row[csf("id")]]["swift_code"]=$row[csf("swift_code")];
	}
	$bank_account_sql=sql_select( "select id, account_id, account_type, account_no from lib_bank_account where is_deleted=0 ");
	foreach($bank_account_sql as $row)
	{
		$bank_acc_arr[$row[csf("account_id")]][$row[csf("account_type")]]["account_no"]=$row[csf("account_no")];
	}
	$inv_master_data=sql_select("SELECT id, benificiary_id, buyer_id, location_id, invoice_no, invoice_date, exp_form_no, exp_form_date, is_lc, lc_sc_id,  bl_no, feeder_vessel, inco_term, inco_term_place, shipping_mode, port_of_entry, port_of_loading, port_of_discharge, main_mark, side_mark, net_weight, gross_weight, cbm_qnty, place_of_delivery, delv_no, consignee, notifying_party, item_description, discount_ammount, bonus_ammount, commission, total_carton_qnty, bl_date, hs_code, mother_vessel, category_no, forwarder_name, etd,co_no, total_measurment, invoice_value, net_invo_value, upcharge, other_discount_amt,claim_ammount,atsite_discount_amt from com_export_invoice_ship_mst where id=$update_id");
	$id=$inv_master_data[0][csf("id")];
	$benificiary_id=$inv_master_data[0][csf("benificiary_id")];
	$buyer_id=$inv_master_data[0][csf("buyer_id")];
	$location_id=$inv_master_data[0][csf("location_id")];
	$invoice_no=$inv_master_data[0][csf("invoice_no")];
	$invoice_date=$inv_master_data[0][csf("invoice_date")];
	$exp_form_no=$inv_master_data[0][csf("exp_form_no")];
	$exp_form_date=$inv_master_data[0][csf("exp_form_date")];
	$is_lc=$inv_master_data[0][csf("is_lc")];
	$lc_sc_id=$inv_master_data[0][csf("lc_sc_id")];
	$bl_no=$inv_master_data[0][csf("bl_no")];
	$feeder_vessel=$inv_master_data[0][csf("feeder_vessel")];
	$inco_term=$inv_master_data[0][csf("inco_term")];
	$inco_term_place=$inv_master_data[0][csf("inco_term_place")];
	$shipping_mode=$inv_master_data[0][csf("shipping_mode")];
	$port_of_entry=$inv_master_data[0][csf("port_of_entry")];
	$port_of_loading=$inv_master_data[0][csf("port_of_loading")];
	$port_of_discharge=$inv_master_data[0][csf("port_of_discharge")];
	$main_mark=$inv_master_data[0][csf("main_mark")];
	$side_mark=$inv_master_data[0][csf("side_mark")];
	$net_weight=$inv_master_data[0][csf("net_weight")];
	$gross_weight=$inv_master_data[0][csf("gross_weight")];
	$cbm_qnty=$inv_master_data[0][csf("cbm_qnty")];
	$place_of_delivery=$inv_master_data[0][csf("place_of_delivery")];
	$delv_no=$inv_master_data[0][csf("delv_no")];
	$consignee=$inv_master_data[0][csf("consignee")];
	$notifying_party=$inv_master_data[0][csf("notifying_party")];
	$item_description=$inv_master_data[0][csf("item_description")];
	$discount_ammount=$inv_master_data[0][csf("discount_ammount")];
	$upcharge_ammount=$inv_master_data[0][csf("upcharge")];
	$bonus_ammount=$inv_master_data[0][csf("bonus_ammount")];
	$claim_ammount=$inv_master_data[0][csf("claim_ammount")];
	$atsite_discount_amt=$inv_master_data[0][csf("atsite_discount_amt")];
	$commission=$inv_master_data[0][csf("commission")];
	$total_carton_qnty=$inv_master_data[0][csf("total_carton_qnty")];
	$bl_date=$inv_master_data[0][csf("bl_date")];
	$hs_code=$inv_master_data[0][csf("hs_code")];
	$mother_vessel=$inv_master_data[0][csf("mother_vessel")];
	$mother_vessel=$inv_master_data[0][csf("mother_vessel")];
	$category_no=$inv_master_data[0][csf("category_no")];
	$forwarder_name=$inv_master_data[0][csf("forwarder_name")];
	$etd=$inv_master_data[0][csf("etd")];
	$co_no=$inv_master_data[0][csf("co_no")];
	$total_measurment=$inv_master_data[0][csf("total_measurment")];
	$net_invo_value=$inv_master_data[0][csf("net_invo_value")];
	$total_discount=$inv_master_data[0][csf("invoice_value")]-$inv_master_data[0][csf("net_invo_value")];
	$other_discount_amt=$inv_master_data[0][csf("other_discount_amt")];
	
	$itemIdArr=array();
	$setQtyArr=array();
	$poIdArr=array();
	$dtls_sql="select a.id as dtls_id, a.po_breakdown_id,c.total_set_qnty,c.gmts_item_id from  com_export_invoice_ship_dtls a,  wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$update_id";
	$PO_agent=sql_select($dtls_sql);
	foreach($PO_agent as $row){
		$poIdArr[]=$row[csf('po_breakdown_id')];
		$setQtyArr[$row[csf('po_breakdown_id')]]=$row[csf('total_set_qnty')];
		$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
		foreach($gmts_item_id as $item=>$ivalue){
			$itemIdArr[$row[csf('po_breakdown_id')]][$item]=$garments_item[$ivalue];
		}
	}
	
	$carton_arr=array();
	$sqlCarton=sql_select("select a.id,a.sys_number, a.dl_no, b.delivery_mst_id,b.po_break_down_id,b.total_carton_qnty,b.carton_qnty from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where a.id=b.delivery_mst_id and b.po_break_down_id in(".implode(",",$poIdArr).")");
	foreach($sqlCarton as $rowCarton)
	{
		$carton_arr[$rowCarton[csf('po_break_down_id')]]=$rowCarton[csf('total_carton_qnty')];
	}
	$carton_inv_arr=array();
	$sqlInvCarton=sql_select("select a.id,a.sys_number, a.dl_no, b.delivery_mst_id,b.po_break_down_id,b.total_carton_qnty,b.carton_qnty, b.invoice_no from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where a.id=b.delivery_mst_id and b.po_break_down_id in(".implode(",",$poIdArr).")");
	foreach($sqlInvCarton as $rowInvCarton)
	{
		$carton_inv_arr[$rowInvCarton[csf('po_break_down_id')]][$rowInvCarton[csf('invoice_no')]]+=$rowInvCarton[csf('total_carton_qnty')];
	}
	$agent_id="";
	$fristPo=array_shift($poIdArr);
	$sql_agent=sql_select("select agent_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$fristPo'");
	foreach($sql_agent as $row_agent){
		$agent_id=$row_agent[csf('agent_name')];
	}
	
	
	if($is_lc==1)
	{
		$lc_sc_data=sql_select("select id, export_lc_no, lc_date, notifying_party, consignee, issuing_bank_name, negotiating_bank, lien_bank, pay_term, applicant_name,inco_term,lien_bank,nominated_shipp_line, buyer_name from com_export_lc where id='".$lc_sc_id."' ");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row[csf("id")];
			$lc_sc_no=$row[csf("export_lc_no")];
			$lc_sc_date=change_date_format($row[csf("lc_date")]);
			//$notifying_party=$row[csf("notifying_party")];
			//$consignee=$row[csf("consignee")];//also notify party
			$issuing_bank_name=$row[csf("issuing_bank_name")];
			$negotiating_bank=$row[csf("lien_bank")];
			$pay_term_id=$row[csf("pay_term")];
			$applicant_name=$row[csf("applicant_name")];
			$buyer_name=$row[csf("buyer_name")];
			// $inco_term=$row[csf("inco_term")];
			$lien_bank=$row[csf("lien_bank")];
			$shipping_line=$row[csf("nominated_shipp_line")];
			$negotiating_bank_text=$row[csf("negotiating_bank")];
		}
		
		$cate_hs_sql=sql_select("select wo_po_break_down_id, fabric_description, category_no, hs_code from com_export_lc_order_info where com_export_lc_id='".$lc_sc_id."'");
		foreach($cate_hs_sql as $row)
		{
			$order_la_data[$row[csf("wo_po_break_down_id")]]["category_no"]=$row[csf("category_no")];
			$order_la_data[$row[csf("wo_po_break_down_id")]]["hs_code"]=$row[csf("hs_code")];
			$order_la_data[$row[csf("wo_po_break_down_id")]]["fabric_description"]=$row[csf("fabric_description")];
			$all_order_id[$row[csf("wo_po_break_down_id")]]=$row[csf("wo_po_break_down_id")];
		}

		$article_res = sql_select("select a.article_number, a.po_break_down_id from wo_po_color_size_breakdown a, com_export_lc_order_info b where a.po_break_down_id=b.wo_po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.com_export_lc_id=$lc_sc_id ");
		foreach ($article_res as $artiVal)
		{
			if($artiVal["ARTICLE_NUMBER"] != "")
			{
				if($articleNoArr[$artiVal["PO_BREAK_DOWN_ID"]] == "")
				{
					$articleNoArr[$artiVal["PO_BREAK_DOWN_ID"]] = $artiVal["ARTICLE_NUMBER"];
				}else{
					$articleNoArr[$artiVal["PO_BREAK_DOWN_ID"]] .= ", ".$artiVal["ARTICLE_NUMBER"];
				}
			}
		}
	}
	else
	{
		$lc_sc_data=sql_select("select id, contract_no, contract_date, notifying_party, consignee, lien_bank, pay_term, applicant_name,inco_term,lien_bank,shipping_line,buyer_name from com_sales_contract where id='".$lc_sc_id."'  and status_active=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row[csf("id")];
			$lc_sc_no=$row[csf("contract_no")];
			$lc_sc_date=change_date_format($row[csf("contract_date")]);
			//$notifying_party=$row[csf("notifying_party")];
			//$consignee=$row[csf("consignee")];//also notify party
			$issuing_bank_name="";
			$negotiating_bank=$row[csf("lien_bank")];
			$pay_term_id=$row[csf("pay_term")];
			$applicant_name=$row[csf("applicant_name")];
			$buyer_name=$row[csf("buyer_name")];
			// $inco_term=$row[csf("inco_term")];
			$lien_bank=$row[csf("lien_bank")];
			$shipping_line=$row[csf("shipping_line")];
			$negotiating_bank_text="";
		}
		
		$cate_hs_sql=sql_select("select wo_po_break_down_id, fabric_description, category_no, hs_code from com_sales_contract_order_info where com_sales_contract_id='".$lc_sc_id."' and status_active=1");
		foreach($cate_hs_sql as $row)
		{
			$order_la_data[$row[csf("wo_po_break_down_id")]]["category_no"]=$row[csf("category_no")];
			$order_la_data[$row[csf("wo_po_break_down_id")]]["hs_code"]=$row[csf("hs_code")];
			$order_la_data[$row[csf("wo_po_break_down_id")]]["fabric_description"]=$row[csf("fabric_description")];
			$all_order_id[$row[csf("wo_po_break_down_id")]]=$row[csf("wo_po_break_down_id")];
		}

		$article_res = sql_select("select a.article_number as ARTICLE_NUMBER, a.po_break_down_id as PO_BREAK_DOWN_ID from wo_po_color_size_breakdown a, com_sales_contract_order_info b where a.po_break_down_id= b.wo_po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.com_sales_contract_id=$lc_sc_id");
		foreach ($article_res as $artiVal)
		{
			if($artiVal["ARTICLE_NUMBER"] != "")
			{
				if($articleNoArr[$artiVal['PO_BREAK_DOWN_ID']] == "")
				{
					$articleNoArr[$artiVal['PO_BREAK_DOWN_ID']] = $artiVal['ARTICLE_NUMBER'];
				}else{
					$articleNoArr[$artiVal['PO_BREAK_DOWN_ID']] .= ", ".$artiVal['ARTICLE_NUMBER'];
				}
			}
		}
	}
	
	
	$company_name_sql=sql_select( "select id, company_name, company_short_name, contract_person, plot_no, level_no, road_no, block_no, city, country_id,erc_no from lib_company where id ='$benificiary_id'");
	foreach($company_name_sql as $row)
	{
		$company_name=$row[csf("company_name")];
		$company_short_name=$row[csf("company_short_name")];
		$contract_person=$row[csf("contract_person")];
		$plot_no=$row[csf("plot_no")];
		$level_no=$row[csf("level_no")];
		$road_no=$row[csf("road_no")];
		$block_no=$row[csf("block_no")];
		$city=$row[csf("city")];
		$country_id=$row[csf("country_id")];
		$erc_no=$row[csf("erc_no")];
		
	}
	
	$country_name=return_field_value( "country_name","lib_country","id='$country_id'");
	$carrier=$SupplierArr[$forwarder_name];
	$applicant=$buyer_name_arr[$applicant_name]["buyer_name"];
	$applicantAddress=$buyer_name_arr[$applicant_name]["address_1"];
	$agent=$buyer_name_arr[$agent_id]["buyer_name"];
	$agentAddress=$buyer_name_arr[$agent_id]["address_1"];
	
	
	
	$all_order_id=implode(",",$all_order_id);
	if($all_order_id!="")
	{
		if($db_type==0)
		{
			$art_num_arr=return_library_array( "select po_break_down_id, min(article_number) as article_number from wo_po_color_size_breakdown where po_break_down_id in($all_order_id) and article_number!='' group by po_break_down_id",'po_break_down_id','article_number');
		}
		else
		{
			$art_num_arr=return_library_array( "select po_break_down_id, min(article_number) as article_number from wo_po_color_size_breakdown where po_break_down_id in($all_order_id) and article_number is not null group by po_break_down_id",'po_break_down_id','article_number');
		}
		
		$acc_po_num_arr=return_library_array( "select po_break_down_id, listagg(cast(acc_po_no as varchar(4000)),',') within group(order by acc_po_no) as acc_po_no from wo_po_acc_po_info where po_break_down_id in($all_order_id) group by po_break_down_id",'po_break_down_id','acc_po_no');	
	}
	
	
	
	
	
	
	
	$dtls_sql="select a.id as dtls_id, a.po_breakdown_id, a.current_invoice_rate, a.current_invoice_qnty, a.current_invoice_value, a.actual_po_infos, b.po_number, c.style_ref_no, c.gmts_item_id, c.order_uom, c.gmts_item_id 
	from com_export_invoice_ship_dtls a,  wo_po_break_down b, wo_po_details_master c 
	where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$update_id";
	$result=sql_select($dtls_sql);
	$row_span=count($result);
	
	$main_mark_arr=explode(",",$main_mark);
	$side_mark_arr=explode(",",$side_mark);
	$header['company']=$company_name;
	$header['address']=$city.",".return_field_value( "country_name","lib_country","id=".$country_id);
	?>