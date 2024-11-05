<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');

echo load_html_head_contents("Set Order Import","../../", 1, 1, $unicode,1,'');

$txt_job_no=$_POST["txt_job_no"];

include( 'excel_reader.php' );
$output = `uname -a`;

if( isset( $_POST["submit"] ) )
{	
	error_reporting(E_ALL);
	//ini_set('display_errors', '1');
	
	extract($_REQUEST);
	
	foreach (glob("files/"."*.xls") as $filename){			
		@unlink($filename);
	}
	foreach (glob("files/"."*.xlsx") as $filename){			
		@unlink($filename);
	}
	//die;
	$source = $_FILES['uploadfile']['tmp_name'];
	$targetzip ='files/'.$_FILES['uploadfile']['name'];
	$file_name=$_FILES['uploadfile']['name'];
	unset($_SESSION['excel']);
	if (move_uploaded_file($source, $targetzip)) 
	{
		$excel = new Spreadsheet_Excel_Reader($targetzip); 
		$totalColumn=$excel->sheets[0]['numCols'];
		$totalRows=$excel->sheets[0]['numRows']; 
		//echo "<pre>";
		//print_r($excel->sheets[0]['cells']);
		$sizeSeqArr=array();
		for ($k = 1; $k <= $totalRows; $k++)
		{
			if($excel->sheets[0]['cells'][$k][1]=="Order Number") $pono=$excel->sheets[0]['cells'][$k+1][1];
			$gmtsColor=$excel->sheets[0]['cells'][$k][2]; 
			$m=1;
			for ($i = 3; $i <= $totalColumn; $i++) 
			{
				$gmtsSize=$excel->sheets[0]['cells'][$k][$i];
				if($gmtsColor=="Color + Variant" && trim($gmtsSize)!="Total" && $gmtsSize!="" && $gmtsColor!="") 
				{
					$sizeSeqArr[$pono][$m]=$gmtsSize;
					$m++;
				}
			}
			if($excel->sheets[0]['cells'][$k][1]=="GRAND TOTAL") break;
		}
		/*echo "<pre>";
		print_r($sizeSeqArr);
		die;*/
		
		$r=0; $n=1; $s=1; $stylePoDataArr=array(); $colorDataArr=array(); $sizeDataArr=array(); $sizeNameArr=array();
		for ($k = 1; $k <= $totalRows; $k++) 
		{
			if($excel->sheets[0]['cells'][$k][1]!="")
			{
				if($excel->sheets[0]['cells'][$k][1]=="Order Number")
				{
					$r++;
					$pono=$excel->sheets[0]['cells'][$k+1][1];//po
					$styleDescrip=$excel->sheets[0]['cells'][$k+1][3];//Style Description
					$styleRef=$excel->sheets[0]['cells'][$k+1][4];//Style
					$poRecDate=$excel->sheets[0]['cells'][$k+1][5];//Po Rec Date
					$countryShipDate=$excel->sheets[0]['cells'][$k+1][6];//Country Ship Date
					$orderstatus=$excel->sheets[0]['cells'][$k+1][7];//Order Status
					$buyerBrand=$excel->sheets[0]['cells'][$k+1][8];//Buyer Brand
					$poPrice=$excel->sheets[0]['cells'][$k+1][10];//Po Price
					$poCountry=$excel->sheets[0]['cells'][$k+1][11];//Delivery Country
					$pubShipDate=$excel->sheets[0]['cells'][$k+1][12];//Public Shipment Date
					$stylePoDataArr[$r]['po']=$pono.'__'.$styleRef.'__'.$styleDescrip.'__'.$poRecDate.'__'.$countryShipDate.'__'.$orderstatus.'__'.$buyerBrand.'__'.$poPrice.'__'.$poCountry.'__'.$pubShipDate;
					//echo $pono.'__'.$styleRef.'__'.$styleDescrip.'__'.$poRecDate.'__'.$countryShipDate.'__'.$orderstatus.'__'.$buyerBrand.'__'.$poPrice.'__'.$poCountry.'__'.$pubShipDate.'<br>';
				
				}
				$gmtsColor="";
				$gmtsColor=$excel->sheets[0]['cells'][$k][2];//Gmts Color
				if(trim($gmtsColor)!="") // && trim($gmtsColor)!="Order Type" && $gmtsColor!="Style Purchase Order" && $gmtsColor!="Color + Variant"
				{
					if(trim($gmtsColor)!="Order Type" && $gmtsColor!="Style Purchase Order" && $gmtsColor!="Color + Variant") $colorDataArr[$r][$n]['color']=$gmtsColor;
					//echo $pono.'__'.$gmtsColor.'__'.$k.'<br>';//Gmts Color
					
/*					if($excel->sheets[0]['cells'][$k+2][2]=="Color + Variant")
					{
*/						for ($i = 1; $i <= $totalColumn; $i++) 
						{
							$gmtsSize=$sizeSeqArr[$k][$i];//Gmts Size
							
							$sizeQty=$excel->sheets[0]['cells'][$k+3][$i];//Gmts Size;
							if(trim($gmtsSize)!="" && trim($sizeQty)!="")
							{
								$sizeDataArr[$r][$n][$gmtsSize]['size']=$gmtsSize;
								$sizeDataArr[$r][$n][$gmtsSize]['qty']=$sizeQty;
								//echo $gmtsColor.'__'.$gmtsSize.'__'.$sizeQty.'<br>';
								$s++;
							}
						}
					//}
					if(trim($gmtsColor)!="Order Type" && $gmtsColor!="Style Purchase Order" && $gmtsColor!="Color + Variant") $n++;
				}
			}
			if($excel->sheets[0]['cells'][$k][1]=="GRAND TOTAL") break;
			//if($excel->sheets[0]['cells'][$k][1]=="Order Number") $r++;
		}
	}
}

echo "<pre>";
//print_r($stylePoDataArr[3]);
print_r($colorDataArr[3]);
print_r($sizeDataArr[3]);
die;
foreach($stylePoDataArr as $pinc=>$stylepodata)
{
	//echo $stylepodata['po'].'<br>';
	foreach($colorDataArr[$pinc] as $cinc=>$gcolordata)
	{
		echo $pinc.'__'.$cinc.'__'.$gcolordata['color'].'<br>';
		foreach($sizeDataArr[$pinc][$cinc] as $sinc=>$sizedata)
		{
			if($sinc!="")
			{
			echo $pinc.'__'.$cinc.'__'.$sinc.'__'.$gcolordata['color'].'<br>';
			//echo $gcolordata['color'].'<br>';
			//echo $sizedata['size'].'-'. $sizedata['qty'].'<br>';
			}
		}
	}
}



die;
if( isset( $_POST["submit"] ) )
{	
	error_reporting(E_ALL);
	//ini_set('display_errors', '1');
	
	extract($_REQUEST);
	
	foreach (glob("files/"."*.xls") as $filename){			
		@unlink($filename);
	}
	foreach (glob("files/"."*.xlsx") as $filename){			
		@unlink($filename);
	}
	//die;
	$source = $_FILES['uploadfile']['tmp_name'];
	$targetzip ='files/'.$_FILES['uploadfile']['name'];
	$file_name=$_FILES['uploadfile']['name'];
	unset($_SESSION['excel']);
	if (move_uploaded_file($source, $targetzip)) 
	{
		$excel = new Spreadsheet_Excel_Reader($targetzip);  
		//$excel->read($targetzip);
		$card_colum=0; $m=1; $q=0; $styleItemData_arr=array(); $po_data_array=array(); $country_data_array=array(); $style_all_data_arr=array();
		$sizeNameHeadArr=array(); $str_rep=array("_", "&", "*", "(", ")", "=","'",",","\r", "\n","\t",'"','#');
		$totalColumn=$excel->sheets[0]['numCols'];
		for ($i = 1; $i <= $excel->sheets[0]['numRows']; $i++) 
		{
			$z=0;
			if($m==1)
			{
				for ($j = 35; $j <= $totalColumn; $j++) 
				{
					//Excel Column Name. Header 
					$sizeNameHeadArr[$j]=strtoupper(str_replace($str_rep,' ',$excel->sheets[0]['cells'][1][$j]));
					$z++;
					$q=1;
				}
				$m++;
			}
			else
			{
				//echo "<pre>".$z;
				//print_r($sizeNameHeadArr); die;
				
				$all_data='';
				
				$style_ref=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][13]);
				
				if(trim($style_ref)!="")
				{
					$style_description=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][14]);
					$po_number=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][26]);
					
					if($po_number=="") $po_number=$style_ref."-".$q;
					$q++;
					$countryCode=str_replace("(","[",$excel->sheets[0]['cells'][$i][21]);
					
					$countryCode=strtoupper(str_replace(")","]",$countryCode));
					
					$po_rec_date=date("Y-m-d");
					$po_ship_date=$excel->sheets[0]['cells'][$i][28];
					$avg_rate=str_replace("$","",$excel->sheets[0]['cells'][$i][22]);
					//echo $avg_rate.'<br>';
					
					$color_name=str_replace($str_rep,' ',trim($excel->sheets[0]['cells'][$i][17]));
						
					$breakdownPackType=strtoupper($excel->sheets[0]['cells'][$i][32]);
					$colorcountryQty=str_replace(",","",$excel->sheets[0]['cells'][$i][25]);
					$colorcountryPackQty=str_replace(",","",$excel->sheets[0]['cells'][$i][34]);
					
					//$po_remarks=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][46]);
					$strSetItem=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][15]);
					$exSetItem=explode("*",$excel->sheets[0]['cells'][$i][15]);
					foreach($exSetItem as $setItem)
					{
						//$po_data_array[$style_ref][$po_number]=$po_rec_date.'__'.$po_ship_date.'__'.$avg_rate.'__'.$po_remarks; 
						$all_data=$style_description.'__'.change_date_format($po_rec_date).'__'.change_date_format($po_ship_date).'__'.$avg_rate.'__'.$breakdownPackType.'__'.$colorcountryQty.'__'.$colorcountryPackQty.'__'.trim($countryCode);
						
						for ($j = 35; $j <= $totalColumn; $j++) 
						{
							$sizeQtyRatio=0;
							$sizeQtyRatio=str_replace(",","",$excel->sheets[0]['cells'][$i][$j]);
							$size_name=$sizeNameHeadArr[$j];
							//Excel Column Name. Header
							if($sizeQtyRatio>0)
							{
								if($breakdownPackType=="SINGLE")
								{
									$sizeQty=$sizeQtyRatio;
								}
								else if($breakdownPackType=="RATIO")
								{
									$sizeQty=($colorcountryQty/$colorcountryPackQty)*$sizeQtyRatio;
									
									/*if($style_ref=='222') 
									{
										echo $colorcountryQty.'='.$colorcountryPackQty.'='.$sizeQtyRatio.'='.$sizeQty.'<br>';
									}*/
									//$style_all_data_arr[$style_ref][$po_number][$setItem][$color_name][$size_name][$all_data]['ratio']+=$sizeQtyRatio;
								}
								$style_all_data_arr[$style_ref][$po_number][$setItem][$color_name][$size_name][$all_data]['poqty']+=$sizeQty;
							}
						}
						$styleItemData_arr[$style_ref][$setItem]+=$colorcountryQty;
					}
				}
			}
		}
		/*echo "<pre>";
		print_r($style_all_data_arr[222]);
		die;*/
		
		$_SESSION['excel']=$style_all_data_arr;
		$_SESSION['excelitem']=$styleItemData_arr;
		///echo "<pre>";
		//print_r($style_all_data_arr);
		die;
		?>
        <script>
			var permission='<? echo $permission; ?>';
			if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
			
			function fnc_excel_import(operation)
			{
				if($('#txt_ready_to_save').val()==0)
				{
					alert('Please check Country and Upload the file again.');
					return;
				}
				
				if( form_validation('cbo_company_id*cbo_location_id*cbo_buyer_id*cbo_product_department*cbo_currercy_id*cbo_season_id*cbo_prod_catgory*cbo_team_leader*cbo_dealing_merchant*cbo_factory_merchant*cbo_order_uom*cbo_packing','Company*Location*Buyer*Prod. Dept.*Currency*Season*Prod. Catgory*Team Leader*Dealing Marchant*Factory Merchant*Order Uom*Packing')==false)
				{
					return;
				}
				
				var rowCount = $('#tbl_set_details tr').length-2;
				for(var i=1; i<=rowCount; i++)
				{
					if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i+'*smv_'+i,'Gmts Items*Set Ratio')==false)
					{
						return;
					}
					var smv=$('#smv_'+i).val();
					if(smv==0)
					{
						alert("Smv 0 not accepted");
						return;
					}
					
					var setRatio=$('#txtsetitemratio_'+i).val();
					if(setRatio==0)
					{
						alert("Set Ratio 0 not accepted");
						return;
					}
				}
				
				fnc_itemSet_data();
				
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_buyer_id*cbo_style_owner_id*cbo_product_department*cbo_currercy_id*cbo_season_id*cbo_prod_catgory*cbo_team_leader*cbo_dealing_merchant*cbo_factory_merchant*cbo_order_uom*cbo_packing*txt_job_no*set_breck_down*item_id',"../../");
				//alert(data); return;
				freeze_window(operation);
				http.open("POST","requires/excel_order_import_bs_controller.php",true);
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
					release_freezing();
					alert(reponse[1]);
					//show_msg(trim(reponse[0]));
				}
			}
			
			function fnc_style_owner(val)
			{
				$('#cbo_style_owner_id').val(val);
			}
			
			function check_duplicate(id,td)
			{
				var item_id=document.getElementById('cboitem_'+id).value;
				var excelstyle=document.getElementById('txtexcelstyle_'+id).value;
				var row_num=$('#tbl_set_details tr').length-2;
				
				for (var k=1;k<=row_num; k++)
				{
					if(k==id)
					{
						continue;
					}
					else
					{
						if(excelstyle==document.getElementById('txtexcelstyle_'+k).value && item_id==document.getElementById('cboitem_'+k).value)
						{
							alert("Same Style and Gmts Item Duplication Not Allowed.");
							document.getElementById(td).value="0";
							document.getElementById(td).focus();
						}
					}
				}
			}
			
			function calculate_set_smv(i)
			{
				var rowCount = $('#tbl_set_details tr').length-2;
				var ddd={ dec_type:1, comma:0, currency:1}
				
				var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
				
				var sewsmv=document.getElementById('smv_'+i).value;
				var setSew_smv=txtsetitemratio*sewsmv;
				document.getElementById('smvset_'+i).value=setSew_smv;
				
				var cutsmv=document.getElementById('cutsmv_'+i).value;
				var setCut_smv=txtsetitemratio*cutsmv;
				document.getElementById('cutsmvset_'+i).value=setCut_smv;
				
				var finsmv=document.getElementById('finsmv_'+i).value;
				var setFin_smv=txtsetitemratio*finsmv;
				document.getElementById('finsmvset_'+i).value=setFin_smv;
				
				math_operation( 'tot_set_qnty', 'txtsetitemratio_', '+', rowCount,ddd);
				
				math_operation( 'tot_smv_qnty', 'smvset_', '+', rowCount,ddd);
				math_operation( 'tot_cutsmv_qnty', 'cutsmvset_', '+', rowCount,ddd);
				math_operation( 'tot_finsmv_qnty', 'finsmvset_', '+', rowCount,ddd);
			}
			
			function fnc_itemSet_data()
			{
				var rowCount = $('#tbl_set_details tr').length-2;
				var set_breck_down="";
				var item_id=""
				for(var i=1; i<=rowCount; i++)
				{
					if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i+'*smv_'+i,'Gmts Items*Set Ratio')==false)
					{
						return;
					}
					var smv=$('#smv_'+i).val();
					if(smv==0)
					{
						alert("Smv 0 not accepted");
						return;
					}
					
					var setRatio=$('#txtsetitemratio_'+i).val();
					if(setRatio==0)
					{
						alert("Set Ratio 0 not accepted");
						return;
					}
					if($('#txtexcelstyle_'+i).val()=='') $('#txtexcelstyle_'+i).val(0);
					if($('#txtexcelitem_'+i).val()=='') $('#txtexcelitem_'+i).val(0);
					
					if($('#hidquotid_'+i).val()=='') $('#hidquotid_'+i).val(0);
					if($('#cboitem_'+i).val()=='') $('#cboitem_'+i).val(0);
					if($('#cutsmv_'+i).val()=='') $('#cutsmv_'+i).val(0);
					if($('#cutsmvset_'+i).val()=='') $('#cutsmvset_'+i).val(0);
					if($('#finsmv_'+i).val()=='') $('#finsmv_'+i).val(0);
					if($('#finsmvset_'+i).val()=='') $('#finsmvset_'+i).val(0);
		
					if(set_breck_down=="")
					{
						set_breck_down+=trim($('#txtexcelstyle_'+i).val())+'_'+$('#txtexcelitem_'+i).val()+'_'+$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#cutsmv_'+i).val()+'_'+$('#cutsmvset_'+i).val()+'_'+$('#finsmv_'+i).val()+'_'+$('#finsmvset_'+i).val()+'_'+$('#hidquotid_'+i).val();
						
						item_id+=trim($('#txtexcelstyle_'+i).val())+'_'+$('#txtexcelitem_'+i).val()+'_'+$('#cboitem_'+i).val();
					}
					else
					{
						set_breck_down+="__"+trim($('#txtexcelstyle_'+i).val())+'_'+$('#txtexcelitem_'+i).val()+'_'+$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#cutsmv_'+i).val()+'_'+$('#cutsmvset_'+i).val()+'_'+$('#finsmv_'+i).val()+'_'+$('#finsmvset_'+i).val()+'_'+$('#hidquotid_'+i).val();
		
						item_id+=","+trim($('#txtexcelstyle_'+i).val())+'_'+$('#txtexcelitem_'+i).val()+'_'+$('#cboitem_'+i).val();
					}
				}
				
				$('#set_breck_down').val( set_breck_down );
				$('#item_id').val( item_id );
			}
			
		</script>
        </head>
        <body onLoad="set_hotkey();">
        <div style="width:100%;" align="center">
        <!-- Important Field outside Form -->  
            <?=load_freeze_divs ("../../",$permission);  ?>
            <fieldset style="width:1300px;">
                <form name="excelImport_1" id="excelImport_1" autocomplete="off"> 
          		<table width="1300" cellspacing="0" border="1" class="rpt_table" rules="all" >      
          	<thead>
            	<tr>
                    <th width="130" class="must_entry_caption">Company</th>
                    <th width="130" class="must_entry_caption">Location</th>
                    <th width="130" class="must_entry_caption">Buyer</th>
                    <th width="130">Style Owner</th>
                    <th width="100" class="must_entry_caption">Prod. Dept</th>
                    <th width="70" class="must_entry_caption">Currency</th>
                    <th width="60" class="must_entry_caption">Season</th>
                    <th width="80" class="must_entry_caption">Prod. Category</th>
                    <th width="100" class="must_entry_caption">Team Leader</th>
                    <th width="100" class="must_entry_caption">Dealing Merchant</th>
                    <th width="100" class="must_entry_caption">Factory Merchant</th>
                    <th width="60" class="must_entry_caption">Order Uom</th>
                    <th class="must_entry_caption">Packing</th>
                 </tr>
              </thead>
              <tbody>
              	<tr>
                	<td><?=create_drop_down( "cbo_company_id", 130, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-Select Company-", $selected, "load_drop_down( 'requires/excel_order_import_bs_controller', this.value, 'load_drop_down_location', 'location_td'); load_drop_down( 'requires/excel_order_import_bs_controller', this.value, 'load_drop_down_buyer', 'buyer_td'); fnc_style_owner(this.value);"); ?></td>
                    <td id="location_td"><?=create_drop_down( "cbo_location_id", 130, $blank_array,"", 1, "-Location-", $selected, ""); ?></td>
                    <td id="buyer_td"><?=create_drop_down( "cbo_buyer_id", 130, $blank_array,"", 1, "-Buyer-", $selected, ""); ?></td>
                    <td id="owner_td"><?=create_drop_down( "cbo_style_owner_id", 130, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-Style Owner-", $selected, ""); ?></td>
                    <td><?=create_drop_down( "cbo_product_department", 100, $product_dept, "", 1, "-Select-", $selected, "", "", "" ); ?></td>
                    <td><?=create_drop_down( "cbo_currercy_id", 70, $currency,'', 0, "",2, "" ); ?></td>
                    <td id="season_td"><?=create_drop_down( "cbo_season_id", 60, $blank_array,'', 1, "-Select Season-",$selected, "" ); ?></td>
                    <td><?=create_drop_down( "cbo_prod_catgory", 80, $product_category,"", 1, "-Category-", 1, "","","" ); ?></td>
                    <td><?=create_drop_down( "cbo_team_leader", 100, "select id, team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-Select Team-", $selected, "load_drop_down( 'requires/excel_order_import_bs_controller', this.value, 'load_drop_down_dealing_merchant', 'div_marchant'); load_drop_down( 'requires/excel_order_import_bs_controller', this.value, 'load_drop_down_factory_merchant', 'div_marchant_factory')" ); ?></td>
                    <td id="div_marchant"><?=create_drop_down( "cbo_dealing_merchant", 100, $blank_array,"", 1, "-Team Member-", $selected, "" ); ?></td>
                    <td id="div_marchant_factory"><?=create_drop_down( "cbo_factory_merchant", 100, $blank_array,"", 1, "-Fac Merchent-", $selected, "" ); ?></td>
                    <td><?=create_drop_down( "cbo_order_uom",60, $unit_of_measurement, "",1, "--", 0, "","","1,58" ); ?></td>
                    <td><?=create_drop_down( "cbo_packing", 90, $packing,"", 1, "--Select--", $selected, "","","" ); ?></td>
                </tr>
              </tbody>
           </table> 
           
           <form id="setdetails_1" autocomplete="off">
            <input type="hidden" id="set_breck_down" />
            <input type="hidden" id="item_id" />
            <input type="hidden" name="txt_job_no" id="txt_job_no" style="width:50px;" value="<?=$txt_job_no; ?>"/>
            <table width="600" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
                <thead>
                    <tr>
                    	<th width="130">Style</th>
                    	<th width="150">Excel Item</th>
                    	<th width="150" class="must_entry_caption">Item</th>
                        <th width="40" class="must_entry_caption">Set Ratio</th>
                        <th width="40" class="must_entry_caption">Sew SMV</th>
                        <th width="40">Cut SMV</th>
                        <th>Fin SMV</th>
                   </tr>
                </thead>
                <tbody>
                <?
                if(count($styleItemData_arr)>0)
				{
					$k=1; $nullItem=0;
					foreach($styleItemData_arr as $styleName=>$itemData)
					{
						foreach($itemData as $excelitem=>$poQty)
						{
							?>
							<tr id="settr_<?=$k; ?>" align="center">
								<td><input type="text" id="txtexcelstyle_<?=$k; ?>" name="txtexcelstyle_<?=$k; ?>" style="width:120px" class="text_boxes" title="<?=$styleName; ?>" value="<?=$styleName; ?>" readonly disabled /></td>
								<td><input type="text" id="txtexcelitem_<?=$k; ?>" name="txtexcelitem_<?=$k; ?>" style="width:140px" class="text_boxes" title="<?=$excelitem; ?>" value="<?=$excelitem; ?>" readonly disabled /></td>
								<td><?=create_drop_down( "cboitem_".$k, 150, $garments_item, "",1,"--Select--", 0, "check_duplicate($k,this.id);",'','' ); ?></td>
								<td><input type="text" id="txtsetitemratio_<?=$k; ?>" name="txtsetitemratio_<?=$k; ?>" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(<?=$k; ?>);" value="" /></td>
								<td><input type="text" id="smv_<?=$k; ?>" name="smv_<?=$k; ?>" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(<?=$k; ?>);" value="0" />
									<input type="hidden" id="smvset_<?=$k; ?>" name="smvset_<?=$k; ?>" style="width:30px" class="text_boxes_numeric" value="0"  />
								</td>
								<td><input type="text" id="cutsmv_<?=$k; ?>" name="cutsmv_<?=$k; ?>" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(<?=$k; ?>);" value="0" />
									<input type="hidden" id="cutsmvset_<?=$k; ?>" name="cutsmvset_<?=$k; ?>" style="width:30px" class="text_boxes_numeric" value="0"  />
								</td>
								<td><input type="text" id="finsmv_<?=$k; ?>" name="finsmv_<?=$k; ?>" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(<?=$k; ?>);" value="0" />
									<input type="hidden" id="finsmvset_<?=$k; ?>" name="finsmvset_<?=$k; ?>" style="width:30px" class="text_boxes_numeric" value="0"  />
									<input type="hidden" id="hidquotid_<?=$k; ?>" name="hidquotid_<?=$k; ?>" style="width:30px" class="text_boxes_numeric" value="" readonly/>
								</td>
							</tr>
							<?
							$k++;
							$nullItem=1;
						}
						if($nullItem==0)
						{
							?>
                            <tr id="settr_1" align="center">
                                <td><input type="text" id="txtexcelstyle_<?=$k; ?>" name="txtexcelstyle_<?=$k; ?>" style="width:120px" class="text_boxes" value="<?=$styleName; ?>" readonly disabled /></td>
                                <td><input type="text" id="txtexcelitem_<?=$k; ?>" name="txtexcelitem_<?=$k; ?>" style="width:140px" class="text_boxes" value="<?=$excelitem; ?>" readonly disabled /></td>
                                <td><?=create_drop_down( "cboitem_".$k, 150, $garments_item, "",1,"--Select--", 0, "check_duplicate($k,this.id ); check_smv_set($k); check_smv_set_popup($k);",'','' ); ?></td>
                                <td><input type="text" id="txtsetitemratio_<?=$k; ?>" name="txtsetitemratio_<?=$k; ?>" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(<?=$k; ?>);" value="" /></td>
                                <td><input type="text" id="smv_<?=$k; ?>" name="smv_<?=$k; ?>" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(<?=$k; ?>);" value="0" />
                                    <input type="hidden" id="smvset_<?=$k; ?>" name="smvset_<?=$k; ?>" style="width:30px" class="text_boxes_numeric" value="0"  />
                                </td>
                                <td><input type="text" id="cutsmv_<?=$k; ?>" name="cutsmv_<?=$k; ?>" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(<?=$k; ?>);" value="0" />
                                    <input type="hidden" id="cutsmvset_<?=$k; ?>" name="cutsmvset_<?=$k; ?>" style="width:30px" class="text_boxes_numeric" value="0"  />
                                </td>
                                <td><input type="text" id="finsmv_<?=$k; ?>" name="finsmv_<?=$k; ?>" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(<?=$k; ?>);" value="0" />
                                    <input type="hidden" id="finsmvset_<?=$k; ?>" name="finsmvset_<?=$k; ?>" style="width:30px" class="text_boxes_numeric" value="0"  />
                                    <input type="hidden" id="hidquotid_<?=$k; ?>" name="hidquotid_<?=$k; ?>" style="width:30px" class="text_boxes_numeric" value="" readonly/>
                                </td>
                            </tr>
                            <?php
							$k++;
							$nullItem=0;
						}
					}
				}
				else
				{
					?>
                    <tr id="settr_1" align="center">
                        <td><input type="text" id="txtexcelstyle_1" name="txtexcelstyle_1" style="width:120px" class="text_boxes" value="<?=$styleName; ?>" readonly disabled /></td>
                        <td><input type="text" id="txtexcelitem_1" name="txtexcelitem_1" style="width:140px" class="text_boxes" value="<?=$excelitem; ?>" readonly disabled /></td>
                        <td><?=create_drop_down( "cboitem_1", 150, $garments_item, "",1,"--Select--", 0, "check_duplicate(1,this.id ); check_smv_set(1); check_smv_set_popup(1);",'','' ); ?></td>
                        <td><input type="text" id="txtsetitemratio_1" name="txtsetitemratio_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1);" value="" /></td>
                        <td><input type="text" id="smv_1" name="smv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1);" value="0" />
                            <input type="hidden" id="smvset_1" name="smvset_1" style="width:30px" class="text_boxes_numeric" value="0"  />
                        </td>
                        <td><input type="text" id="cutsmv_1" name="cutsmv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1);" value="0" />
                            <input type="hidden" id="cutsmvset_1" name="cutsmvset_1" style="width:30px" class="text_boxes_numeric" value="0"  />
                        </td>
                        <td><input type="text" id="finsmv_1" name="finsmv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1);" value="0" />
                            <input type="hidden" id="finsmvset_1" name="finsmvset_1" style="width:30px" class="text_boxes_numeric" value="0"  />
                            <input type="hidden" id="hidquotid_1" name="hidquotid_1" style="width:30px" class="text_boxes_numeric" value="" readonly/>
                        </td>
                    </tr>
					<?php
				}
                ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom" style="display:none">
                        <th colspan="3" align="right">Total :</th>
                        <th><input type="text" id="tot_set_qnty" name="tot_set_qnty" class="text_boxes_numeric" style="width:30px" value="0" readonly /></th>
                        <th><input type="text" id="tot_smv_qnty" name="tot_smv_qnty" class="text_boxes_numeric" style="width:30px" value="0" readonly /></th>
                        <th><input type="text" id="tot_cutsmv_qnty" name="tot_cutsmv_qnty" class="text_boxes_numeric" style="width:30px" value="0" readonly /></th>
                        <th><input type="text" id="tot_finsmv_qnty" name="tot_finsmv_qnty" class="text_boxes_numeric" style="width:30px" value="0" readonly /></th>
                    </tr>
                </tfoot>
            </table>
           </form>
           
        <table width="1200" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
			<thead>
            	<tr>
                	<th width="30" rowspan="2">SL.</th>
                    <th colspan="2">Style Details</th>
                    <th colspan="4">Po Details</th>
                    <th colspan="8">Country or Color Size Details</th>
                </tr>
                <tr>
                    <th width="100">Style Ref.</th>
                    <th width="100">Style Des.</th>
                    <th width="80">Order Status</th>
                    <th width="100">Po No.</th>
                    <th width="70">Po Receive Date</th>
                    <th width="70">Shipment Date</th>
                    
                    <th width="80">Set Item</th>
                    <th width="80">Country</th>
                    <th width="70">Country Ship Date</th>
                    <th width="100">Color Name</th>
                    <th width="60">Size Name</th>
                    <th width="80">Qty</th>
                    <th width="70">Rate</th>
                    <th>Amount</th>
                </tr>
			</thead>
		</table>
		<div style="width:1200px; max-height:320px; overflow-y:scroll" id="scroll_body" > 
		<table width="1182" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="scanning_tbl"><!--table_body-->
        	<?
			$country_arr=array();
			$countrysql=sql_select("select id, country_name from lib_country order by country_name");
			foreach($countrysql as $row)
			{
				$country_arr[strtoupper(trim($row[csf('country_name')]))]=$row[csf('country_name')];
			}
			unset($countrysql);
			/*$code_arr=array();
			$code_sql=sql_select("select ultimate_country_code from lib_country_loc_mapping where status_active=1 and is_deleted=0 order by ultimate_country_code");
			foreach($code_sql as $row)
			{
				$code_arr[trim($row[csf('ultimate_country_code')])]=$row[csf('ultimate_country_code')];
			}
			unset($code_sql);*/
			$ready_to_save=1;
			$i=1; $sty=1; $st_name="";
			foreach($style_all_data_arr as $style_name=>$order_data)
			{
				$st=1; $pn=1; $count=1;
				foreach($order_data as $order_no=>$itemcolor_data)
				{
					$p=1; $ctpn=1; 
					foreach($itemcolor_data as $setItem=>$color_size_data)
					{
						foreach($color_size_data as $color_val=>$size_data)
						{
							$s=1;//$style_all_data_arr[$style_ref][$po_number][$color_name][$size_name][$all_data]
							foreach($size_data as $size_val=>$extra_data)
							{
								foreach($extra_data as $ex_val=>$sizeqty)
								{	
								$ex_data=explode('__',$ex_val);
								
								$style_des=''; $po_receive_date=''; $po_shiment_date=''; $country_shiment_date=''; $po_avg_rate=0;  $country_qty=0; $country_amt=0;
								$style_des=$ex_data[0]; $po_receive_date=$ex_data[1]; $po_shiment_date=$ex_data[2]; $pub_shiment_date=$ex_data[2]; $country_shiment_date=$ex_data[2]; $po_avg_rate=number_format($ex_data[3],2); $country_qty=$sizeqty['poqty']; $country_amt=number_format($country_qty*$po_avg_rate,2,'.','');
								
								if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$td_color_countryCode="";
								
								//if(trim($code_arr[trim($ex_data[5])])=="") { $td_color_code="red"; $ready_to_save=0; } else if(trim($ex_data[5])==""){ $td_color_code="red"; $ready_to_save=0;}
								
								if(trim($ex_data[7])==""){ $td_color_countryCode="red"; $ready_to_save=0; } else if($country_arr[trim($ex_data[7])]==""){ $ready_to_save=0; $td_color_countryCode="red";  }
								?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_1nd<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_1nd<?=$i; ?>">
									<td width="30"><?=$i; ?></td>
									<? if($st==1) { ?>
									<td width="100" id="styleRef_<?=$sty; ?>" style="word-break:break-all"><?=$style_name; ?></td>
									<td width="100" id="styleDes_<?=$sty; ?>" style="word-break:break-all"><?=$style_des; ?></td>
									<? $st++; } else { ?>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<? } if($p==1) { $auto_id=''; $auto_id=$sty.'_'.$pn; ?>
									<td width="80"><?=create_drop_down( "cboOrderStatus_$auto_id", 75, $order_status, 0, "", $selected,"", "" ); ?></td>
									<td width="100" id="poNo_<?=$auto_id; ?>" style="word-break:break-all"><?=$order_no; ?></td>
									<td width="70" id="recDate_<?=$auto_id; ?>" style="word-break:break-all"><?=$po_receive_date; ?></td>
									<td width="70" id="shipDate_<?=$auto_id; ?>"><?=$po_shiment_date; ?></td>
									
									<? $p++; } else { ?>
									<td width="80">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="70">&nbsp;</td>
									<td width="70">&nbsp;</td>
									<? } $ciid=''; $ciid=$sty.'_'.$pn.'_'.$ctpn; ?>
                                    <td width="80" id="setItem_<?=$ciid; ?>" style="word-break:break-all"><?=trim($setItem); ?></td>
									<td width="80" id="countryCode_<?=$ciid; ?>" bgcolor="<?=$td_color_countryCode; ?>" style="word-break:break-all"><?=trim($ex_data[7]); ?>&nbsp;</td>
									
									<td width="70" id="countryShipDate_<?=$ciid; ?>"><?=change_date_format($po_shiment_date); ?></td>
									<td width="100" id="colorName_<?=$ciid; ?>" style="word-break:break-all"><?=$color_val; ?></td>
									<td width="60" id="sizeName_<?=$ciid; ?>" align="center" style="word-break:break-all"><?=$size_val; ?></td>
									<td width="80" id="countryQty_<?=$ciid; ?>" align="right"><?=$country_qty; ?></td>
									<td width="70" id="countryRate_<?=$ciid; ?>" align="right"><?=$po_avg_rate; ?></td>
									<td id="countryAmt_<?=$ciid; ?>" align="right"><?=$country_amt; ?></td>
								</tr>
								<?
								$i++; $ctpn++;
								}
							}
						}
					?><input type="hidden" class="text_boxes" id="couCount_<?=$sty.'_'.$pn; ?>" name="couCount_<?=$sty.'_'.$pn; ?>" style="width:30" value="<?=$ctpn-1; ?>"><? 
					$pn++;
					}
				}
				 ?><input type="hidden" class="text_boxes" id="poCount_<?=$sty; ?>" name="poCount_<?=$sty; ?>" style="width:30" value="<?=$pn-1; ?>"><?
				$sty++;
			}
			//echo $st_name;
			?>
            <input type="hidden" class="text_boxes" id="txt_ready_to_save" name="txt_ready_to_save" style="width:80" value="<?=$ready_to_save; ?>">
            <input type="hidden" class="text_boxes" id="styPoSzInc_id" name="styPoSzInc_id" style="width:80" value="<?=$sty; ?>">
        </table>
        </div>
        </form>
        </fieldset>
		<div>
            <table>
            	<tr style="border:none">
                    <td align="center" colspan="15" class="button_container">
                         <input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Save" onClick="fnc_excel_import(0);" />
                    </td>
                </tr>
           </table>
      	</div>
        </div>
	</body>
    <script> //if ($('#txt_job_no').val()!="") get_php_form_data( $('#txt_job_no').val(), 'populate_job_data_form','requires/excel_order_import_bs_controller');</script>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
        <?php
	}
	else
	{
		echo "Failed";	
	}
	die;
}
?>
