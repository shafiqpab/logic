<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//---------------------------------------------------- Start---------------------------------------------------------------------------
//$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number" );
$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name" );
$size_library=return_library_array( "select id,size_name from lib_size where status_active =1 and is_deleted=0", "id", "size_name" );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name" );
$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data'  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/sample_booking_non_order_controller', this.value, 'cbo_sample_type', 'sampletd' );load_drop_down( 'requires/sample_booking_non_order_controller', this.value, 'load_drop_down_brand', 'brand_td' );" );
	exit();
}
if ($action=="load_drop_down_brand")
{
	//$data_arr = explode("*", $data);
	// echo "<pre>";
	// print_r($data_arr);die;
	//echo "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC";
	echo create_drop_down( "cbo_brand_name", 130, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer_pop")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data'  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_suplier")
{
	if($data==5 || $data==3){
		echo create_drop_down( "cbo_supplier_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Supplier --", "", "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/sample_booking_non_order_controller');",0,"" ); // new condition
	}
	else{
		echo create_drop_down( "cbo_supplier_name", 130, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/sample_booking_non_order_controller');",0 );
	}
	exit();
}

if($action=="load_drop_down_attention")
{
	$data=explode("_",$data);
	$supplier=$data[0];
	$paymode=$data[1];
	$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$supplier."' and is_deleted=0 and status_active=1");
	if($paymode==1 || $paymode==2)
	{
		echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	}
	else
	{
		echo "document.getElementById('txt_attention').value = '';\n";
	}
	exit();
}

else if ($action=="cbo_sample_type")
{
	$sample_library=return_library_array( "select a.sample_name,a.id from lib_sample a, lib_buyer_tag_sample b where a.id=b.tag_sample and b.buyer_id=$data and b.sequ!=0 and a.is_deleted=0 and a.status_active=1 order by a.sample_name", "id", "sample_name"  );
	if(count($sample_library)==0){
		$sample_library=return_library_array( "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );

	}
    echo create_drop_down( "cbo_sample_type", 130, $sample_library,"", '1', "--Select--", '', "",'','' );
	exit();
}

if($action=="process_loss_method_id")
{
	$data=explode("_",$data);
	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$data[0]  and variable_list=18 and item_category_id=$data[1] and status_active=1 and is_deleted=0");
	echo $process_loss_method;

}

if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 130, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
}

if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date ,$data[2]);
	echo "1"."_".$currency_rate;
	exit();
}

if($action=="check_kniting_charge")
{
	//echo $data;
	$sql_result=sql_select("select process_costing_maintain from variable_settings_production where company_name='$data' and variable_list=34 and status_active=1 and is_deleted=0");
	//$currency_rate=check_kniting_charge( $data[0], $conversion_date );
	$maintain_setting=$sql_result[0][csf('process_costing_maintain')];
	if($maintain_setting==1) echo "1"."_"; else echo "0"."_";
	//echo $sql_result[0][csf('process_costing_maintain')];
	//echo "1"."_".$currency_rate;
	exit();
}

if($action=="print_button_variable_setting")
{
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=4 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();
}

if($action=="show_fabric_booking")
{
	extract($_REQUEST);
	$sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	$arr=array (0=>$style_library,2=>$sample_library,3=>$body_part,4=>$color_type,8=>$color_library,9=>$color_library,10=>$size_library,16=>$unit_of_measurement);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$sql= "select style_id, style_des, sample_type, body_part, color_type_id, construction, composition, gsm_weight, gmts_color, fabric_color, gmts_size, item_size, item_qty, dia_width, finish_fabric, process_loss, grey_fabric, rate, amount, uom, id FROM wo_non_ord_samp_booking_dtls  WHERE booking_no ='".$data."'  and status_active=1 and	is_deleted=0 order by id";
	//echo $sql;

	echo  create_list_view("list_view", "Style,Style Des,Sample,Body Part,Color Type,Construction,Composition,GSM,Gmts Color,Fab.Color,Gmts Size,Item Size,Item Qty, Dia/ Width,Fin Fab Qty,Process Loss,Uom,Gray Qty,Rate,Amount", "50,100,100,120,80,100,120,50,80,80,70,70,50,50,60,60,60,60,60,60","1600","220",0, $sql , "get_php_form_data", "id", "'populate_details_data_from_for_update'", 1, "style_id,0,sample_type,body_part,color_type_id,0,0,0,gmts_color,fabric_color,gmts_size,0,0,0,0,0,uom,0,0,0", $arr , "style_id,style_des,sample_type,body_part,color_type_id,construction,composition,gsm_weight,gmts_color,fabric_color,gmts_size,item_size,item_qty,dia_width,finish_fabric,process_loss,uom,grey_fabric,rate,amount", "requires/sample_booking_non_order_controller",'','0,0,0,0,0,0,0,0,0,0,0,0,0,0,2,2,0,2,2,2') ;
	exit();
}

if($action=="color_from_library")
{
  $color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$data  and variable_list=23  and status_active=1 and is_deleted=0");
  echo trim($color_from_library);
  die;
}

if($action=="booking_no_check")
{
  $booking_no_check=sql_select("select a.issue_number from inv_issue_master a,inv_transaction b where  a.id=b.mst_id and a.booking_no='$data' and a.issue_basis=1 and a.issue_purpose=8 and a.entry_form=3 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.issue_number");
 // echo "10**select a.issue_number from inv_issue_master a,inv_transaction b where  a.id=b.mst_id and a.booking_no='$data' and a.issue_basis=1 and a.issue_purpose=8 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 group by a.issue_number";die;

  //echo " select booking_no from inv_issue_master where  booking_no='$data' and issue_basis=1 and issue_purpose=8 and entry_form=3 and status_active=1 and is_deleted=0";
  $issue_number="";
  foreach($booking_no_check as $row_iss)
  {
	  if( $issue_number=="")  $issue_number=$row_iss[csf('issue_number')]; else $issue_number.="_".$row_iss[csf('issue_number')];

  }
  echo rtrim($issue_number);
  exit();
}

if($action=="color_popup")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);
?>
<script>
function js_set_value(data)
{
	document.getElementById('color_name').value=data;
    parent.emailwindow.hide();
}
</script>
</head>
<body>

<div align="center">
<form>
<input type="hidden" id="color_name" name="color_name" />
<?
    if($buyer_name=="" || $buyer_name==0 )
	{
	$sql="select color_name,id FROM lib_color  WHERE status_active=1 and is_deleted=0";
	}
	else
	{
	$sql="select a.color_name,a.id FROM lib_color a, lib_color_tag_buyer b  WHERE a.id=b.color_id and b.buyer_id=$buyer_name and  status_active=1 and is_deleted=0";
	}
	echo  create_list_view("list_view", "Color Name", "160","210","420",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name", "requires/sample_booking_non_order_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0,0,2,2,2,2,2') ;
	?>
    </form>
    </div>
    </body>
    </html>
    <?
	exit();
}

if($action=="show_fabric_booking_report")
{
	extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');

	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$com_supplier_id=return_field_value( "supplier_id as supplier_id", "wo_non_ord_samp_booking_mst","booking_no=$txt_booking_no","supplier_id");
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no, a.is_approved from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=9 group by a.is_approved");
	list($nameArray_approved_row)=$nameArray_approved;
//==================================================================
	?>
	<div style="width:1330px" align="center">
    										<!--    Header Company Information         -->
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100">
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php echo $company_library[$cbo_company_name];?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
							//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name";
                            foreach ($nameArray as $result)
                            {
                            ?>
                                            Plot No: <? echo $result[csf('plot_no')]; ?>
                                            Level No: <? echo $result[csf('level_no')]?>
                                            Road No: <? echo $result[csf('road_no')]; ?>
                                            Block No: <? echo $result[csf('block_no')];?>
                                            City No: <? echo $result[csf('city')];?>
                                            Zip Code: <? echo $result[csf('zip_code')]; ?>
                                            Province No: <?php echo $result[csf('province')]; ?>
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                            Email Address: <? echo $result[csf('email')];?>
                                            Website No: <? echo $result[csf('website')];

                            }
							 $sup_addres=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$com_supplier_id");  	foreach ($sup_addres as $row)
                            {
							 				if($row[csf('plot_no')]!='') $plot_no=$row[csf('plot_no')].',';
											if($row[csf('level_no')]!='') $level_no=$row[csf('level_no')].',';
											if($row[csf('road_no')]!='') $road_no=$row[csf('road_no')].',';
											if($row[csf('block_no')]!='') $road_no=$row[csf('block_no')].',';
											if($row[csf('block_no')]!='') $road_no=$row[csf('block_no')].',';
											if($row[csf('country_id')]!=0) $country_name=$country_arr[$row[csf('country_id')]].',';
											if($row[csf('block_no')]!='') $block_no=$row[csf('block_no')].',';
											if($row[csf('province')]!='') $province=$row[csf('province')].',';
											if($row[csf('city')]!='') $city=$row[csf('city')].',';
											if($row[csf('zip_code')]!='') $zip_code=$row[csf('zip_code')].',';
											if($row[csf('email')]!='') $email=$row[csf('email')].',';
											if($row[csf('website')]!='') $website=$row[csf('website')];

											$company_address=$plot_no.$level_no.$road_no.$country_name.$block_no.$province.$city.$zip_code.$email.$website;
							}
                                            ?>

                               </td>
                            </tr>
                            <tr>

                            <td align="center" style="font-size:20px">
                                <strong><? if($report_title !=""){echo $report_title;} else {echo "Sample Fabric Booking -Without order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if($nameArray_approved_row[csf('is_approved')]==1){ echo "(Approved)";} else if($nameArray_approved_row[csf('is_approved')]==3){ echo "(Partial Approved)";}else{echo "";}; ?> </font></strong><!--//ISD-22-05730 by kausar-->
                             </td>

                             <td style="font-size:20px">
                              <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <strong> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
                                  <br/>

								  <?
								 }
							  	?>
                             </td>

                            </tr>
                      </table>
                </td>
                <td width="250" id="barcode_img_id">

                </td>
            </tr>
       </table>

                <?
				//$season_con="";
				$buyer_req_no="";
				$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
				 $sqls= "select a.season,a.season_buyer_wise, b.buyer_req_no  from  sample_development_mst a, sample_development_dtls b, wo_non_ord_samp_booking_dtls c  where  a.id=b. sample_mst_id and a.id=c.style_id and c.booking_no=$txt_booking_no and (c.entry_form_id=140 or c.entry_form_id=90 or c.entry_form_id is null or c.entry_form_id=0)";

				$nameseason=sql_select($sqls);
 				foreach ($nameseason as $season_row)
				{
					$season=$season_row[csf('season')];
					$season_buyer=$season_row[csf('season_buyer_wise')];
					//echo $season.'='.$season_buyer;
					if($season_buyer!=0 && $season=='' )
					{
						$season_con=$season_arr[$season_buyer];
					}
					if($season_buyer!=0 && $season!='' )
					{
						$season_con=$season_arr[$season_buyer];
					}
					else if($season!='' && $season_buyer==0)
					{
						$season_con=$season;
					}
					$buyer_req_no=$season_row[csf('buyer_req_no')];

				}

				$fabric_source='';
                $nameArray=sql_select( "select id,buyer_id,pay_mode,booking_no,booking_date,supplier_id,currency_id,exchange_rate,attention,delivery_date,fabric_source,team_leader,entry_form_id,	dealing_marchant from wo_non_ord_samp_booking_mst   where  booking_no=$txt_booking_no and (entry_form_id=140 or entry_form_id=90 or entry_form_id=0 or entry_form_id is null)");

				foreach ($nameArray as $result)
				{
					$fabric_source=$result[csf('fabric_source')];

					$varcode_booking_no=$result[csf('booking_no')];

				?>
       <table width="100%" style="border:1px solid black">
            <tr>
                <td colspan="6" valign="top"></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:12px"><b>Fab. Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>

            </tr>
            <tr>

                <td width="100"><span style="font-size:12px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3)
				{
					echo $company_library[$result[csf('supplier_id')]];
				}
				else
				{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
				}
				?>    </td>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;
				<?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3)
				{
					echo $company_address;
				}
				else
				{
					echo $supplier_address_arr[$result[csf('supplier_id')]];
				}
				?>

                </td>
            </tr>


            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>

                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>

            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $season_con; ?></td>
                <td  width="100" style="font-size:12px"><b>Buyer Req. No</b></td>
                <td  width="110" >:&nbsp;<? echo $buyer_req_no; ?></td>
                <td  width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td  width="110" >&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>

            </tr>
        </table>
        <?
			}
		?>

      <br/>
      <?
	  $composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 order by b.id";
	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			$yarn_count_arr[$row[csf('id')]].=$lib_yarn_count[$row[csf('count_id')]].",";
		}
	}

    $sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	if($db_type==0)
	{
	$sql= sql_select("SELECT a.fabric_source, b.style_id,b.style_des,b.sample_type,b.body_part,b.color_type_id,b.construction,b.composition,b.gsm_weight,b.gmts_color ,b.fabric_color,b.gmts_size,b.item_size,b.dia_width,b.finish_fabric,b.process_loss,b.grey_fabric,b.rate,b.amount,b.id,b.lib_yarn_count_deter_id as lib_yarn_count_deter_id,b.yarn_details,b.remarks ,b.req_dzn,b.req_qty,b.uom,b.fabric_description, b.item_qty FROM wo_non_ord_samp_booking_mst a join  wo_non_ord_samp_booking_dtls b on a.booking_no = b.booking_no WHERE b.booking_no =$txt_booking_no  and b.status_active=1 and b.is_deleted=0 and (b.entry_form_id=140 or b.entry_form_id=0 or b.entry_form_id is null) order by b.id");
	}
	if($db_type==2)
	{
	$sql= sql_select("SELECT a.fabric_source, b.style_id,b.style_des,b.sample_type,b.body_part,b.color_type_id,b.construction,b.composition,b.gsm_weight,b.gmts_color ,b.fabric_color,b.gmts_size,b.item_size,b.dia_width,b.finish_fabric,b.process_loss,b.grey_fabric,b.rate,b.amount,b.id,b.lib_yarn_count_deter_id as lib_yarn_count_deter_id,b.yarn_details,b.remarks ,b.req_dzn,b.req_qty,b.uom,b.fabric_description, b.item_qty FROM wo_non_ord_samp_booking_mst a join  wo_non_ord_samp_booking_dtls b on a.booking_no = b.booking_no WHERE b.booking_no =$txt_booking_no  and b.status_active=1 and b.is_deleted=0 and (b.entry_form_id=140 or b.entry_form_id=0 or b.entry_form_id is null) order by b.id");
	}
	$entry_form =$nameArray[0][csf("entry_form_id")];

	$yarn_dtls = sql_select("SELECT b.wo_non_ord_samp_book_dtls_id, b.count_id,b.copm_one_id,b.percent_one,b.type_id,b.cons_ratio from wo_non_ord_samp_booking_mst a join wo_non_ord_samp_yarn_dtls b on a.booking_no=b.booking_no where b.booking_no =$txt_booking_no and a.fabric_source = 1 and b.status_active=1 and b.is_deleted = 0");
	$yarn_composition_arr = array();
	if (count($yarn_dtls)>0)
	{
		foreach( $yarn_dtls as $row )
		{
			$dtls_id = $row[csf('wo_non_ord_samp_book_dtls_id')];
			if(array_key_exists($row[csf('wo_non_ord_samp_book_dtls_id')],$yarn_composition_arr))
			{
				$yarn_composition_arr[$dtls_id]=$yarn_composition_arr[$dtls_id]." ".$row[csf('cons_ratio')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$yarn_composition_arr[$dtls_id]=$row[csf('cons_ratio')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
		}
	}
?>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
			<thead>
				<tr>
					<th width="50">Sl</th>
					<th width="50">Fabric Code</th>
					<th width="60">Style</th>
					<th width="60">Style Des</th>
					<th width="100">Sample</th>
					<th width="130">Body Part</th>
					<th width="100">Color Type</th>
					<th width="80">Construction</th>
					<th width="100">Composition & Yarn</th>
					<th width="100">Custom Yarn</th>
					<?
					if($entry_form==140)
					{
						echo "<th width='120'>Fabric Desc.</th>";
					}
					?>
					<th width="50">GSM</th>
					<th width="80">Gmts Color</th>
					<th width="80">Fab.Color</th>
					<th width="50">Gmts Size</th>
					<th width="50">Item Size</th>
					<th width="50">Item Qty</th>
					<th width="50">Dia/ Width</th>
					<th width="60"><? if($entry_form==140){echo "Req Dzn";} else{echo "Fin Fab Qnty ";} ?></th>
					<th width="60">Process Loss</th>
					<th width="60"> <? if($entry_form==140){echo "Req. Qty.";} else{echo "Gray Qnty";} ?></th>
					<th width="50">UOM</th>
					<th width="70">Remarks</th>

					<?
					if($fabric_source !=1)
					{
					?>
					<th width="60">Rate</th>
					<th width="60">Amount</th>
					<?
					}
					?>
				</tr>
			</thead>
		<?
		$style_id="";$sample_type_id="";
		$total_finish_fabric=0;
		$total_grey_fabric=0;
		$toatl_rate=0;
		$total_amount=0;

		$i=1;
		foreach ($sql as $row)
		{
			$style_id.=$row[csf('style_id')].",";
			$sample_type_id.=$row[csf('sample_type')].",";
			if($row[csf('style_id')])$style_sting.=$style_library[$row[csf('style_id')]].'_';
			$yarn_count=rtrim($yarn_count_arr[$row[csf('lib_yarn_count_deter_id')]],",");
			$yarn_counts=implode(",",array_unique(explode(",",$yarn_count)));
		?>
			<tr>
				<td width="50"><? echo $i; ?></td>
				<td width="50"><? echo $row[csf('lib_yarn_count_deter_id')]; ?></td>
				<td width="60"><? echo $style_library[$row[csf('style_id')]]; ?></td>
				<td width="60"><? echo $row[csf('style_des')]; ?></td>
				<td width="100"><? echo $sample_library[$row[csf('sample_type')]]; ?></td>
				<td width="130"><? echo $body_part[$row[csf('body_part')]]; ?></td>
				<td width="100"><? echo $color_type[$row[csf('color_type_id')]]; ?></td>
				<td width="80"><? echo $row[csf('construction')]; ?></td>
				<td width="100"><?

				if($row[csf('fabric_source')] == 1){
					//$yarn_dtls=$yarn_composition_arr[$row[csf('id')]];
				}
				else{
				//	$yarn_dtls=$composition_arr[$row[csf('lib_yarn_count_deter_id')]];
				}
				//if($yarn_dtls) $yarn_dtlss=$yarn_dtls;else $yarn_dtlss="";
				// echo  $row[csf('composition')].','.$yarn_counts;
				echo  $row[csf('composition')];

				?></td>
				<td width="100"><? echo $row[csf('yarn_details')]; ?></td>
				<?
				if($entry_form==140)
				{
					?>
					<td width="100"><? echo $row[csf('fabric_description')]; ?></td>

					<?
				}
				?>
				<td width="50"><? echo $row[csf('gsm_weight')]; ?></td>
				<td width="80"><? echo $color_library[$row[csf('gmts_color')]]; ?></td>
				<td width="80"><? echo $color_library[$row[csf('fabric_color')]]; ?></td>
				<td width="50"><? echo $size_library[$row[csf('gmts_size')]]; ?></td>
				<td width="50"><? echo $row[csf('item_size')]; ?></td>
				<td width="50"><? echo $row[csf('item_qty')]; ?></td>
				<td width="50"><? echo $row[csf('dia_width')]; ?></td>
				<td width="60" align="right"><? if($entry_form!=140){echo number_format($row[csf('finish_fabric')],4); $total_finish_fabric+=$row[csf('finish_fabric')];} else {echo number_format($row[csf('req_dzn')],4); $total_finish_fabric+=$row[csf('req_dzn')];} ?></td>
				<td width="60" align="right"><? echo number_format($row[csf('process_loss')],2); ?></td>
				<td width="60" align="right"><?  if($entry_form!=140){ echo number_format($row[csf('grey_fabric')],4);  $total_grey_fabric+=$row[csf('grey_fabric')];} else {echo number_format($row[csf('req_qty')],4);  $total_grey_fabric+=$row[csf('req_qty')];} ?></td>
				<td width="50" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
				<td width="70"><? echo $row[csf('remarks')];?></td>
				<?
				if($fabric_source !=1)
				{
				?>
				<td width="60" align="right"><? echo number_format($row[csf('rate')],4); $toatl_rate+=$row[csf('rate')]; ?></td>
				<td width="60" align="right"><? echo number_format($row[csf('amount')],4); $total_amount+=$row[csf('amount')];?></td>
				<?
				}
				?>
				</tr>
				<?
				$i++;
				}
				?>
				<tr>
				<th width="50" colspan="17" align="right">Total </th>

				<th width="60" align="right"><? echo number_format($total_finish_fabric,4); ?></th>
				<th width="60" align="right"></th>
				<th width="60" align="right"><? echo number_format($total_grey_fabric,4);  ?></th>
				<th width="50" align="right"><? //echo number_format($total_grey_fabric,4);  ?></th>
				<th width="50" align="right"><? //echo number_format($total_grey_fabric,4);  ?></th>
				<?
				if($fabric_source !=1)
				{
				?>
				<th width="60" align="right"><? echo number_format($toatl_rate,4);?></th>
				<th width="60" align="right"><? echo number_format($total_amount,4); ?></th>
				<?
				}
				?>
			</tr>
		</table>
        <br/>
        <?
		$sql_img=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='sample_booking_non' and master_tble_id=$txt_booking_no and file_type=1");
		?>
        <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
         <caption align="center"><b>View Photo</b></caption>
        <tr>
        <td colspan="8" height="100">
       <?
        foreach($sql_img as $img)
		{
		?>
        	<img  src='../../<? echo $img[csf('image_location')]; ?>' height='90px' width='90px' /> &nbsp;&nbsp;
        <?
		}
		?>
        </td>
        </tr>
        </table>
        <br/>



		<?
		$lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
	$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	 $mst_id=return_field_value("id as mst_id","wo_non_ord_samp_booking_mst","booking_no=$txt_booking_no","mst_id");
	 $approve_data_array=sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=9  group by  b.approved_by order by b.approved_by asc");
	 $unapprove_data_array=sql_select("select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form=9  order by b.approved_date,b.approved_by");



	?>
    <br/> <br/>
  		<?
          if(count($approve_data_array)>0)
			{
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="5" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="40%" style="border:1px solid black;">Name</th>
				<th width="30%" style="border:1px solid black;">Designation</th>
				<th width="27%" style="border:1px solid black;">Approval Date</th>

                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($approve_data_array as $row){
			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="40%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="27%" style="border:1px solid black;text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>

                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
		<?
		}
		?>
		<br>
		<?
		if(count($unapprove_data_array)>0)
		{
			//and approval_type=0
			$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=9  and is_deleted=0 and status_active=1 and booking_id=$mst_id");
		//	echo "select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=8  and is_deleted=0 and status_active=1 and booking_id=$mst_id";


			$unapproved_request_arr=array();
			foreach($sql_unapproved as $rowu)
			{
				if($rowu[csf('approval_cause')]!='')
				{
					$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
				}
			}
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="6" style="border:1px solid black;">Approval/Un Approval History</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="30%" style="border:1px solid black;">Name</th>
				<th width="20%" style="border:1px solid black;">Designation</th>
				<th width="5%" style="border:1px solid black;">Approval Status</th>
				<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
				<th width="22%" style="border:1px solid black;"> Date</th>

                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($unapprove_data_array as $row){

			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes';?></td>
				<td width="20%" style="border:1px solid black;"><? echo '';?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
             </tr>
				<?
				$i++;
				$un_approved_date= explode(" ",$row[csf('un_approved_date')]);
				$un_approved_date=$un_approved_date[0];
				if($db_type==0) //Mysql
				{
					if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
				}
				else
				{
					if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
				}

				if($un_approved_date!="")
				{
				?>
			<tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No';?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id];?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
              </tr>

                <?
				$i++;
				}

			}
				?>
            </tbody>
        </table>
		<?
		}
		?>
        <br/>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <table align="left" cellspacing="0" width="450"  border="1" rules="all" class="rpt_table" >

            <?
           $sql_req="SELECT gmts_color as gmts_color,gmts_size,sum(bh_qty) as bh_qty,sum(rf_qty) as rf_qty FROM wo_non_ord_samp_booking_dtls WHERE booking_no=$txt_booking_no and status_active=1 and is_deleted=0 and gmts_size!=0 and bh_qty>0 group by gmts_color,gmts_size order by gmts_size ";
        $sql_data =sql_select($sql_req);
        $size_array=array();$qnty_array_bh=array();$qnty_array_rf=array();
        foreach($sql_data as $row)
        {
          $size_array[$row[csf('gmts_size')]]=$row[csf('gmts_size')];
          $qnty_array_bh[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('bh_qty')];
          $qnty_array_rf[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('rf_qty')];
          $qnty_array[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('bh_qty')]+$row[csf('rf_qty')];
        }
         $sql_color="SELECT gmts_color as gmts_color,sum(bh_qty) as bh_qty FROM wo_non_ord_samp_booking_dtls  WHERE booking_no=$txt_booking_no and status_active=1 and is_deleted=0 and gmts_size!=0 and bh_qty>0 group by gmts_color  order by gmts_color";
        $sql_data_color =sql_select($sql_color);
        $color_array=array();
        foreach($sql_data_color as $row)
        {
          $color_array[$row[csf('gmts_color')]]=$row[csf('gmts_color')];
        }
         $sizearr=return_library_array("select id,size_name from lib_size where status_active =1 and is_deleted=0","id","size_name");
         $colorarr=return_library_array("select id,color_name from  lib_color where status_active =1 and is_deleted=0","id","color_name");
         $width=400+(count($size_array)*150);
         //count($size_array);
         ?>


            <thead align="center">
             <tr>
                   <th align="left" colspan="<? echo count($size_array)+5;?>" width="30"><strong>Sample Requirement</strong></th>
               </tr>
                <tr>
                <th width="30" rowspan="2">SL</th>
                <th width="80" rowspan="2" align="center">Color/Size</th>
                <?
                foreach ($size_array as $sizid)
                {
                ?>
                <th width="" colspan="2"><strong><? echo  $sizearr[$sizid];  ?></strong>
                </th>

                <?
                } ?>
               <th width="80" rowspan="2" align="center">Total Qnty.</th>
                </tr>
                <tr>
                 <?
                foreach ($size_array as $sizid)
                {
                ?>
                <th width="75"> BH &nbsp;</th> <th width="75"> Rf.&nbsp;</th>
                <?
                } ?>
                </tr>
            </thead>
            <tbody>
          <?
                //$mrr_no=$dataArray[0][csf('issue_number')];
                $i=1;
                $tot_qnty=array();
                    foreach($color_array as $cid)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
              $color_count=count($cid);
                        ?>
                        <tr>
                            <td><? echo $i;  ?></td>
                            <td><? echo $colorarr[$cid]; ?></td>

                             <?
                foreach ($size_array as $sizval)
                {
                //$size_count=count($sizid);
                $tot_qnty[$cid]+=$qnty_array[$cid][$sizval];
                $tot_qnty_size_bh[$sizval]+=$qnty_array_bh[$cid][$sizval];
                $tot_qnty_size_rf[$sizval]+=$qnty_array_rf[$cid][$sizval];
                ?>
                <td width="75" align="right"> <? echo $qnty_array_bh[$cid][$sizval]; ?></td> <td width="75" align="right"> <? echo $qnty_array_rf[$cid][$sizval]; ?></td>
                <?

                } ?>

                            <td align="right"><? echo $tot_qnty[$cid]; ?></td>
                        </tr>
                        <?
              $production_quantity+=$tot_qnty[$cid];
              $i++;
                    }
                ?>
            </tbody>
            <tr>
                <td colspan="2" align="right"><strong>Grand Total :</strong></td>
                <?
            foreach ($size_array as $sizval)
            {
              ?>
                        <td align="right"><?php echo $tot_qnty_size_bh[$sizval]; ?></td>
                        <td align="right"><?php echo $tot_qnty_size_rf[$sizval]; ?></td>
                        <?
            }
          ?>
                <td align="right"><?php echo $production_quantity; ?></td>
            </tr>
        </table>
          <br>
        </table>
        <br>
        <br>

        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="padding-top: 10px">
            <tr>
                <td width="100%">
                    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    <tr>
                        	<th width="30" colspan="8" align="left">Sample Info</th>

                        </tr>
                    	<tr>
                        	<th width="30">Sl </th>
                            <th width="100">Style</th>
                            <th width="100">Sample Name</th>
                            <th width="80">Color</th>
                            <th width="80">Size</th>
                            <th width="80">Qty( Pcs)</th>
                            <th width="80">Rate (Pcs)</th>
                            <th width="80">Currency</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					if($style_id!='') $style_cond=" and a.id in(".rtrim($style_id,",").")";else $style_cond="";
					if($sample_type_id!='') $sample_cond=" and b.sample_name in(".rtrim($sample_type_id,",").")";else $sample_cond="";
					//echo $style_id."  ".$sample_type_id;die;

					$lib_sample_name=return_library_array("select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name");
					$sql= "select a.style_ref_no,b.sample_name,b.sample_color,b.sample_charge,b.sample_curency,c.size_id,c.size_qty,d.booking_no from sample_development_mst a, sample_development_dtls b,sample_development_size c,wo_non_ord_samp_booking_dtls d where a.id=b.sample_mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.id=d.style_id and b.sample_mst_id=d.style_id  and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and d.status_active=1  $style_cond $sample_cond and b.sample_color=d.gmts_color and d.dtls_id=b.id order by a.id";

                    $totqty=0;
					$totcharge=0;
					$data_array=sql_select($sql);// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							$totqty+=$row[csf('size_qty')];
							$totcharge+=$row[csf('sample_charge')];
							?>
                            	<tr id="settr_1" align="">
                                    <td width="30"><? echo $i ?></td>
                                    <td width="100" title="<? echo $row[csf('booking_no')]; ?>"><? echo $row[csf('style_ref_no')] ?></td>
                                    <td width="100"><? echo $lib_sample_name[$row[csf('sample_name')]] ?></td>
                                    <td width="80"><? echo $color_library[$row[csf('sample_color')]] ?></td>
                                    <td width="80"><? echo $size_library[$row[csf('size_id')]] ?></td>
                                    <td width="80" align="right"><? echo $row[csf('size_qty')] ?></td>
                                    <td width="80" align="right"><? echo $row[csf('sample_charge')] ?></td>
                                    <td width="80"><? echo $currency[$row[csf('sample_curency')]] ?></td>
                                </tr>
                            <?
						}
					}
					?>
                    <tr id="settr_1" align="">
                                    <td width="30" colspan="5">Total:</td>

                                    <td width="80" align="right"><? echo $totqty; ?></td>
                                    <td width="80" align="right"><? echo $totcharge; ?></td>
                                    <td width="80"></td>
                                </tr>
                </tbody>
                </table>
                </td>

            </tr>
        </table>
        <br/>

        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="100%">
                    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th width="3%">Sl</th><th width="97%" align="left">Spacial Instruction</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=90 and booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>
                                </tr>
                            <?
						}
					}
					/*else
					{
						$i=0;
					$data_array=sql_select("select id, terms from  lib_terms_condition");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>

                                </tr>
                    <?
						}
					} */
					?>
                </tbody>
                </table>
                </td>

            </tr>
        </table>
          <?
		 	echo signature_table(6, $cbo_company_name, "1330px");
			echo "****".custom_file_name($varcode_booking_no,$style_sting,'');
		  ?>
       </div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
        </script>
       <?
	   exit();
}

if($action=="show_fabric_booking_report2")
{
	//echo load_html_head_contents("Sample Booking","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$item_library=return_library_array( "select id,item_name from lib_item_group", "id", "item_name"  );
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=9");
	list($nameArray_approved_row)=$nameArray_approved;
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:1330px" align="center">
    <table width="100%" cellpadding="0" cellspacing="0" style="border:2px solid black">
        <tr>
        <td width="100">
        <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
        </td>
        <td width="1000">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
            <td align="center" style="font-size:20px;">
            <?php
				echo $company_library[$cbo_company_name];
				?>
            </td>
            </tr>
            <tr>
            <td align="center" style="font-size:14px">
            <?
            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
            foreach ($nameArray as $result)
            {
            ?>
                Plot No: <? echo $result[csf('plot_no')]; ?>
                Level No: <? echo $result[csf('level_no')]?>
                Road No: <? echo $result[csf('road_no')]; ?>
                Block No: <? echo $result[csf('block_no')];?>
                City No: <? echo $result[csf('city')];?>
                Zip Code: <? echo $result[csf('zip_code')]; ?>
                Province No: <?php echo $result[csf('province')]; ?>
                Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                Email Address: <? echo $result[csf('email')];?>
                Website No: <? echo $result[csf('website')];
            }
                ?>

            </td>
            </tr>
            <tr>

            <td align="center" style="font-size:20px">
            <strong><? if($report_title !=""){echo $report_title;} else {echo "Sample Fabric Booking -Without order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
            </td>

            <td>
				<?
                if($nameArray_approved_row[csf('approved_no')]>1)
                {
                ?>
                <strong> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
                <br/>

                <?
                }
                ?>
            </td>

            </tr>
    </table>
    </td>
    <td width="250" id="barcode_img_id">

    </td>
    </tr>
    </table>
    <br>

                <?
				$season="";
				$buyer_req_no="";

				$nameseason=sql_select( "select a.season, b.buyer_req_no  from  sample_development_mst a, sample_development_dtls b, wo_non_ord_samp_booking_dtls c  where  a.id=b. sample_mst_id and a.id=c.style_id and c.booking_no=$txt_booking_no");
				foreach ($nameseason as $season_row)
				{
					$season=$season_row[csf('season')];
					$buyer_req_no=$season_row[csf('buyer_req_no')];

				}

				$fabric_source='';
                $nameArray=sql_select( "select buyer_id,booking_no,booking_date,pay_mode,supplier_id,currency_id,exchange_rate,attention,delivery_date,fabric_source,team_leader,	dealing_marchant from wo_non_ord_samp_booking_mst   where  booking_no=$txt_booking_no");
				foreach ($nameArray as $result)
				{
					$fabric_source=$result[csf('fabric_source')];

					$varcode_booking_no=$result[csf('booking_no')];

				?>
       <table width="100%" style="border:2px solid black; margin-top:-20px">
            <tr>
                <td colspan="6" valign="top"></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:12px"><b>Fab. Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>

            </tr>
            <tr>

                <td width="100"><span style="font-size:12px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3)
				{
					echo $company_library[$result[csf('supplier_id')]];
				}
				else
				{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
				}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;<?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
				$comAdd=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$result[csf('supplier_id')]);
					foreach ($comAdd as $comAddRow){
						echo $comAddRow[csf('plot_no')].'&nbsp;';
						echo $comAddRow[csf('level_no')].'&nbsp;' ;
						echo $comAddRow[csf('road_no')].'&nbsp;';
						echo $comAddRow[csf('block_no')].'&nbsp;';
						echo $comAddRow[csf('city')].'&nbsp;';
						//echo $comAddRow[csf('zip_code')].'&nbsp;';
						//echo $comAddRow[csf('province')].'&nbsp;';
						//echo $country_arr[$comAddRow[csf('country_id')]].'&nbsp;';
						//echo $comAddRow[csf('email')];
						//echo $comAddRow[csf('website')];
					}
				}
				else{
					echo $supplier_address_arr[$result[csf('supplier_id')]];
				}
				//echo $supplier_address_arr[$result[csf('supplier_id')]];

				?></td>
            </tr>


            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>

                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>

            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $season; ?></td>
                <td  width="100" style="font-size:12px"><b>Buyer Req. No</b></td>
                <td  width="110" >:&nbsp;<? echo $buyer_req_no; ?></td>
                <td  width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td  width="110" >&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>

            </tr>
        </table>
        <?
			}
		?>

      <br/>
      <?
			$composition_arr=array();
			$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
			$lib_fabric_composition=return_library_array( "select a.id,b.fabric_composition_name  from lib_yarn_count_determina_mst a ,lib_fabric_composition b where a.fabric_composition_id=b.id", "id", "fabric_composition_name");


	$yarnDtlsArr=array();

	$yarn_dtls = sql_select("SELECT id, booking_no, wo_non_ord_samp_book_dtls_id, count_id, copm_one_id, cons_ratio, type_id, cons_qnty from wo_non_ord_samp_yarn_dtls where is_deleted=0 and status_active=1 and booking_no =$txt_booking_no");
	//echo "SELECT id, booking_no, wo_non_ord_samp_book_dtls_id, count_id, copm_one_id, cons_ratio, type_id, cons_qnty from wo_non_ord_samp_yarn_dtls where is_deleted=0 and status_active=1 and booking_no =$txt_booking_no";
	foreach($yarn_dtls as $yrow)
	{
		$yarnDtlsArr[$yrow[csf('wo_non_ord_samp_book_dtls_id')]]['ycount'].=$lib_yarn_count[$yrow[csf('count_id')]].',';
		$yarnDtlsArr[$yrow[csf('wo_non_ord_samp_book_dtls_id')]]['composition'].=$composition[$yrow[csf('copm_one_id')]].',';
		$yarnDtlsArr[$yrow[csf('wo_non_ord_samp_book_dtls_id')]]['type'].=$yarn_type[$yrow[csf('type_id')]].',';
	}
	unset($yarn_dtls);

    $sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	$sql= sql_select("select id, style_id, style_des, sample_type, body_part, color_type_id, construction, composition, gsm_weight, gmts_color, fabric_color, gmts_size, item_size, dia_width, finish_fabric, process_loss, grey_fabric, rate, amount, lib_yarn_count_deter_id as lib_yarn_count_deter_id, yarn_details, remarks  FROM wo_non_ord_samp_booking_dtls  WHERE booking_no =$txt_booking_no and status_active=1 and is_deleted=0 order by id");
?>
        <table class="rpt_table" width="1430"  border="2" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th width="20">Sl</th>
                    <th width="100">Style Des</th>
                    <th width="100">Sample</th>
                    <th width="120">Body Part</th>
                    <th width="90">Color Type</th>
                    <th width="100">Construction</th>
					<th width="100">Fabric Composition</th>
                    <th width="50">Y/Count</th>
					<th width="100">Y/Comp</th>
                    <th width="70">Y/Type</th>
                    <th width="50">GSM</th>
                    <th width="80">Fab.Color</th>

                    <th width="50">Dia/ Width</th>
                    <th width="70">Fin Fab Qnty</th>
                    <th width="70">Process Loss</th>
                    <th width="70">Gray Qnty</th>
                    <th width="70">Remarks</th>
                    <th width="50">Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
			<?
            $style_id="";
            $total_finish_fabric=0;
            $total_grey_fabric=0;$total_process_loss=0;
            $toatl_rate=0;
            $total_amount=0;

            $i=1;
            foreach ($sql as $row)
            {
                $style_id.=$row[csf('style_id')].",";
                //$yarn_counts=$lib_yarn_count[$lib_yarn_count_determina_arr[$row[csf('lib_yarn_count_deter_id')]]];
                //$yarn_count_type=$yarn_type[$lib_yarn_type_count_determina_arr[$row[csf('lib_yarn_count_deter_id')]]];

               // $yarn_data= explode(",",$composition_data_arr[$row[csf('lib_yarn_count_deter_id')]]);
               // $yarn_counts=$yarn_data[0];
			  $fabric_com= $lib_fabric_composition[$row[csf('lib_yarn_count_deter_id')]];
			//    $fabric_com= $row[csf('composition')];
                $yarn_count_type=rtrim($yarnDtlsArr[$row[csf('id')]]['type'],',');
				 $ycount=rtrim($yarnDtlsArr[$row[csf('id')]]['ycount'],',');
                if($row[csf('style_des')]) $style_sting.=$row[csf('style_des')].'_';
				//if($yarn_counts=="")
				$yarn_counts=implode(",",array_filter(array_unique(explode(",",$ycount))));
				$yarn_count_type=implode(",",array_filter(array_unique(explode(",",$yarn_count_type))));



            ?>
            <tr>
                <td><? echo $i; ?></td>
                <td><? echo $row[csf('style_des')]; ?></td>
                <td><? echo $sample_library[$row[csf('sample_type')]]; ?></td>
                <td><? echo $body_part[$row[csf('body_part')]]; ?></td>
                <td><? echo $color_type[$row[csf('color_type_id')]]; ?></td>
                <td><? echo $row[csf('construction')]; ?></td>
				<td><? echo $fabric_com; ?></td>

                <td><? echo $yarn_counts; ?></td>
				<td><? echo  $row[csf('composition')];//$composition_arr[$row[csf('lib_yarn_count_deter_id')]]; ?></td>
                <td><? echo $yarn_count_type; ?></td>
                <td><? echo $row[csf('gsm_weight')];//$color_library[$row[csf('gmts_color')]]; ?></td>
                <td><? echo $color_library[$row[csf('fabric_color')]]; ?></td>
                <td><? echo $row[csf('dia_width')]; ?></td>
                <td align="right"><? $total_finish_fabric+=$row[csf('finish_fabric')]; echo number_format($row[csf('finish_fabric')],2); ?></td>
                <td align="right"><? echo number_format($row[csf('process_loss')],2); ?></td>
                <td align="right"><? echo number_format($row[csf('grey_fabric')],2); $total_grey_fabric+=$row[csf('grey_fabric')]; ?></td>
                <td style="word-break:break-all"><p><? echo $row[csf('remarks')]; ?></p></td>
                <td align="right"><? echo number_format($row[csf('rate')],3); ?></td>
               	<td  align="right"><? echo number_format($row[csf('grey_fabric')]*$row[csf('rate')],2);//echo number_format($row[csf('rate')],4); $toatl_rate+=$row[csf('rate')]; ?></td>
            </tr>
            <?
            $total_amount+=$row[csf('grey_fabric')]*$row[csf('rate')];
            $total_process_loss+=$row[csf('process_loss')];
            $i++;
            }
            ?>
            </tbody>
        </table>

        <table class="rpt_table" width="1430" border="2" cellpadding="0" cellspacing="0" rules="all">
            <tfoot>
                <tr>
                    <th colspan="13" width="950" align="right">Total:</th>
                    <th width="70" align="right"><? echo number_format($total_finish_fabric,2); ?></th>
                    <th width="70">&nbsp;</th>
                    <th width="70" align="right"><? echo number_format($total_grey_fabric,2);  ?></th>
                    <th width="69">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="52" align="right"><? echo number_format($total_amount,2); ?></th>
                </tr>
            </tfoot>
        </table>
		<br>
		<br>
		<?
			$composition_arr=array();
			$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
			$lib_fabric_composition=return_library_array( "select a.id,b.fabric_composition_name  from lib_yarn_count_determina_mst a ,lib_fabric_composition b where a.fabric_composition_id=b.id", "id", "fabric_composition_name");


	/* $yarnDtlsArr=array();

	$yarn_dtls = sql_select("SELECT id, booking_no, wo_non_ord_samp_book_dtls_id, count_id,percent_one, copm_one_id, cons_ratio, type_id, cons_qnty from wo_non_ord_samp_yarn_dtls where is_deleted=0 and status_active=1 and booking_no =$txt_booking_no");
	foreach($yarn_dtls as $yrow)
	{
		$yarnDtlsArr[$yrow[csf('wo_non_ord_samp_book_dtls_id')]]['ycount'].=$lib_yarn_count[$yrow[csf('count_id')]].',';
		$yarnDtlsArr[$yrow[csf('wo_non_ord_samp_book_dtls_id')]]['percent'].=$yrow[csf('percent_one')].',';
		$yarnDtlsArr[$yrow[csf('wo_non_ord_samp_book_dtls_id')]]['composition'].=$composition[$yrow[csf('copm_one_id')]].',';
		$yarnDtlsArr[$yrow[csf('wo_non_ord_samp_book_dtls_id')]]['type'].=$yarn_type[$yrow[csf('type_id')]].',';
	}
	unset($yarn_dtls); */

    $sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );

	// $sql= sql_select("select id, style_id, style_des, sample_type, body_part, color_type_id, construction, composition, gsm_weight, gmts_color, fabric_color, gmts_size, item_size, dia_width, finish_fabric, process_loss, grey_fabric, rate, amount, lib_yarn_count_deter_id as lib_yarn_count_deter_id, yarn_details, remarks  FROM wo_non_ord_samp_booking_dtls  WHERE booking_no =$txt_booking_no and status_active=1 and is_deleted=0 order by id");

	$sql= sql_select("SELECT a.id, a.composition,a.lib_yarn_count_deter_id as lib_yarn_count_deter_id,  b.count_id,b.percent_one, b.copm_one_id, b.cons_ratio, b.type_id, b.cons_qnty FROM wo_non_ord_samp_booking_dtls a, wo_non_ord_samp_yarn_dtls b WHERE a.id=b.wo_non_ord_samp_book_dtls_id and a.booking_no =$txt_booking_no and a.status_active=1 and a.is_deleted=0 order by b.id");


?>
        <table class="rpt_table" width="440"  align="center "border="2" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th width="50">Count</th>
					<th width="100">Composition</th>
					<th width="70">Yarn Composition%</th>
                    <th width="150">Y/Type</th>
                    <th width="70">Cons Qnty</th>
                </tr>
            </thead>
            <tbody>
			<?
            $i=1;
			$attributes=array('count_id','copm_one_id','cons_ratio','type_id');
			foreach ($sql as $row)
            {
				$index=$row[csf('count_id')].'*'.$row[csf('copm_one_id')].'*'.$row[csf('cons_ratio')].'*'.$row[csf('type_id')];
				foreach($attributes as $attr){
					$yarn_data_arr[$index][$attr]=$row[csf($attr)];
				}
				$yarn_data_arr[$index]['cons_qnty']+=$row[csf('cons_qnty')];				
			}
            foreach ($yarn_data_arr as $row)
            {
				?>
				<tr>
					<td align="center"><? echo $lib_yarn_count[$row['count_id']]; ?></td>
					<td align="center"><? echo  $composition[$row['copm_one_id']]; ?></td>
					<td align="center"><? echo  $row['cons_ratio']; ?></td>
					<td align="center"><? echo $yarn_type[$row['type_id']]; ?></td>
					<td align="center"><? echo number_format($row['cons_qnty'],2); $total_grey_fabric+=$row['cons_qnty']; ?></td>
				</tr>
				<?
				$i++;
            }
            ?>
            </tbody>
        </table>
		<br>
		<br>
		<?php

			$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
			$collar_cuff_data=sql_select("select a.id,a.booking_no, b.gmts_color,a.body_part_id,a.gmts_size, a.item_size,a.qty ,a.wo_non_ord_samp_booking_dtl_id,b.id	from wo_booking_colar_culff_dtls a,wo_non_ord_samp_booking_dtls b 	where  b.id=a.wo_non_ord_samp_booking_dtl_id and a.booking_no=$txt_booking_no and a.status_active=1");

			foreach($collar_cuff_data as $row){
				$collar_wise_data[$row[csf('body_part_id')]]=$row[csf('body_part_id')];
				$gmts_size_arr[$row[csf('body_part_id')]][$row[csf('gmts_size')]]=$row[csf('gmts_size')];
				$item_size_arr[$row[csf('body_part_id')]][$row[csf('item_size')]]=$row[csf('item_size')];
				$gmts_color_arr[$row[csf('body_part_id')]][$row[csf('gmts_color')]]=$row[csf('gmts_color')];
				$gmts_color_qnty_arr[$row[csf('body_part_id')]][$row[csf('gmts_color')]][$row[csf('gmts_size')]]['qnty']+=$row[csf('qty')];

			}

		foreach($collar_wise_data as $bid){

		?>
		<table class="rpt_table" width="50%"  style="float:left;"border="2" cellpadding="0" cellspacing="0" rules="all">
            <thead>

				<tr>
					<th width="100" colspan="4"><?=$body_part[$bid];?> - Colour Size Breakedown in Pcs </th>

                </tr>
                <tr>
					<th width="100">Gmts Size</th>
						<?php
						foreach($gmts_size_arr[$bid] as $val){?>
						 <th width="50"><?=$val;?></th>
					   <?}?>
                    <th width="100">Total</th>
                </tr>
				<tr>
					<th width="100"><?=$body_part[$bid];?></th>
						<?php	foreach($item_size_arr[$bid] as $val){?>
						 <th width="50"><?=$val;?></th>
						<?}?>
                    <th width="100">Total</th>
                </tr>

				<?php
					foreach($gmts_color_arr[$bid] as $val){?>

					<tr>
						<th width="100"><?=$color_library_arr[$val];?></th>
						<?php
						foreach($gmts_size_arr[$bid] as $sid){?>
						 <th width="50"><?=$gmts_color_qnty_arr[$bid][$val][$sid]['qnty'];?></th>
					   <?
						$color_total[$bid][$val]+=$gmts_color_qnty_arr[$bid][$val][$sid]['qnty'];
						$size_total[$bid][$sid]+=$gmts_color_qnty_arr[$bid][$val][$sid]['qnty'];
					}?>
						<th width="100"><?=$color_total[$bid][$val];?></th>
					</tr>
				<?}?>
				<tr>

					<th width="100">Size Total</th>
						<?php
						foreach($gmts_size_arr[$bid] as $val){?>
						 <th width="50"><?=$size_total[$bid][$val];?></th>

					<?$total_qnty[$bid]+=$size_total[$bid][$val];}?>


                    <th width="100"><?=$total_qnty[$bid];?></th>
                </tr>

            </thead>
            <tbody>


			</tbody>
          </table>



        <?
		}




		$sql_img=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='sample_booking_non' and master_tble_id=$txt_booking_no and file_type=1");
		?>
		<br>
        <table class="rpt_table" width="100%"  border="2" cellpadding="0" cellspacing="0" rules="all">
        <caption align="center"><b>View Photo</b></caption>
        <tr>
        <td colspan="8" height="100">
       <?
        foreach($sql_img as $img)
		{
		?>
        	<img  src='../../<? echo $img[csf('image_location')]; ?>' height='90px' width='90px' /> &nbsp;&nbsp;
        <?
		}
		?>
        </td>
        </tr>
        </table>


        <br/>
		<div style="width:1330px; float:left">

   <table align="left" class="rpt_table" width="1330"  border="1" cellpadding="0" cellspacing="0" rules="all">

    <thead align="center">
       <tr>
                <th align="left" colspan="6"><strong>Accessoris Requirement</strong></th>
       </tr>
           <tr>
            <th width="30">Sl</th>
            <th width="250">Item</th>
            <th width="300">Desc.</th>
            <th width="80">Qnty</th>
            <th width="80">UOM</th>
            <th width="">Remarks</th>
     </tr>

    </thead>
    </table>
     <table class="rpt_table" width="1330"  border="1" cellpadding="0" cellspacing="0" rules="all">
       <tbody>
        <?

        $k=1;$total_acc_qty=0;
        $sql_acces="select id,booking_no,item_group_id,description,uom,qty,remarks from wo_non_ord_booking_acc_dtls where booking_no=$txt_booking_no and description is not null";
     $sql_result= sql_select( $sql_acces);
       foreach($sql_result as $row)
         {
     ?>
       <tr>
            <td width="30" align="center"><? echo $k; ?></td>
            <td width="250"><? echo $item_library[$row[csf('item_group_id')]]; ?></td>
            <td width="300"><? echo $row[csf('description')]; ?></td>
            <td width="80" align="right"><? echo $row[csf('qty')]; ?></td>
            <td width="80" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
            <td width=""><? echo $row[csf('remarks')]; ?></td>

      </tr>
      <?
       $k++;
       $total_acc_qty+=$row[csf('qty')];
       }
       ?>
     </tbody>


      </table>
	  </br>
	</div>

	</br>
	 <div style="width:1330px; float:left">
      <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="100%">
                    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th width="3%">Sl</th><th width="97%" align="left">Spacial Instruction</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no and entry_form=90");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>
                                </tr>
                            <?
						}
					}
					/*else
					{
						$i=0;
					$data_array=sql_select("select id, terms from  lib_terms_condition");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>

                                </tr>
                    <?
						}
					} */
					?>
                </tbody>
                </table>
                </td>

            </tr>
        </table>
   </div>
			<br>
          <?
		 	echo signature_table(6, $cbo_company_name, "1230px");
			echo "****".custom_file_name($varcode_booking_no,$style_sting,'');
		  ?>
       </div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
        </script>
       <?
	   exit();
}

if($action=="show_fabric_booking_report8")//shariar
{
	extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$item_library=return_library_array( "select id,item_name from lib_item_group", "id", "item_name"  );
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=9");
	list($nameArray_approved_row)=$nameArray_approved;
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:1330px" align="center">
    <table width="100%" cellpadding="0" cellspacing="0" style="border:2px solid black">
        <tr>
        <td width="100">
        <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
        </td>
        <td width="1000">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
            <td align="center" style="font-size:20px;">
            <?php
				echo $company_library[$cbo_company_name];
				?>
            </td>
            </tr>
            <tr>
            <td align="center" style="font-size:14px">
            <?
            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
            foreach ($nameArray as $result)
            {
            ?>
                Plot No: <? echo $result[csf('plot_no')]; ?>
                Level No: <? echo $result[csf('level_no')]?>
                Road No: <? echo $result[csf('road_no')]; ?>
                Block No: <? echo $result[csf('block_no')];?>
                City No: <? echo $result[csf('city')];?>
                Zip Code: <? echo $result[csf('zip_code')]; ?>
                Province No: <?php echo $result[csf('province')]; ?>
                Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                Email Address: <? echo $result[csf('email')];?>
                Website No: <? echo $result[csf('website')];
            }
                ?>

            </td>
            </tr>
            <tr>

            <td align="center" style="font-size:20px">
            <strong><? if($report_title !=""){echo $report_title;} else {echo "Sample Fabric Booking -Without order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
            </td>

            <td>
				<?
                if($nameArray_approved_row[csf('approved_no')]>1)
                {
                ?>
                <strong> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
                <br/>

                <?
                }
                ?>
            </td>

            </tr>
    </table>
    </td>
    <td width="250" id="barcode_img_id">

    </td>
    </tr>
    </table>
    <br>

                <?
				$season="";
				$buyer_req_no="";

				$nameseason=sql_select( "select a.season, b.buyer_req_no  from  sample_development_mst a, sample_development_dtls b, wo_non_ord_samp_booking_dtls c  where  a.id=b. sample_mst_id and a.id=c.style_id and c.booking_no=$txt_booking_no");
				foreach ($nameseason as $season_row)
				{
					$season=$season_row[csf('season')];
					$buyer_req_no=$season_row[csf('buyer_req_no')];

				}

				$fabric_source='';
                $nameArray=sql_select( "select buyer_id,booking_no,booking_date,pay_mode,supplier_id,currency_id,exchange_rate,attention,delivery_date,fabric_source,team_leader,	dealing_marchant from wo_non_ord_samp_booking_mst   where  booking_no=$txt_booking_no");
				foreach ($nameArray as $result)
				{
					$fabric_source=$result[csf('fabric_source')];

					$varcode_booking_no=$result[csf('booking_no')];

				?>
       <table width="100%" style="border:2px solid black; margin-top:-20px">
            <tr>
                <td colspan="6" valign="top"></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:12px"><b>Fab. Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>

            </tr>
            <tr>

                <td width="100"><span style="font-size:12px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3)
				{
					echo $company_library[$result[csf('supplier_id')]];
				}
				else
				{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
				}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;<?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
				$comAdd=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$result[csf('supplier_id')]);
					foreach ($comAdd as $comAddRow){
						echo $comAddRow[csf('plot_no')].'&nbsp;';
						echo $comAddRow[csf('level_no')].'&nbsp;' ;
						echo $comAddRow[csf('road_no')].'&nbsp;';
						echo $comAddRow[csf('block_no')].'&nbsp;';
						echo $comAddRow[csf('city')].'&nbsp;';
						//echo $comAddRow[csf('zip_code')].'&nbsp;';
						//echo $comAddRow[csf('province')].'&nbsp;';
						//echo $country_arr[$comAddRow[csf('country_id')]].'&nbsp;';
						//echo $comAddRow[csf('email')];
						//echo $comAddRow[csf('website')];
					}
				}
				else{
					echo $supplier_address_arr[$result[csf('supplier_id')]];
				}
				//echo $supplier_address_arr[$result[csf('supplier_id')]];

				?></td>
            </tr>


            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>

                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>

            </tr>
            <tr>
				<td  width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td  width="110" >:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                <td width="100" style="font-size:12px">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td  width="100" style="font-size:12px">&nbsp;</td>
                <td  width="110" >&nbsp;</td>

            </tr>
        </table>
        <?
			}
		?>

      <br/>
      <?
			$composition_arr=array();
			$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
			$lib_fabric_composition=return_library_array( "select a.id,b.fabric_composition_name  from lib_yarn_count_determina_mst a ,lib_fabric_composition b where a.fabric_composition_id=b.id", "id", "fabric_composition_name");


	$yarnDtlsArr=array();

	$yarn_dtls = sql_select("SELECT id, booking_no, wo_non_ord_samp_book_dtls_id, count_id, copm_one_id, cons_ratio, type_id, cons_qnty from wo_non_ord_samp_yarn_dtls where is_deleted=0 and status_active=1 and booking_no =$txt_booking_no");
	//echo "SELECT id, booking_no, wo_non_ord_samp_book_dtls_id, count_id, copm_one_id, cons_ratio, type_id, cons_qnty from wo_non_ord_samp_yarn_dtls where is_deleted=0 and status_active=1 and booking_no =$txt_booking_no";
	foreach($yarn_dtls as $yrow)
	{
		$yarnDtlsArr[$yrow[csf('wo_non_ord_samp_book_dtls_id')]]['ycount'].=$lib_yarn_count[$yrow[csf('count_id')]].',';
		$yarnDtlsArr[$yrow[csf('wo_non_ord_samp_book_dtls_id')]]['composition'].=$composition[$yrow[csf('copm_one_id')]].',';
		$yarnDtlsArr[$yrow[csf('wo_non_ord_samp_book_dtls_id')]]['type'].=$yarn_type[$yrow[csf('type_id')]].',';
	}
	unset($yarn_dtls);

    $sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	$sql= sql_select("select id, style_id, style_des, sample_type, body_part, color_type_id, construction, composition, gsm_weight, gmts_color, fabric_color, gmts_size, item_size, dia_width, finish_fabric, process_loss, grey_fabric, rate, amount, lib_yarn_count_deter_id as lib_yarn_count_deter_id, yarn_details, remarks  FROM wo_non_ord_samp_booking_dtls  WHERE booking_no =$txt_booking_no and status_active=1 and is_deleted=0 order by id");
?>
        <table class="rpt_table" width="1290"  border="2" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th width="20">Sl</th>
                    <th width="100">Style Des</th>
                    <th width="100">Sample</th>
                    <th width="120">Body Part</th>
                    <th width="90">Color Type</th>
                    <th width="100">Construction</th>
					<th width="100">Fabric Composition</th>
                    <th width="50">Y/Count</th>
					<th width="100">Y/Comp</th>
                    <th width="70">Y/Type</th>
                    <th width="50">GSM</th>
                    <th width="80">Fab.Color</th>
                    <th width="50">Dia/ Width</th>
                    <th width="70">Fin Fab Qnty</th>
                    <th width="50">Rate</th>
                    <th width="70">Amount</th>
					<th width="70">Remarks</th>
                </tr>
            </thead>
            <tbody>
			<?
            $style_id="";
            $total_finish_fabric=0;
            $total_grey_fabric=0;$total_process_loss=0;
            $toatl_rate=0;
            $total_amount=0;

            $i=1;
            foreach ($sql as $row)
            {
                $style_id.=$row[csf('style_id')].",";
                //$yarn_counts=$lib_yarn_count[$lib_yarn_count_determina_arr[$row[csf('lib_yarn_count_deter_id')]]];
                //$yarn_count_type=$yarn_type[$lib_yarn_type_count_determina_arr[$row[csf('lib_yarn_count_deter_id')]]];

               // $yarn_data= explode(",",$composition_data_arr[$row[csf('lib_yarn_count_deter_id')]]);
               // $yarn_counts=$yarn_data[0];
			  $fabric_com= $lib_fabric_composition[$row[csf('lib_yarn_count_deter_id')]];
			//    $fabric_com= $row[csf('composition')];
                $yarn_count_type=rtrim($yarnDtlsArr[$row[csf('id')]]['type'],',');
				 $ycount=rtrim($yarnDtlsArr[$row[csf('id')]]['ycount'],',');
                if($row[csf('style_des')]) $style_sting.=$row[csf('style_des')].'_';
				//if($yarn_counts=="")
				$yarn_counts=implode(",",array_filter(array_unique(explode(",",$ycount))));
				$yarn_count_type=implode(",",array_filter(array_unique(explode(",",$yarn_count_type))));



            ?>
            <tr>
                <td><? echo $i; ?></td>
                <td><? echo $row[csf('style_des')]; ?></td>
                <td><? echo $sample_library[$row[csf('sample_type')]]; ?></td>
                <td><? echo $body_part[$row[csf('body_part')]]; ?></td>
                <td><? echo $color_type[$row[csf('color_type_id')]]; ?></td>
                <td><? echo $row[csf('construction')]; ?></td>
				<td><? echo $fabric_com; ?></td>

                <td><? echo $yarn_counts; ?></td>
				<td><? echo  $row[csf('composition')];//$composition_arr[$row[csf('lib_yarn_count_deter_id')]]; ?></td>
                <td><? echo $yarn_count_type; ?></td>
                <td><? echo $row[csf('gsm_weight')];//$color_library[$row[csf('gmts_color')]]; ?></td>
                <td><? echo $color_library[$row[csf('fabric_color')]]; ?></td>
                <td><? echo $row[csf('dia_width')]; ?></td>
                <td align="right"><? $total_finish_fabric+=$row[csf('finish_fabric')]; echo number_format($row[csf('finish_fabric')],2); ?></td>
                <td align="right"><? echo number_format($row[csf('rate')],3); ?></td>
               	<td  align="right"><? echo number_format($row[csf('grey_fabric')]*$row[csf('rate')],2);//echo number_format($row[csf('rate')],4); $toatl_rate+=$row[csf('rate')]; ?></td>
				<td style="word-break:break-all"><p><? echo $row[csf('remarks')]; ?></p></td>
            </tr>
            <?
            $total_amount+=$row[csf('grey_fabric')]*$row[csf('rate')];
            $total_process_loss+=$row[csf('process_loss')];
            $i++;
            }
            ?>
            </tbody>
			<tfoot>
                <tr>
                    <th colspan="13" align="right">Total:</th>
                    <th width="70" align="right"><? echo number_format($total_finish_fabric,2); ?></th>
                    <th width="50">&nbsp;</th>
                    <th width="52" align="right"><? echo number_format($total_amount,2); ?></th>
					<th width="69">&nbsp;</th>
                </tr>
            </tfoot>
        </table>
		<br>
		<br>
		<?php

			$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
			$collar_cuff_data=sql_select("select a.id,a.booking_no, b.gmts_color,a.body_part_id,a.gmts_size, a.item_size,a.qty ,a.wo_non_ord_samp_booking_dtl_id,b.id	from wo_booking_colar_culff_dtls a,wo_non_ord_samp_booking_dtls b 	where  b.id=a.wo_non_ord_samp_booking_dtl_id and a.booking_no=$txt_booking_no and a.status_active=1");

			foreach($collar_cuff_data as $row){
				$collar_wise_data[$row[csf('body_part_id')]]=$row[csf('body_part_id')];
				$gmts_size_arr[$row[csf('body_part_id')]][$row[csf('gmts_size')]]=$row[csf('gmts_size')];
				$item_size_arr[$row[csf('body_part_id')]][$row[csf('item_size')]]=$row[csf('item_size')];
				$gmts_color_arr[$row[csf('body_part_id')]][$row[csf('gmts_color')]]=$row[csf('gmts_color')];
				$gmts_color_qnty_arr[$row[csf('body_part_id')]][$row[csf('gmts_color')]][$row[csf('gmts_size')]]['qnty']+=$row[csf('qty')];

			}

		foreach($collar_wise_data as $bid){

		?>
		<table class="rpt_table" width="50%"  style="float:left;"border="2" cellpadding="0" cellspacing="0" rules="all">
            <thead>

				<tr>
					<th width="100" colspan="4"><?=$body_part[$bid];?> - Colour Size Breakedown in Pcs </th>

                </tr>
                <tr>
					<th width="100">Gmts Size</th>
						<?php
						foreach($gmts_size_arr[$bid] as $val){?>
						 <th width="50"><?=$val;?></th>
					   <?}?>
                    <th width="100">Total</th>
                </tr>
				<tr>
					<th width="100"><?=$body_part[$bid];?></th>
						<?php	foreach($item_size_arr[$bid] as $val){?>
						 <th width="50"><?=$val;?></th>
						<?}?>
                    <th width="100">Total</th>
                </tr>

				<?php
					foreach($gmts_color_arr[$bid] as $val){?>

					<tr>
						<th width="100"><?=$color_library_arr[$val];?></th>
						<?php
						foreach($gmts_size_arr[$bid] as $sid){?>
						 <th width="50"><?=$gmts_color_qnty_arr[$bid][$val][$sid]['qnty'];?></th>
					   <?
						$color_total[$bid][$val]+=$gmts_color_qnty_arr[$bid][$val][$sid]['qnty'];
						$size_total[$bid][$sid]+=$gmts_color_qnty_arr[$bid][$val][$sid]['qnty'];
					}?>
						<th width="100"><?=$color_total[$bid][$val];?></th>
					</tr>
				<?}?>
				<tr>

					<th width="100">Size Total</th>
						<?php
						foreach($gmts_size_arr[$bid] as $val){?>
						 <th width="50"><?=$size_total[$bid][$val];?></th>

					<?$total_qnty[$bid]+=$size_total[$bid][$val];}?>


                    <th width="100"><?=$total_qnty[$bid];?></th>
                </tr>

            </thead>
            <tbody>


			</tbody>
          </table>



        <?
		}




		$sql_img=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='sample_booking_non' and master_tble_id=$txt_booking_no and file_type=1");
		?>
		<br>
        <table class="rpt_table" width="100%"  border="2" cellpadding="0" cellspacing="0" rules="all">
        <caption align="center"><b>View Photo</b></caption>
        <tr>
        <td colspan="8" height="100">
       <?
        foreach($sql_img as $img)
		{
		?>
        	<img  src='../../<? echo $img[csf('image_location')]; ?>' height='90px' width='90px' /> &nbsp;&nbsp;
        <?
		}
		?>
        </td>
        </tr>
        </table>


        <br/>


	</br>
	 <div style="width:1330px; float:left">
      <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="100%">
                    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th width="3%">Sl</th><th width="97%" align="left">Spacial Instruction</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no and entry_form=90");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>
                                </tr>
                            <?
						}
					}
					/*else
					{
						$i=0;
					$data_array=sql_select("select id, terms from  lib_terms_condition");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>

                                </tr>
                    <?
						}
					} */
					?>
                </tbody>
                </table>
                </td>

            </tr>
        </table>
   </div>
			<br>
          <?
		 	echo signature_table(6, $cbo_company_name, "1230px");
			echo "****".custom_file_name($varcode_booking_no,$style_sting,'');
		  ?>
       </div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
        </script>
       <?
	   exit();
}

if($action=="show_fabric_booking_report3")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);

	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');

	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$size_name_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');

	$style_arr=array();
	$nameseason=sql_select( "select a.id,a.season,a.style_ref_no  from  sample_development_mst a, sample_development_dtls b, wo_non_ord_samp_booking_dtls c  where  a.id=b. sample_mst_id and a.id=c.style_id and c.booking_no=$txt_booking_no group by a.id,a.season,a.style_ref_no");
	foreach ($nameseason as $season_row)
	{
		$season=$season_row[csf('season')];
		$style_arr[$season_row[csf('id')]]=$season_row[csf('style_ref_no')];
	}
	//print_r($style_arr);
	$fabric_source='';
	$nameArray=sql_select( "select buyer_id,supplier_id,currency_id,pay_mode,dealing_marchant,attention,exchange_rate,delivery_date,booking_no,booking_date from wo_non_ord_samp_booking_mst   where  booking_no=$txt_booking_no");
	$buyer_name=$nameArray[0][csf('buyer_id')];

	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=9");
	list($nameArray_approved_row)=$nameArray_approved;

?>
<div style="width:1200px" align="center">
    										<!--    Header Company Information         -->
       <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
                      <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
                    <tr>
                        <td width="60" align="left">
                        <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='60%' width='60%' />
                        </td>
                        <td  colspan="6" align="center" style="font-size:20px;">
                        <strong>
                          <?php
echo $company_library[$cbo_company_name];
?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>

                        </td>
                    </tr>
                    <tr>
                            <td align="center" style="font-size:12px; margin-top:-10px" colspan="6">
                            <?
							echo show_company($cbo_company_name,'','');
                            /*$nameArray_address=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray_address as $result)
                            {
                            ?>
                                            <? echo $result[csf('plot_no')]; ?>
                                           <? echo $result[csf('level_no')]?>
                                            <? echo $result[csf('road_no')]; ?>
                                           <? echo $result[csf('block_no')];?>
                                             <? echo $result[csf('city')];?>
                                             <? echo $result[csf('zip_code')]; ?>
                                            <?php echo $result[csf('province')]; ?>
                                            <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                           <? echo $result[csf('email')];?>
                                            <? echo $result[csf('website')];
                            }*/
                                            ?>

                               </td>
                            </tr>
                            <tr>
                        		<td colspan="5" align="center"><strong> Sample Fabric Booking &nbsp;&nbsp;&nbsp;&nbsp; </strong> </td>

                                <td>
                              <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <strong> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
                                  <br/>

								  <?
								 }
							  	?>
                             </td>
                        	</tr>
                             </table>
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black; border-top:hidden" >
                       		<tr>

                             <td style="font-size:15px;">
                             <strong>Booking No</strong>
                            </td>
                            <td style="font-size:15px;">:
                            	&nbsp;
							<?php
echo $nameArray[0][csf('booking_no')];
?>
                            </td>
                            <td  style="font-size:15px;">
                               <strong>Booking Date</strong>

                            </td>
                             <td  style="font-size:15px;">:
                              &nbsp;
                              <?php
echo change_date_format($nameArray[0][csf('booking_date')]);
?>
                            </td>
                            <td style="font-size:15px;">
                            <strong>Delivery Date</strong>

                            </td>
                             <td style="font-size:15px;">:
                           		&nbsp;
                              <?php
echo change_date_format($nameArray[0][csf('delivery_date')]);
?>
                            </td>

                        </tr>
                        <tr>
                            <td align="left" style="font-size:15px;">
                            <strong> Buyer Name</strong>
                            </td>
                            <td  style="font-size:15px;">:
                          		&nbsp;
                              <?php
echo $buyer_name_arr[$buyer_name];
?>
                            </td>
                            <td  style="font-size:15px;">
                           <strong> Supplier Name</strong>

                            </td>
                             <td  style="font-size:15px;">:
                           		&nbsp;
                              <?php
if ($nameArray[0][csf('pay_mode')] == 5 || $nameArray[0][csf('pay_mode')] == 3) {
	echo $company_library[$nameArray[0][csf('supplier_id')]];
} else {
	echo $supplier_name_arr[$nameArray[0][csf('supplier_id')]];
}
//echo  $supplier_name_arr[$nameArray[0][csf('supplier_id')]];
?>
                            </td>
                             <td  style="font-size:15px;">
                           <strong> Supplier Address</strong>

                            </td>
                             <td  style="font-size:15px;">:
                           		&nbsp;
                              <?php
if ($nameArray[0][csf('pay_mode')] == 5 || $nameArray[0][csf('pay_mode')] == 3) {
	$comAdd = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=" . $nameArray[0][csf('supplier_id')]);
	foreach ($comAdd as $comAddRow) {
		echo $comAddRow[csf('plot_no')] . '&nbsp;';
		echo $comAddRow[csf('level_no')] . '&nbsp;';
		echo $comAddRow[csf('road_no')] . '&nbsp;';
		echo $comAddRow[csf('block_no')] . '&nbsp;';
		echo $comAddRow[csf('city')] . '&nbsp;';
		//echo $comAddRow[csf('zip_code')].'&nbsp;';
		//echo $comAddRow[csf('province')].'&nbsp;';
		//echo $country_arr[$comAddRow[csf('country_id')]].'&nbsp;';
		//echo $comAddRow[csf('email')];
		//echo $comAddRow[csf('website')];
	}
} else {
	echo $supplier_address_arr[$result[csf('supplier_id')]];
}
//echo  $currency[$nameArray[0][csf('currency_id')]];
//echo $supplier_address_arr[$nameArray[0][csf('supplier_id')]];
?>
                            </td>
                        </tr>

                        <tr>
                        	<td style="font-size:15px;"><strong> Currency </strong> </td>
                            <td style="font-size:15px;">: &nbsp;<? echo  $currency[$nameArray[0][csf('currency_id')]];?> </td>
                            <td style="font-size:15px;"><strong> Season </strong> </td>
                            <td style="font-size:15px;">: &nbsp;<? echo $season;?> </td>
                            <td style="font-size:15px;"><strong> Dealing Merchant </strong> </td>
                            <td style="font-size:15px;">: &nbsp;<? echo  $marchentrArr[$nameArray[0][csf('dealing_marchant')]];?> </td>
                        </tr>
                      </table>
                      <br>
                      <?
		  $body_part_arr=array();$body_part_other_arr=array(); $color_part_size_arr=array();$color_part_size_qty_arr=array();$body_type_size_qty_arr=array();

		 $sql_dtl=sql_select("select body_part
		 from wo_non_ord_samp_booking_dtls   where  booking_no=$txt_booking_no and status_active=1 and is_deleted=0 and body_part in(2,3)");

		 foreach($sql_dtl as $row)
		 {
			 $body_part_arr[$row[csf('body_part')]]= $row[csf('body_part')];
		 }
		 $sql_others=sql_select("select body_part
		 from wo_non_ord_samp_booking_dtls   where  booking_no=$txt_booking_no and status_active=1 and is_deleted=0 and body_part not in(2,3)");

		 foreach($sql_others as $row)
		 {
			 $body_part_other_arr[$row[csf('body_part')]]= $row[csf('body_part')];
		 }
		 $sql_data=sql_select("select body_part,gmts_size,sum(item_qty)
 as item_qty	 from wo_non_ord_samp_booking_dtls   where  booking_no=$txt_booking_no and status_active=1 and is_deleted=0 and body_part in(2,3) group by body_part,gmts_size order by  body_part,gmts_size");
		 $col_span=0;
		 $gmts_size_arr=array();
		 foreach($sql_data as $row)
		 {
			 $gmts_size_arr[$row[csf('body_part')]][$row[csf('gmts_size')]]+= $row[csf('item_qty')];
			 $col_span++;
		 }
		  $sql_data_item=sql_select("select distinct body_part,gmts_size,item_size	 from wo_non_ord_samp_booking_dtls   where  booking_no=$txt_booking_no and status_active=1 and is_deleted=0 and body_part in(2,3) and item_size!='null' order by  body_part,gmts_size");

		 $items_size_arr=array();
		 foreach($sql_data_item as $row)
		 {
			 $items_size_arr[$row[csf('body_part')]][$row[csf('gmts_size')]]= $row[csf('item_size')];

		 }
		 $sql_other_data=sql_select("select body_part,gmts_size,sum(item_qty)
 as item_qty	 from wo_non_ord_samp_booking_dtls   where  booking_no=$txt_booking_no and status_active=1 and is_deleted=0 and body_part not in(2,3) group by body_part,gmts_size");
		// $col_span=0;
		 $item_size_other_arr=array();
		 foreach($sql_other_data as $row)
		 {
			 $item_size_other_arr[$row[csf('body_part')]][$row[csf('gmts_size')]]+= $row[csf('item_qty')];
			 //$col_span++;
		 }
		// echo $col_span;
		$grey_size_qty_arr=array();


		  $sql_sam_data=sql_select("select style_id,style_des,fabric_description,gsm_weight,gmts_color,finish_fabric,grey_fabric,body_part,gmts_size,body_type_id,item_qty
		 from wo_non_ord_samp_booking_dtls   where  booking_no=$txt_booking_no and status_active=1 and is_deleted=0 and body_part in(2,3) order by body_part");

		 $item_size_qty_arr=array(); $finish_size_qty_arr=array();$item_size_body_type_arr=array();$item_size_body_type_arr2=array();
		 foreach($sql_sam_data as $row)
		 {
			 $item_size_qty_arr[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$row[csf('body_part')]][$row[csf('gmts_size')]]+= $row[csf('item_qty')];
			 $finish_size_qty_arr[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$row[csf('body_part')]][$row[csf('gmts_size')]]+= $row[csf('finish_fabric')];
			 $grey_size_qty_arr[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$row[csf('body_part')]][$row[csf('gmts_size')]]+= $row[csf('grey_fabric')];

			 if($row[csf('body_part')]==2)
			 {
			  $item_size_body_type_arr[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$row[csf('body_part')]][$row[csf('gmts_size')]]= $row[csf('body_type_id')];
			 }
			 else if($row[csf('body_part')]==3)
			 {

			  $item_size_body_type_arr2[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$row[csf('body_part')]][$row[csf('gmts_size')]]=$row[csf('body_type_id')];
			 }
		 }
		// print_r($item_size_body_type_arr);
		  $sql_other_data=sql_select("select style_id,style_des,fabric_description,gsm_weight,gmts_color,body_part,gmts_size,finish_fabric,grey_fabric
		 from wo_non_ord_samp_booking_dtls   where  booking_no=$txt_booking_no and status_active=1 and is_deleted=0 and body_part not in(2,3)");

		 $item_size_other_qty_arr=array(); $item_grey_size_other_qty_arr=array();
		 foreach($sql_other_data as $row)
		 {
			 $item_size_other_qty_arr[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$row[csf('body_part')]][$row[csf('gmts_size')]]+= $row[csf('finish_fabric')];
			 $item_grey_size_other_qty_arr[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$row[csf('body_part')]][$row[csf('gmts_size')]]+= $row[csf('grey_fabric')];
		 }
			//print_r($item_size_other_qty_arr);
		 $sql_result=sql_select("select style_id,style_des,fabric_description,gsm_weight,gmts_color,count(id) as id
		 from wo_non_ord_samp_booking_dtls   where  booking_no=$txt_booking_no and status_active=1 and is_deleted=0 group by style_id,style_des,fabric_description,gsm_weight,gmts_color  order by id");

		?>
    <div style="width:100%;">
    <table align="left" cellspacing="0" width="1200"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
                <tr>
                    <th width="30" rowspan="4">SL</th>
                     <th width="130" rowspan="4">Style</th>
                     <th width="100" rowspan="4">Description</th>
                     <th width="180" rowspan="4">Fabrication</th>
                     <th width="50"  rowspan="4">GSM</th>
                     <th width="70"  rowspan="4">Color</th>
                     <th align="center" width="" colspan="<? echo $col_span + count($body_part_other_arr);?>">Body Part</th>
                    <th width="70" rowspan="4" align="center">Total Qty.</th>
                    <th width="70" rowspan="4" align="center">Total Gray Qty.</th>
                </tr>
                <tr>
                <?
                foreach($body_part_other_arr as $body_id)
				{
				?>
                 <th align="center" width="90" rowspan="3"><? echo $body_part[$body_id];?></th>
                <?
				}
                ?>
                <?
                foreach($body_part_arr as $body_id)
				{
				?>
                 <th align="center" width="90" colspan="<? echo count($gmts_size_arr[$body_id]);?>"><? echo $body_part[$body_id];?></th>
                <?
				}
                ?>
                </tr>
                <tr>
                <?
				foreach($body_part_arr as $body_id)
				{
					foreach($gmts_size_arr[$body_id] as $gmts_size_key=>$value)
					{
					?>
					 <th align="center" width="90"><? echo $size_library[$gmts_size_key];?></th>
					<?
					}
				}
                ?>
                </tr>
                <tr>
                <?
				foreach($body_part_arr as $body_id)
				{
					foreach($gmts_size_arr[$body_id] as $item_size_key=>$value)
					{
						 $item_size=$items_size_arr[$body_id][$item_size_key];
					?>
					 <th align="center" width="90"><? echo $item_size;//$body_id.'='.$item_size_key;//$size_library[$item_size_key];?></th>
					<?
					}
				}
                ?>
                </tr>
            </thead>
            <?
			$k=1;$total_size_qty=0;$total_all_down=0;$total_grey_qty=0;$total_grey_size_qty=0;
			foreach($sql_result as $row)
			{
				//$collar_dtls_id=explode(",",$row[csf('collar_dtls_id')]);
				if($row[csf('style_id')])$style_sting.=$style_arr[$row[csf('style_id')]].'_';
			?>
				 <tr>
					<td rowspan="2"><? echo $k; ?></td>
					<td rowspan="2"> <?
						echo $style_arr[$row[csf('style_id')]];
					 ?>
					 </td>
					<td rowspan="2"> <? echo $row[csf('style_des')];  ?></td>
					<td rowspan="2"> <? echo $row[csf('fabric_description')];  ?></td>
					<td rowspan="2"> <? echo $row[csf('gsm_weight')];  ?></td>
					<td rowspan="2"> <? echo  $color_library[$row[csf('gmts_color')]];  ?></td>
                <?
				$total_other_qty=0;$other_size_grey_qty=0;
                foreach($body_part_other_arr as $body_id)
				{
				?>
                 <td align="right" width="90" rowspan="2"><?
				 $other_size_qty=0; $other_size_grey_qty=0;
				 foreach($item_size_other_arr[$body_id] as $item_size_key=>$value)
					{
				  	$other_size_qty+=$item_size_other_qty_arr[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$body_id][$item_size_key];
					 $other_size_grey_qty+=$item_grey_size_other_qty_arr[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$body_id][$item_size_key];
					 //echo $other_size_grey_qty.'<br>';


                 	}
						if($other_size_qty!=0) echo number_format($other_size_qty,2); else echo ' ';

						$total_other_qty+=$other_size_qty;
						$total_other_down_arr[$body_id]+=$other_size_qty;
						$total_other_grey_down_arr[$body_id]+=$other_size_grey_qty;
						$total_other_grey_qty+=$other_size_grey_qty;
						//print_r($total_other_grey_down_arr);
					?>
                  </td>
                <?
				}
				$tot_size_qty=0;$tot_grey_size_qty=0;
				foreach($body_part_arr as $body_id)
				{
					foreach($gmts_size_arr[$body_id] as $gmts_size_key=>$value)
					{
					?>
					 <td align="center" width="90"><?
					 $body_type_ids=$item_size_body_type_arr[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$body_id][$gmts_size_key];

					  $body_type_ids2=$item_size_body_type_arr2[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$body_id][$gmts_size_key];

					 $size_qty=$item_size_qty_arr[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$body_id][$gmts_size_key];
					  $fin_size_qty=$finish_size_qty_arr[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$body_id][$gmts_size_key];
					 $grey_size_qty=$grey_size_qty_arr[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$body_id][$gmts_size_key];


					 if($body_type_ids!=0 || $body_type_ids2!=0)
					 {
						 if($body_id==2)
						 {
					  			echo $body_type_arr[$body_type_ids];
						 }
						 else if($body_id==3)
						 {

								echo $body_type_arr[$body_type_ids2];
						 }
					 }
					 else
					 {
						 echo '&nbsp;';
					 }
					 ?></td>
					<? //$tot_size_qty+=$size_qty+$fin_size_qty;
					$tot_size_qty+=$fin_size_qty;
					$total_all_down+=$fin_size_qty;
					$tot_grey_size_qty+=$grey_size_qty;
					}
				}

                ?>
				<td rowspan="2" align="right"><? echo  number_format($tot_size_qty+$total_other_qty,2); ?></td>
                <td rowspan="2" align="right"><? echo  number_format($tot_grey_size_qty+$total_other_grey_qty,2); ?></td>
				</tr>
                 <tr>
              <?
				foreach($body_part_arr as $body_id)
				{
					$size_qty=0;$fin_size_qty=0;$grey_size_qty=0;
					foreach($gmts_size_arr[$body_id] as $gmts_size_key=>$value)
					{
					?>
					 <td align="right" width="90" ><? //echo $item_size_qty_arr[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$body_id][$gmts_size_key].' PCS';
					 $size_qty=$item_size_qty_arr[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$body_id][$gmts_size_key];
					 $fin_size_qty=$finish_size_qty_arr[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$body_id][$gmts_size_key];
					   $grey_size_qty=$grey_size_qty_arr[$row[csf('style_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('gmts_color')]][$body_id][$gmts_size_key];
					if($size_qty!=0 || $fin_size_qty!=0)
					{
					?>
                  <table style="margin-top:-3px;border-left: hidden; border-right:hidden; border-bottom:hidden; border-top:none"  width="100" border="0" >
                    <tr>
                    <td style=" border-right:solid 1px;"><?  if($fin_size_qty!=0) echo $fin_size_qty.' &nbsp;';else echo ' ';?></td> <td><? if($size_qty!=0) echo $size_qty.' p'; else echo ' ';?></td>
                    </tr>
                    </table>
                    <?
					}
					?>
                   </td>
					<?
					//echo $grey_size_qty.'M';
					//$tot_size_qty=$size_qty+$fin_size_qty;
						$tot_size_qty+=$fin_size_qty;
						$total_grey_size_qty+=$grey_size_qty;
					}
				}

                ?>
				</tr>
				<?
				$k++;
				$total_size_qty+=$tot_size_qty+$total_other_qty;
				$total_grey_qty+=$total_grey_size_qty;
			}
			//echo $total_grey_size_qty.'AD';
				?>
               <tr>
                   <td colspan="6" align="right">Total </td>
                    <?
                       // $row_san=0;
                        foreach($body_part_other_arr as $body_id)
                        {
                             //$row_san=$body_id;
                            // echo $body_id;
                            //count($body_part_other_arr);
                        ?>
                         <td align="right" width="90"><? echo number_format($total_other_down_arr[$body_id],2);?></td>
                        <?
                        }
                        ?>
                        <?
                        foreach($body_part_arr as $body_id)
                        {
                            //print_r($gmts_size_arr[$body_id]);
                        ?>
                         <td align="center" width="90" colspan="<? echo count($gmts_size_arr[$body_id]);?>"><?  //echo $tot_size_qty+$total_size_qty; ?></td>
                        <?
                        }
                        ?>
                        <td  align="right"><?
                        $total_other=0;$total_grey_other=0;
                        foreach($body_part_other_arr as $body_id)
                        {
                            $total_other+=$total_other_down_arr[$body_id];
                            $total_grey_other+=$total_other_grey_down_arr[$body_id];
                            //$total_other_grey_down_arr[$body_id];
                        }
                        echo  number_format($total_all_down+$total_other,2); ?></td>
                         <td  align="right"><?
                        echo  number_format($total_grey_other+$total_grey_size_qty,2); ?></td>
               </tr>
         </table>
         <br/> <br/>
         <table align="left" style="margin-top:20px;" cellspacing="0" width="1200"  border="0" rules="all" class="rpt_table" >            <tr>
                <td width="100%">
                    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th width="3%">Sl</th><th width="97%" align="left">Spacial Instruction</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=90 and booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>
                                </tr>
                            <?
						}
					}
					/*else
					{
						$i=0;
					$data_array=sql_select("select id, terms from  lib_terms_condition");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>

                                </tr>
                    <?
						}
					} */
					?>
                </tbody>
                </table>
                    </td>
                    </tr>
                </table>
       </div>
       </table>
        <br/>
          <?
		 	echo signature_table(6, $cbo_company_name, "1200px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,'');
		  ?>
       </div>

<?
}

if($action=="show_fabric_booking_report4")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=9");
	list($nameArray_approved_row)=$nameArray_approved;
	?>
	<div style="width:1330px" align="center">
    										<!--    Header Company Information         -->
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100">
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php
echo $company_library[$cbo_company_name];
?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
                            ?>
                                            Plot No: <? echo $result[csf('plot_no')]; ?>
                                            Level No: <? echo $result[csf('level_no')]?>
                                            Road No: <? echo $result[csf('road_no')]; ?>
                                            Block No: <? echo $result[csf('block_no')];?>
                                            City No: <? echo $result[csf('city')];?>
                                            Zip Code: <? echo $result[csf('zip_code')]; ?>
                                            Province No: <?php echo $result[csf('province')]; ?>
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                            Email Address: <? echo $result[csf('email')];?>
                                            Website No: <? echo $result[csf('website')];
                            }
                                            ?>

                               </td>
                            </tr>
                            <tr>

                            <td align="center" style="font-size:20px">
                                <strong><? if($report_title !=""){echo $report_title;} else {echo "Sample Fabric Booking -Without order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             </td>

                              <td>
                              <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <strong> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
                                  <br/>

								  <?
								 }
							  	?>
                             </td>

                            </tr>
                      </table>
                </td>
                <td width="250" id="barcode_img_id">

                </td>
            </tr>
       </table>

                <?
				$season="";
				$buyer_req_no="";

				$nameseason=sql_select( "select a.season, b.buyer_req_no  from  sample_development_mst a, sample_development_dtls b, wo_non_ord_samp_booking_dtls c  where  a.id=b. sample_mst_id and a.id=c.style_id and c.booking_no=$txt_booking_no");
				foreach ($nameseason as $season_row)
				{
					$season=$season_row[csf('season')];
					$buyer_req_no=$season_row[csf('buyer_req_no')];

				}

				$fabric_source='';
                $nameArray=sql_select( "select buyer_id,pay_mode,booking_no,booking_date,supplier_id,currency_id,exchange_rate,attention,delivery_date,fabric_source,team_leader,	dealing_marchant from wo_non_ord_samp_booking_mst   where  booking_no=$txt_booking_no");
				foreach ($nameArray as $result)
				{
					$fabric_source=$result[csf('fabric_source')];

					$varcode_booking_no=$result[csf('booking_no')];

				?>
       <table width="100%" style="border:1px solid black">
            <tr>
                <td colspan="6" valign="top"></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>

            </tr>
            <tr>

                <td width="100"><span style="font-size:12px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<?
					if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}

				//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;<?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
				$comAdd=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$result[csf('supplier_id')]);
					foreach ($comAdd as $comAddRow){
						echo $comAddRow[csf('plot_no')].'&nbsp;';
						echo $comAddRow[csf('level_no')].'&nbsp;' ;
						echo $comAddRow[csf('road_no')].'&nbsp;';
						echo $comAddRow[csf('block_no')].'&nbsp;';
						echo $comAddRow[csf('city')].'&nbsp;';
						//echo $comAddRow[csf('zip_code')].'&nbsp;';
						//echo $comAddRow[csf('province')].'&nbsp;';
						//echo $country_arr[$comAddRow[csf('country_id')]].'&nbsp;';
						//echo $comAddRow[csf('email')];
						//echo $comAddRow[csf('website')];
					}
				}
				else{
					echo $supplier_address_arr[$result[csf('supplier_id')]];
				}
				//echo $supplier_address_arr[$result[csf('supplier_id')]];


				?></td>
            </tr>

            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $season; ?></td>
                <td  width="100" style="font-size:12px"><b>Buyer Req. No</b></td>
                <td  width="110" >:&nbsp;<? echo $buyer_req_no; ?></td>
                <td  width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td  width="110" >&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>

            </tr>
        </table>
        <?
			}
		?>

      <br/>

      <?
	  $composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 order by b.id";
	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
		}
	}

    $sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	if($db_type==0)
	{
	$sql= sql_select("select style_id,style_des,sample_type,body_part,color_type_id,construction,composition,gsm_weight,gmts_color ,fabric_color,gmts_size,item_size,dia_width,finish_fabric,process_loss,grey_fabric,rate,amount,id,lib_yarn_count_deter_id as lib_yarn_count_deter_id,yarn_details,remarks  FROM wo_non_ord_samp_booking_dtls  WHERE booking_no =$txt_booking_no  and status_active=1 and	is_deleted=0 order by id");
	}
	if($db_type==2)
	{
	$sql= sql_select("select style_id,style_des,sample_type,body_part,color_type_id,construction,composition,gsm_weight,gmts_color ,fabric_color,gmts_size,item_size,dia_width,finish_fabric,process_loss,grey_fabric,rate,amount,id,lib_yarn_count_deter_id as lib_yarn_count_deter_id,yarn_details,remarks  FROM wo_non_ord_samp_booking_dtls  WHERE booking_no =$txt_booking_no  and status_active=1 and	is_deleted=0 order by id");
	}
?>
    <table class="rpt_table" width="100%"  border="2" cellpadding="2" cellspacing="0" rules="all">
        <thead>
            <tr style="border-top:3px solid black;">
                <th width="30">Sl</th>
                <th width="70">Style</th>
                <th width="100">Style Des</th>
                <th width="100">Sample</th>
                <th width="130">Body Part</th>
                <th width="80">Color Type</th>
                <th width="80">Construction</th>
                <th width="100">Composition & Yarn</th>

                <th width="40">GSM</th>

                <th width="80">Fab.Color</th>
                <th width="80">Item Size</th>

                <th width="50">Dia/ Width</th>
                <th width="60">Gray Qnty</th>
                <th width="60">Fin Fab Qnty</th>
               <th width="60">Process Loss</th>


                <th width="40">UOM</th>
                <th>Remarks</th>

                <?
                if($fabric_source !=1)
                {
                ?>
                <th width="60">Rate</th>

                <th width="70">Amount</th>
                <?
                }
                ?>
            </tr>
        </thead>
        <tbody>
		<?
        $style_id="";
        $total_finish_fabric=0;
        $total_grey_fabric=0;
        $toatl_rate=0;
        $total_amount=0;

        $i=1;
        foreach ($sql as $row)
        {
            $style_id.=$row[csf('style_id')].",";
            if($row[csf('style_id')])$style_sting.=$style_library[$row[csf('style_id')]].'_';
			?>
			<tr>
                <td align="center"><? echo $i; ?></td>
                <td><? echo $style_library[$row[csf('style_id')]]; ?></td>
                <td><? echo $row[csf('style_des')]; ?></td>
                <td><? echo $sample_library[$row[csf('sample_type')]]; ?></td>
                <td><? echo $body_part[$row[csf('body_part')]]; ?></td>
                <td><? echo $color_type[$row[csf('color_type_id')]]; ?></td>
                <td><? echo $row[csf('construction')]; ?></td>
                <td><? echo $composition_arr[$row[csf('lib_yarn_count_deter_id')]]; ?></td>

                <td><? echo $row[csf('gsm_weight')]; ?></td>



                <td><? echo $color_library[$row[csf('fabric_color')]];//$row[csf('dia_width')];$row[csf('grey_fabric')] ?></td>
                <td><? echo $row[csf('item_size')];//$row[csf('dia_width')];$row[csf('grey_fabric')] ?></td>
                <td align="right"><? echo $row[csf('dia_width')];//  ?></td>
                <td align="right"><? echo number_format($row[csf('grey_fabric')],2); $total_grey_fabric+=$row[csf('grey_fabric')];//number_format($row[csf('process_loss')],2); ?></td>
                <td align="right"><? echo number_format($row[csf('finish_fabric')],2);$total_finish_fabric+=$row[csf('finish_fabric')];//process_loss  ?></td>

              <td align="right"><? echo number_format($row[csf('process_loss')],2);?></td>
                <td align="center"><? if(str_replace("'","",$cbo_fabric_natu)==2){echo "KG";}if(str_replace("'","",$cbo_fabric_natu)==3){echo "Yds";};?></td>
                <td><? echo $row[csf('remarks')];?></td>
                <?
                if($fabric_source !=1)
                {
                    ?>
                    <td align="right"><? echo number_format($row[csf('rate')],2); $toatl_rate+=$row[csf('rate')]; ?></td>
                    <td align="right"><? echo number_format($row[csf('amount')],2); $total_amount+=$row[csf('amount')];?></td>
                    <?
                }
                ?>
			</tr>
			<?
			$tot_process_loss+=$row[csf('process_loss')];
			$i++;
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="12" align="right">Total </th>
                <th align="right" ><? echo number_format($total_grey_fabric,2);  ?></th>
                <th align="right" ><? echo number_format($total_finish_fabric,2); ?></th>
                <th align="right"><? //echo number_format($tot_process_loss,2); ?></th>
                <th align="right"><? //echo number_format($total_grey_fabric,4);  ?></th>
                <th align="right"><? //echo number_format($total_grey_fabric,4);  ?></th>
                <?
                if($fabric_source !=1)
                {
					?>
					<th align="right"><? echo number_format($toatl_rate,2);?></th>
					<th align="right"><? echo number_format($total_amount,2); ?></th>
					<?
                }
                ?>
            </tr>
        </tfoot>
    </table>
 <br/>
        <?
		$sql_img=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='sample_booking_non' and master_tble_id=$txt_booking_no and file_type=1");
		?>
        <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
        <caption align="center"><b>View Photo</b></caption>
        <tr>
        <td colspan="8" height="100">
       <?
        foreach($sql_img as $img)
		{
		?>
        	<img  src='../../<? echo $img[csf('image_location')]; ?>' height='90px' width='90px' /> &nbsp;&nbsp;
        <?
		}
		?>
        </td>
        </tr>
        </table>
        <br/>


        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="display:none">
            <tr>
                <td width="100%">
                    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    <tr>
                        	<th width="30" colspan="8" align="left">Sample Info</th>

                        </tr>
                    	<tr>
                        	<th width="30">Sl</th>
                            <th width="100">Style</th>
                            <th width="100">Sample Name</th>
                            <th width="80">Color</th>
                            <th width="80">Size</th>
                            <th width="80">Qty( Pcs)</th>
                            <th width="80">Rate (Pcs)</th>
                            <th width="80">Currency</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$lib_sample_name=return_library_array("select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name");
							 $sql= "select a.style_ref_no,b.sample_name,b.sample_color,b.sample_charge,b.sample_curency,c.size_id,c.size_qty from sample_development_mst a, sample_development_dtls b,sample_development_size c where a.id=b.sample_mst_id and a.id=c.mst_id and b.id=c.dtls_id  and a.id in(".rtrim($style_id,",").")   and a.is_deleted=0 and b.is_deleted=0 order by a.id";

                    $totqty=0;
					$totcharge=0;
					$data_array=sql_select($sql);// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							$totqty+=$row[csf('size_qty')];
							$totcharge+=$row[csf('sample_charge')];
							?>
                            	<tr id="settr_1" align="">
                                    <td width="30"><? echo $i ?></td>
                                    <td width="100"><? echo $row[csf('style_ref_no')] ?></td>
                                    <td width="100"><? echo $lib_sample_name[$row[csf('sample_name')]] ?></td>
                                    <td width="80"><? echo $color_library[$row[csf('sample_color')]] ?></td>
                                    <td width="80"><? echo $size_library[$row[csf('size_id')]] ?></td>
                                    <td width="80" align="right"><? echo $row[csf('size_qty')] ?></td>
                                    <td width="80" align="right"><? echo $row[csf('sample_charge')] ?></td>
                                    <td width="80"><? echo $currency[$row[csf('sample_curency')]] ?></td>
                                </tr>
                            <?
						}
					}
					?>
                    <tr id="settr_1" align="">
                                    <td width="30" colspan="5">Total:</td>

                                    <td width="80" align="right"><? echo $totqty; ?></td>
                                    <td width="80" align="right"><? echo $totcharge; ?></td>
                                    <td width="80"></td>
                                </tr>
                </tbody>
                </table>
                </td>

            </tr>
        </table>
        <br/>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="100%">
                    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th width="3%">Sl</th><th width="97%" align="left">Spacial Instruction</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=90 and booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>
                                </tr>
                            <?
						}
					}
					/*else
					{
						$i=0;
					$data_array=sql_select("select id, terms from  lib_terms_condition");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>

                                </tr>
                    <?
						}
					} */
					?>
                </tbody>
                </table>
                </td>

            </tr>
        </table>
          <?
		 	echo signature_table(6, $cbo_company_name, "1330px");
			echo "****".custom_file_name($varcode_booking_no,$style_sting,'');
		  ?>
       </div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
        </script>
       <?

}

if($action=="show_fabric_booking_report5") //For Metro
{
	extract($_REQUEST);

	$cbo_company_name=str_replace("'","",$cbo_company_name);

	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	if ($db_type == 0) $select_group_row = " order by master_tble_id desc limit 0,3";
	else if ($db_type == 2) $select_group_row = " and  rownum<=4 order by id desc";//order by id desc limit 0,1
	$imge_arr_for_book=sql_select( "select master_tble_id,image_location,real_file_name from   common_photo_library where  master_tble_id=$txt_booking_no and file_type=1  $select_group_row ");

	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$com_supplier_id=return_field_value( "supplier_id as supplier_id", "wo_non_ord_samp_booking_mst","booking_no=$txt_booking_no","supplier_id");

	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no, a.is_approved from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no=$txt_booking_no and b.entry_form=9 group by a.is_approved");
	list($nameArray_approved_row)=$nameArray_approved;
	$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no=$txt_booking_no and b.entry_form=9 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
	list($nameArray_approved_date_row)=$nameArray_approved_date;
	 $fabric_source_new=array(1=>"Production",2=>"Purchase",3=>"Buyer Supplied",4=>"Stock");
	?>
	<div style="width:1330px; font-family:'Arial Narrow';font-style: normal;font-variant: normal;font-weight: 400;
	line-height: 20px;" align="center">
    										<!--    Header Company Information         -->
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100">
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:24px;">
                            <strong>
                              <?php
							echo $company_library[$cbo_company_name];
							?>
                              </strong>

						   </td>

                            <td rowspan="3" width="">
							<?
                             if($nameArray_approved_row[csf('approved_no')]>1)
                             {
                             ?>
                             <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                              <br/>
                              Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
                              <?
                             }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
                            ?>
                                           <? echo $result[csf('plot_no')].'&nbsp;'; ?>
                                            <? echo $result[csf('level_no')].'&nbsp;' ?>
                                            <? echo $result[csf('road_no')].'&nbsp;'; ?>
                                            <? echo $result[csf('block_no')].'&nbsp;';?>
                                            <? echo $result[csf('city')].'&nbsp;';?>
                                            <? echo $result[csf('zip_code')].'&nbsp;'; ?>
                                             <?php echo $result[csf('province')] . '&nbsp;'; ?>
                                           <? echo $country_arr[$result[csf('country_id')]].'&nbsp;'; ?><br>
                                            <? echo $result[csf('email')];?>
                                             <? echo $result[csf('website')];
                            }
                                            ?>

                               </td>
                            </tr>
                            <tr>

                            <td align="center" style="font-size:20px;"> <strong style="margin-left:77px;"><? if($report_title !=""){echo $report_title;} else {echo "Sample Fabric Booking -Without order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="r:#F00"><? if($nameArray_approved_row[csf('is_approved')] ==1){ echo "(Approved)";} else if ($nameArray_approved_row[csf('is_approved')] ==3){echo "Partial Approved";} else{echo "";}; ?> </font></strong>
                             <div style="margin-left:854px; margin-top:-75px; position: absolute; float:right;  ">
	                            <?
								foreach($imge_arr_for_book as $row)
								{
									?>
	                                <img  src='../../<? echo $row[csf('image_location')]; ?>' height='80' width='80' />
									<?
								}

								?>
                            </div>
                             </td>

                              <td>
                              <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <strong> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
                                  <br/>

								  <?
								 }
							  	?>

                             </td>

                            </tr>
                      </table>

                </td>
                <td width="250" id="barcode_img_id">

                </td>
            </tr>
       </table>

                <?
				$season="";
				$buyer_req_no="";$bh_merchant="";$style_ref_no="";$product_code="";$product_department="";

				$nameseason=sql_select( "select a.season_buyer_wise, b.buyer_req_no,a.bh_merchant,a.style_ref_no,a.product_code,a.product_dept  from  sample_development_mst a, sample_development_dtls b, wo_non_ord_samp_booking_dtls c  where  a.id=b. sample_mst_id and a.id=c.style_id and c.booking_no=$txt_booking_no and b.status_active=1 and   b.is_deleted=0 and c.status_active=1 and   c.is_deleted=0 ");
				foreach ($nameseason as $season_row)
				{
					$season=$season_row[csf('season_buyer_wise')];
					$buyer_req_no=$season_row[csf('buyer_req_no')];
					$bh_merchant=$season_row[csf('bh_merchant')];
					$style_ref_no=$season_row[csf('style_ref_no')];
					$product_code=$season_row[csf('product_code')];
					$product_department=$product_dept[$season_row[csf('product_dept')]];

				}

				$fabric_source='';
				$season_library=return_library_array( "select id,season_name from lib_buyer_season", "id", "season_name");
                $nameArray=sql_select( "select buyer_id,fabric_source,booking_no,pay_mode,booking_date,supplier_id,currency_id,exchange_rate,attention,delivery_date,fabric_source,team_leader,	dealing_marchant from wo_non_ord_samp_booking_mst   where  booking_no=$txt_booking_no");
				foreach ($nameArray as $result)
				{
					$fabric_source_id=$result[csf('fabric_source')];
					$fabric_source=$result[csf('fabric_source')];
					$varcode_booking_no=$result[csf('booking_no')];
				?>
       <table width="100%" style="border:1px solid black">
            <tr>
                <td colspan="6" valign="top"></td>
            </tr>
            <tr>
                <td width="100" style="font-size:18px"><b>Booking No</b></td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>

                <td width="100"><span style="font-size:12px"><b>MKDL NO</b></span></td>
                <td width="110">:&nbsp;<? echo $style_ref_no;?></td>


            </tr>
            <tr>

                <td width="100"><span style="font-size:12px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
                <td width="100" style="font-size:18px"><b>Supplier Name</b>   </td>
                <td width="110" style="font-size:18px">:&nbsp; <b><?
					if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?></b></td>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;

				<?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
				$comAdd=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$result[csf('supplier_id')]);
					foreach ($comAdd as $comAddRow){
						echo $comAddRow[csf('plot_no')].'&nbsp;';
						echo $comAddRow[csf('level_no')].'&nbsp;' ;
						echo $comAddRow[csf('road_no')].'&nbsp;';
						echo $comAddRow[csf('block_no')].'&nbsp;';
						echo $comAddRow[csf('city')].'&nbsp;';
						//echo $comAddRow[csf('zip_code')].'&nbsp;';
						//echo $comAddRow[csf('province')].'&nbsp;';
						//echo $country_arr[$comAddRow[csf('country_id')]].'&nbsp;';
						//echo $comAddRow[csf('email')];
						//echo $comAddRow[csf('website')];
					}
				}
				else{
					echo $supplier_address_arr[$result[csf('supplier_id')]];
				}
				?>
                </td>
                <td width="100" style="font-size:12px"><b>Department Name</b></td>
               	<td width="110">:&nbsp;<? echo $product_department;?></td>
            </tr>
             <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Department No</b></td>
                <td  width="110" >:&nbsp;<? echo $product_code; ?></td>

            </tr>

            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $season_library[$season]; ?></td>
                <td  width="100" style="font-size:12px"><b>Buyer Req. No</b></td>
                <td  width="110" >:&nbsp;<? echo $buyer_req_no; ?></td>
                <td  width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td  width="110" >:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Buying Merchant Name
	</b></td>
                <td  width="110" >:&nbsp;<? echo $bh_merchant; ?></td>

            </tr>
             <tr>
                <td width="100" style="font-size:12px"><b>Fabric Source</b></td>
                 <td width="110">:&nbsp;<? echo $fabric_source_new[$fabric_source_id]; ?></td>
                </tr>
        </table>
        <?
			}
		?>

      <br/>

      <?
	  $composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id";
	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
		}
	}

 $sub_date_arr=return_library_array( "select id,buyer_dead_line from sample_development_dtls", "id", "buyer_dead_line"  );
    $sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	$item_library=return_library_array( "select id,item_name from lib_item_group", "id", "item_name"  );
	if($db_type==0)
	{
		$sql= sql_select("select style_id, style_des, sample_type, body_part, color_type_id, construction, composition, yarn_details, gsm_weight, gmts_color, fabric_color, gmts_size, item_size, dia_width, finish_fabric, uom, process_loss, grey_fabric, rate, amount, id, lib_yarn_count_deter_id as lib_yarn_count_deter_id, yarn_details, remarks, fabric_source, delivery_date, dtls_id FROM wo_non_ord_samp_booking_dtls  WHERE booking_no =$txt_booking_no  and status_active=1 and	is_deleted=0 order by style_des, sample_type, gmts_color, dtls_id");
	}
	else if($db_type==2)
	{
		$sql= sql_select("select style_id, style_des, sample_type, body_part, color_type_id, construction, yarn_details, composition, gsm_weight, gmts_color, fabric_color, gmts_size, item_size, dia_width, finish_fabric, uom, process_loss, grey_fabric, rate, amount, id, lib_yarn_count_deter_id as lib_yarn_count_deter_id, yarn_details, remarks, fabric_source, delivery_date, dtls_id FROM wo_non_ord_samp_booking_dtls WHERE booking_no =$txt_booking_no and status_active=1 and	is_deleted=0 order by style_des, sample_type, gmts_color, dtls_id");
	}

	foreach ($sql as $row)
	{
		$dataArr[]=$row;
		$key=$row[csf("style_des")].$row[csf("sample_type")].$row[csf("gmts_color")].$row[csf("dtls_id")];
		$gData[$key]+=1;
	}


	?>
    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr>
                <th width="30">Sl</th>

                <th width="90">Style Des</th>
                <th width="100">Sample</th>
                 <th width="80"> Gmts Color</th>

                 <th width="80"> Smpl Sub. Date</th>
                 <th width="80"> Fab Del Date.</th>
                <th width="120">Body Part</th>
                <th width="200">Fabric Details and Composition</th>
                 <th width="70">Color Type</th>

                <th width="80">Fab.Color</th>

                <th width="40">Item Size</th>

                <th width="50">Dia/ Width</th>
                <th width="60">Gray Qnty</th>
                 <th width="40">P. Loss</th>
                <th width="60">Fin Fab Qnty</th>



                <th width="40">UOM</th>
                <th width="60">Rate</th>
                <th width="70">Amount</th>
                <th width="60">Fabric Source</th>
                <th>Remarks</th>




            </tr>
        </thead>
        <tbody>
		<?
        $style_id="";
        $total_finish_fabric=0;
        $total_grey_fabric=0;
        $toatl_rate=0;
        $total_amount=0;


        $i=1;
		$r=1;
        foreach ($dataArr as $row)
        {
            $style_id.=$row[csf('style_id')].",";
            if($row[csf('style_id')])$style_sting.=$style_library[$row[csf('style_id')]].'_';
			$fab_detail=$row[csf('construction')].','.$composition_arr[$row[csf('lib_yarn_count_deter_id')]].','."GSM:".$row[csf('gsm_weight')].','.'<i>'.$row[csf('yarn_details')].'</i>';
			?>
			<tr>
			  <?
                $groupData=$row[csf("style_des")].$row[csf("sample_type")].$row[csf("gmts_color")].$row[csf("dtls_id")];
				if(!in_array($groupData,$date_array))
				{

					?>
                <td  align="center" rowspan="<? echo $gData[$groupData];?>"><? echo $r; ?></td>
                <td rowspan="<? echo $gData[$groupData];?>" ><? echo $row[csf('style_des')]; ?></td>
                <td rowspan="<? echo $gData[$groupData];?>" title="<? echo $row[csf('sample_type')]; ?>" ><? echo $sample_library[$row[csf('sample_type')]]; ?></td>
                <td rowspan="<? echo $gData[$groupData];?>" ><? echo $color_library[$row[csf('gmts_color')]]; ?></td>

                <td  rowspan="<? echo $gData[$groupData];?>" ><? echo change_date_format($sub_date_arr[$row[csf('dtls_id')]]);?></td>
                <td rowspan="<? echo $gData[$groupData];?>" ><? $date=explode(' ',$row[csf('delivery_date')]);echo change_date_format($date[0]); ?></td>
              <?
			  	$date_array[]=$groupData;
                $r++;
               	 }
				 ?>
                <td><? echo $body_part[$row[csf('body_part')]]; ?></td>
                <td><? echo $fab_detail; ?></td>
               <td><? echo $color_type[$row[csf('color_type_id')]]; ?></td>
                <td><? echo $color_library[$row[csf('fabric_color')]];//$row[csf('dia_width')];$row[csf('grey_fabric')] ?></td>
                <td><? echo $row[csf('item_size')];//$row[csf('dia_width')];$row[csf('grey_fabric')] ?></td>
                <td align="right"><? echo $row[csf('dia_width')];//  ?></td>
                <td align="right"><? echo number_format($row[csf('grey_fabric')],2); $total_grey_fabric+=$row[csf('grey_fabric')];//number_format($row[csf('process_loss')],2); ?></td>
                <td align="right"><? echo number_format($row[csf('process_loss')],2);?></td>
                <td align="right"><? echo number_format($row[csf('finish_fabric')],2);$total_finish_fabric+=$row[csf('finish_fabric')];//process_loss  ?></td>


                <td align="center"><? echo $unit_of_measurement[$row[csf('uom')]];//if(str_replace("'","",$cbo_fabric_natu)==2){echo "KG";}if(str_replace("'","",$cbo_fabric_natu)==3){echo "Yds";};?></td>
                <td align="right"><? echo number_format($row[csf('rate')],2); $toatl_rate+=$row[csf('rate')]; ?></td>
                <td align="right"><? echo number_format($row[csf('amount')],2); $total_amount+=$row[csf('amount')];?></td>
                 <td><? echo $fabric_source_new[$row[csf('fabric_source')]];?></td>
                <td><? echo $row[csf('remarks')];?></td>



			</tr>
			<?
			$tot_process_loss+=$row[csf('process_loss')];
			$i++;
        }
        ?>
        </tbody>

            <tr>
                <th colspan="12" align="right">Total </th>
                <th align="right"><? echo number_format($total_grey_fabric,2);  ?></th>

                <th align="right"><? //echo number_format($tot_process_loss,2); ?></th>
                 <th align="right"><? echo number_format($total_finish_fabric,2); ?></th>
                <th align="right"><? //echo number_format($total_grey_fabric,4);  ?></th>
                <th align="right"><? echo number_format($toatl_rate,2);?></th>
				<th align="right"><? echo number_format($total_amount,2); ?></th>
                <th align="right"><? //echo number_format($total_grey_fabric,4);  ?></th>
                <th align="right"></th>




            </tr>

    </table>
    <br/>
    <div style="width:1330px; float:left">

    <table align="left" class="rpt_table" width="1330"  border="1" cellpadding="0" cellspacing="0" rules="all">

        <thead align="center">
           <tr>
           		 <th align="left" colspan="6"><strong>Accessoris Requirement</strong></th>
           </tr>
               <tr>
                <th width="30">Sl</th>
                <th width="250">Item</th>
                <th width="300">Desc.</th>
                <th width="80">Qnty</th>
                <th width="80">UOM</th>
                <th width="">Remarks</th>
         </tr>

       </thead>
       </table>
       <table class="rpt_table" width="1330"  border="1" cellpadding="0" cellspacing="0" rules="all">
        <tbody>
            <?
       $k=1;$total_acc_qty=0;
		 $sql_acces="select id,booking_no,item_group_id,description,uom,qty,remarks from wo_non_ord_booking_acc_dtls where booking_no=$txt_booking_no and description is not null";
		$sql_result= sql_select( $sql_acces);
		 foreach($sql_result as $row)
		 {
	   ?>
           <tr>
                <td width="30" align="center"><? echo $k; ?></td>
                <td width="250"><? echo $item_library[$row[csf('item_group_id')]]; ?></td>
                <td width="300"><? echo $row[csf('description')]; ?></td>
                <td width="80" align="right"><? echo $row[csf('qty')]; ?></td>
                <td width="80" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                <td width=""><? echo $row[csf('remarks')]; ?></td>

        </tr>
        <?
		$k++;
		$total_acc_qty+=$row[csf('qty')];
		 }
		?>
         </tbody>


 </table>
 </div>
 <br/>
 <table align="left" class="rpt_table" width="100%"  border="0" cellpadding="2" cellspacing="0" rules="all">
 <tr>
 <td>&nbsp; </td>
 </tr>
 </table>
 <?
  /*  $sql_req=("select gmts_color,gmts_size,sum(bh_qty) as bh_qty,sum(rf_qty) as rf_qty  FROM wo_non_ord_samp_booking_dtls  WHERE booking_no=$txt_booking_no  and status_active=1 and is_deleted=0  and bh_qty>0 group by gmts_color,gmts_size  order by gmts_size");
	$sql_data =sql_select($sql_req);
	$size_array=array();$qnty_array_bh=array();$qnty_array_rf=array();
	foreach($sql_data as $row)
	{
		$size_array[$row[csf('gmts_size')]]=$row[csf('gmts_size')];
		$qnty_array_bh[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('bh_qty')];
		$qnty_array_rf[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('rf_qty')];
		$qnty_array[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('bh_qty')]+$row[csf('rf_qty')];
	}*/
	$size_array=array(); $qty_array=array(); $sample_arr=array();

	//$sql_req=sql_select("select a.booking_no,a.dtls_id as dtls_id,b.sample_color,c.size_id,c.size_qty as tsizeqty,c.bh_qty as tbh_qty,b.sample_name from wo_non_ord_samp_booking_dtls a, sample_development_dtls b, sample_development_size c where a.style_id=b.sample_mst_id and a.booking_no=$txt_booking_no and  b.id=c.dtls_id");
	$sql_req=sql_select("select a.booking_no,a.dtls_id as dtls_id,b.sample_color,c.size_id,c.size_qty as tsizeqty,c.bh_qty as tbh_qty,b.sample_name from wo_non_ord_samp_booking_dtls a, sample_development_dtls b, sample_development_size c where a.style_id=b.sample_mst_id and a.booking_no=$txt_booking_no and  b.id=c.dtls_id and a.gmts_color=b.sample_color and a.status_active=1 and a.is_deleted=0");
	foreach( $sql_req  as $row)
	{
		$key=$row[csf('sample_color')].'__'.$row[csf('sample_name')];
		$qty_array[$key][$row[csf('size_id')]]['size_qty']=$row[csf('tsizeqty')];
		$qty_array[$key][$row[csf('size_id')]]['bh_qty']=$row[csf('tbh_qty')];
		//$sample_arr[$row[csf('sample_name')]]=$row[csf('sample_name')];
		$size_array[$row[csf('size_id')]]=$row[csf('size_id')];
		//echo $size_array[$row[csf('id')]][$row[csf('dtls_id')]]['size_id']."_";
	}

	//print_r($sample_arr);
	 $sql_data_color=sql_select("select gmts_color,sum(bh_qty) as bh_qty,sample_type FROM wo_non_ord_samp_booking_dtls  WHERE booking_no=$txt_booking_no  and status_active=1 and is_deleted=0 and bh_qty>0  group by gmts_color,sample_type  order by sample_type");

	//$sql_data_color=sql_select("select sample_type,gmts_color,bh_qty,rf_qty  FROM wo_non_ord_samp_booking_dtls  WHERE booking_no=$txt_booking_no and status_active=1 and is_deleted=0 and bh_qty>0 group by gmts_color,sample_type,bh_qty,rf_qty  order by gmts_color,sample_type");
	//$sql_data_color=sql_select("select gmts_color,(bh_qty) as bh_qty,sample_type  FROM wo_non_ord_samp_booking_dtls  WHERE booking_no=$txt_booking_no  and status_active=1 and is_deleted=0 and bh_qty>0  order by gmts_color,sample_type");

	$color_array=array();
	$sample_arr=array();
	foreach($sql_data_color as $row)
	{
		$key=$row[csf('gmts_color')].'__'.$row[csf('sample_type')];
		$color_array[$key]=$row[csf('gmts_color')];
		$sample_arr[$key]=$row[csf('sample_type')];
		$sampleSpan[$row[csf('sample_type')]]+=1;
	}
	 $sizearr=return_library_array("select id,size_name from lib_size where status_active =1 and is_deleted=0","id","size_name");
	 $colorarr=return_library_array("select id,color_name from  lib_color where status_active =1 and is_deleted=0","id","color_name");
	 $width=400+(count($size_array)*150);

	//print_r($sample_arr);
	 //count($size_array);
	 ?>
 	<table align="left" cellspacing="0" width="<? echo $width?>"  border="1" rules="all" class="rpt_table" >
  	<caption> <strong>Sample Requirement</strong></caption>
        <thead  align="center">
            <tr>
                <th width="30" rowspan="2">SL</th>
                <th width="80" rowspan="2" align="center">Sample</th>
                <th width="80" rowspan="2" align="center">Color/Size</th>
                <?
                //$size_name=array_filter(array_unique(explode('__',$size_array)));
                foreach ($size_array as $sizid)
                {
                //$size_count=count($sizid);
                ?>
                <th width="" colspan="2"><strong><? echo  $sizearr[$sizid];  ?></strong></th>

                <?
                } ?>
               <th width="80" rowspan="2" align="center">Total Qnty.</th>
           </tr>

            <tr>
             <?
            foreach ($size_array as $sizid)
            {
            //$size_count=count($sizid);
            ?>
            <th width="75"> BH &nbsp;</th> <th width="75"> Rf.&nbsp;</th>
            <?
            } ?>
            </tr>
        </thead>



        <tbody>
			<?
            $i=1;
            $tot_qnty=array();
                foreach($color_array as $key=>$cid)
                {
                   $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$color_count=count($cid);
					$j++;
					$rowSpan=$sampleSpan[$sample_arr[$key]];
                    ?>

                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <? if($tempData[$sample_arr[$key]]!=1){ ?>
                        	<td rowspan="<? echo $rowSpan;?>"><? echo $j;?></td>
                            <td rowspan="<? echo $rowSpan;?>"><? echo $sample_library[$sample_arr[$key]]; ?></td>
                        <?
							$tempData[$sample_arr[$key]]=1;
						}
						?>
                        <td><? echo $colorarr[$cid]; ?></td>
		                <?
						foreach ($size_array as $sizval)
						{
						$tot_qnty[$key]+=$qty_array[$key][$sizval]['size_qty'];
						$qty_array[$sizval]['bh_qty']+=$qty_array[$key][$sizval]['bh_qty'];
						$qty_array[$sizval]['size_qty']+=$qty_array[$key][$sizval]['size_qty'];
						?>
						<td width="75" align="right"> <? echo $qty_array[$key][$sizval]['bh_qty']; ?></td>
                        <td width="75" align="right"> <? $rf=$qty_array[$key][$sizval]['size_qty']-$qty_array[$key][$sizval]['bh_qty']; if ($rf==0){ echo "";} else { echo $rf;}?></td>
						<?
						}
						?>
                        <td align="right"><? echo $tot_qnty[$key]; ?></td>
                    </tr>
                    <?
					$production_quantity+=$tot_qnty[$key];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="3" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $tot_qnty_size_bh[$sizval]; ?></td>
                    <td align="right"><?php echo $tot_qnty_size_rf[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $production_quantity; ?></td>
        </tr>
    </table>
          <table align="left" class="rpt_table" width="1300"  border="0" cellpadding="0" cellspacing="0" rules="all">
           <tr>
           <td colspan="15">&nbsp;  </td>
           </tr>
          </table>
		  <br>
         <?

		 	$lib_designation=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");


			// $data_array=sql_select("select b.id,b.approved_by,b.approved_no, b.approved_date,b.un_approved_reason, c.user_full_name,c.designation,d.approval_cause from wo_booking_mst a join approval_history b on a.id=b.mst_id join  user_passwd c on b.approved_by=c.id left join fabric_booking_approval_cause d on b.id =d.approval_history_id  where a.booking_no=$txt_booking_no and b.entry_form=90 order by b.id asc");

			$data_array=sql_select("select b.id,b.approved_by,b.approved_no, b.approved_date,b.un_approved_reason, c.user_full_name,c.designation,d.approval_cause  from wo_non_ord_samp_booking_mst a, approval_history b join  user_passwd c on b.approved_by=c.id left join fabric_booking_approval_cause d on b.id =d.approval_history_id where a.id=b.mst_id and a.booking_no=$txt_booking_no and b.entry_form=9");


 	?>
       <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="3" style="border:1px solid black;">Approval Status</th>
                </tr>
					<tr style="border:1px solid black;">
					<th width="3%" style="border:1px solid black;">Sl</th>
					<th width="25%" style="border:1px solid black;">Name/Designation</th>
					<th width="27%" style="border:1px solid black;">Approval Date</th>
					<th width="15%" style="border:1px solid black;">Approval No</th>
					<th width="30%" style="border:1px solid black;">Un Approval Cause</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($data_array as $row){
			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
                <td width="25%" style="border:1px solid black;"><? echo $row[csf('user_full_name')]." / ". $lib_designation[$row[csf('designation')]];?></td>
                <td width="27%" style="border:1px solid black;"><? echo date("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>
                <td width="15%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
                <td width="15%" style="border:1px solid black;"><? echo $row[csf('approval_cause')];?></td>
                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
        <br/>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="100%">
                    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th width="3%">Sl</th><th width="97%" align="left">Spacial Instruction</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no and entry_form=90");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>
                                </tr>
                            <?
						}
					}
					/*else
					{
						$i=0;
					$data_array=sql_select("select id, terms from  lib_terms_condition");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>

                                </tr>
                    <?
						}
					} */
					?>
                </tbody>
                </table>
                </td>

            </tr>
        </table>
          <?
		 	echo signature_table(6, $cbo_company_name, "1330px");
			echo "****".custom_file_name($varcode_booking_no,$style_sting,'');
		  ?>
       </div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
        </script>
       <?

}

if($action=="show_fabric_booking_report6") //For FFL
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no, a.is_approved from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no='$txt_booking_no' and b.entry_form=9 group by a.is_approved");
	list($nameArray_approved_row)=$nameArray_approved;
	$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no='$txt_booking_no' and b.entry_form=9 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
	list($nameArray_approved_date_row)=$nameArray_approved_date;

	?>
	<div style="width:1330px; font-family:'Arial Narrow';font-style: normal;font-variant: normal;font-weight: 400;
	line-height: 20px;" align="center">
    										<!--    Header Company Information         -->
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100">
               	<?
               	if(isset($img_source)){
               		$image_source = $img_source;
               	}else{
               		$image_source = "../../";
               	}
               	?>
               <img  src='<? echo $image_source.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:24px;">
                            <strong>
                              <?php
echo $company_library[$cbo_company_name];
?>
                              </strong>
                            </td>
                            <td rowspan="3" width="">
							<?
                             if($nameArray_approved_row[csf('approved_no')]>1)
                             {
                             ?>
                             <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                              <br/>
                              Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
                              <?
                             }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id='$cbo_company_name'");
                            foreach ($nameArray as $result)
                            {
                            ?>
                                           <? echo $result[csf('plot_no')].'&nbsp;'; ?>
                                            <? echo $result[csf('level_no')].'&nbsp;' ?>
                                            <? echo $result[csf('road_no')].'&nbsp;'; ?>
                                            <? echo $result[csf('block_no')].'&nbsp;';?>
                                            <? echo $result[csf('city')].'&nbsp;';?>
                                            <? echo $result[csf('zip_code')].'&nbsp;'; ?>
                                             <?php echo $result[csf('province')] . '&nbsp;'; ?>
                                           <? echo $country_arr[$result[csf('country_id')]].'&nbsp;'; ?><br>
                                            <? echo $result[csf('email')];?>
                                             <? echo $result[csf('website')];
                            }
                                            ?>

                               </td>
                            </tr>
                            <tr>

                            <td align="center" style="font-size:20px"> <strong><? if($report_title !=""){echo $report_title;} else {echo "Sample Fabric Booking -Without order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="r:#F00"><? if($nameArray_approved_row[csf('is_approved')] ==1) { echo "(Approved)";} else if($nameArray_approved_row[csf('is_approved')] ==3) { echo "(Partial Approved)";}else{echo "";}; ?> </font></strong><!--//ISD-22-05730 by kausar-->
                             </td>

                              <td>
                              <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <strong> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
                                  <br/>

								  <?
								 }
							  	?>
                             </td>

                            </tr>
                      </table>
                </td>
                <td width="250" id="barcode_img_id">

                </td>
            </tr>
       </table>

                <?
				$season="";
				$buyer_req_no="";$bh_merchant="";$style_ref_no="";$product_code="";$product_department="";

				$nameseason=sql_select( "select a.season_buyer_wise, b.buyer_req_no,a.bh_merchant,a.style_ref_no,a.product_code,a.product_dept  from  sample_development_mst a, sample_development_dtls b, wo_non_ord_samp_booking_dtls c  where  a.id=b. sample_mst_id and a.id=c.style_id and c.booking_no='$txt_booking_no'");
				foreach ($nameseason as $season_row)
				{
					$season=$season_row[csf('season_buyer_wise')];
					$buyer_req_no=$season_row[csf('buyer_req_no')];
					$bh_merchant=$season_row[csf('bh_merchant')];
					$style_ref_no=$season_row[csf('style_ref_no')];
					$product_code=$season_row[csf('product_code')];
					$product_department=$product_dept[$season_row[csf('product_dept')]];

				}

				$fabric_source='';
				$season_library=return_library_array( "select id,season_name from lib_buyer_season", "id", "season_name");
                $nameArray=sql_select( "select buyer_id,booking_no,pay_mode,booking_date,supplier_id,currency_id,exchange_rate,attention,delivery_date,fabric_source,team_leader,	dealing_marchant from wo_non_ord_samp_booking_mst   where  booking_no='$txt_booking_no'");
				foreach ($nameArray as $result)
				{
					$fabric_source=$result[csf('fabric_source')];

					$varcode_booking_no=$result[csf('booking_no')];

				?>
       <table width="100%" style="border:1px solid black">
            <tr>
                <td colspan="6" valign="top"></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>

                <td width="100"><span style="font-size:12px"><b>MKDL NO</b></span></td>
                <td width="110">:&nbsp;<? echo $style_ref_no;?></td>


            </tr>
            <tr>

                <td width="100"><span style="font-size:12px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<?
					if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;<?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
				$comAdd=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$result[csf('supplier_id')]);
					foreach ($comAdd as $comAddRow){
						echo $comAddRow[csf('plot_no')].'&nbsp;';
						echo $comAddRow[csf('level_no')].'&nbsp;' ;
						echo $comAddRow[csf('road_no')].'&nbsp;';
						echo $comAddRow[csf('block_no')].'&nbsp;';
						echo $comAddRow[csf('city')].'&nbsp;';
						//echo $comAddRow[csf('zip_code')].'&nbsp;';
						//echo $comAddRow[csf('province')].'&nbsp;';
						//echo $country_arr[$comAddRow[csf('country_id')]].'&nbsp;';
						//echo $comAddRow[csf('email')];
						//echo $comAddRow[csf('website')];
					}
				}
				else{
					echo $supplier_address_arr[$result[csf('supplier_id')]];
				}
				//echo $supplier_address_arr[$result[csf('supplier_id')]];

				?></td>
                <td width="100" style="font-size:12px"><b>Department Name</b></td>
               	<td width="110">:&nbsp;<? echo $product_department;?></td>
            </tr>
             <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Department No</b></td>
                <td  width="110" >:&nbsp;<? echo $product_code; ?></td>

            </tr>

            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $season_library[$season]; ?></td>
                <td  width="100" style="font-size:12px"><b>Buyer Req. No</b></td>
                <td  width="110" >:&nbsp;<? echo $buyer_req_no; ?></td>
                <td  width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td  width="110" >:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Buying Merchant Name
</b></td>
                <td  width="110" >:&nbsp;<? echo $bh_merchant; ?></td>

            </tr>
        </table>
        <?
			}
		?>

      <br/>

      <?
	  $composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 order by b.id";
	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
		}
	}

    $sub_date_arr=return_library_array( "select id,buyer_dead_line from sample_development_dtls", "id", "buyer_dead_line"  );
    $sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	$item_library=return_library_array( "select id,item_name from lib_item_group", "id", "item_name"  );
	if($db_type==0)
	{
	$sql= sql_select("select style_id,style_des,sample_type,body_part,color_type_id,construction,composition,yarn_details,gsm_weight,gmts_color ,fabric_color,gmts_size,item_size,dia_width,finish_fabric,process_loss,grey_fabric,rate,amount,id,lib_yarn_count_deter_id as lib_yarn_count_deter_id,yarn_details,remarks,fabric_source,dtls_id  from wo_non_ord_samp_booking_dtls where booking_no ='$txt_booking_no'  and status_active=1 and	is_deleted=0 and  style_id='' order by style_des,sample_type,gmts_color,dtls_id");
	}
	if($db_type==2)
	{


	$sql= sql_select("select style_id,style_des,sample_type,body_part,color_type_id,construction,yarn_details,composition,gsm_weight,gmts_color ,fabric_color,gmts_size,item_size,dia_width,finish_fabric,process_loss,grey_fabric,rate,amount,id,lib_yarn_count_deter_id as lib_yarn_count_deter_id,yarn_details,remarks,fabric_source,dtls_id  FROM wo_non_ord_samp_booking_dtls  WHERE booking_no ='$txt_booking_no'  and status_active=1 and	is_deleted=0 and  style_id is null order by style_des,sample_type,gmts_color,dtls_id");
	}

	foreach ($sql as $row)
	{
		$dataArr[]=$row;
		$key=$row[csf("style_des")].$row[csf("sample_type")].$row[csf("gmts_color")].$row[csf("dtls_id")];
		$gData[$key]+=1;
	}


?>
    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr>
                <th width="30">Sl</th>

                <th width="90">Style Des</th>
                <th width="100">Sample</th>
                 <th width="80"> Gmts Color</th>
                 <th width="80"> Sub. Date</th>
                <th width="120">Body Part</th>
                <th width="200">Fabric Details and Composition</th>
                 <th width="70">Color Type</th>

                <th width="80">Fab.Color</th>

                <th width="40">Item Size</th>

                <th width="50">Dia/ Width</th>
                <th width="60">Gray Qnty</th>
                 <th width="40">P. Loss</th>
                <th width="60">Fin Fab Qnty</th>



                <th width="40">UOM</th>
                <th width="60">Fabric Source</th>
                <th>Remarks</th>

                <?
                if($fabric_source ==2)
                {
                ?>
                <th width="60">Rate</th>
                <th width="70">Amount</th>
                <?
                }
                ?>
            </tr>
        </thead>
        <tbody>
		<?
        $style_id="";
        $total_finish_fabric=0;
        $total_grey_fabric=0;
        $toatl_rate=0;
        $total_amount=0;
        $fabric_source_new=array(1=>"Production",2=>"Purchase",3=>"Buyer Supplied",4=>"Stock");

        $i=1;
		$r=1;
        foreach ($dataArr as $row)
        {
            $style_id.=$row[csf('style_id')].",";
            if($row[csf('style_id')])$style_sting.=$style_library[$row[csf('style_id')]].'_';
			$fab_detail=$row[csf('construction')].','.$composition_arr[$row[csf('lib_yarn_count_deter_id')]].','."GSM:".$row[csf('gsm_weight')].','.'<i>'.$row[csf('yarn_details')].'</i>';
			?>
			<tr>
			  <?
                $groupData=$row[csf("style_des")].$row[csf("sample_type")].$row[csf("gmts_color")].$row[csf("dtls_id")];
				if(!in_array($groupData,$date_array))
				{

					?>
                <td  align="center" rowspan="<? echo $gData[$groupData];?>"><? echo $r; ?></td>
                <td rowspan="<? echo $gData[$groupData];?>" ><? echo $row[csf('style_des')]; ?></td>
                <td rowspan="<? echo $gData[$groupData];?>" title="<? echo $row[csf('sample_type')]; ?>" ><? echo $sample_library[$row[csf('sample_type')]]; ?></td>
                <td rowspan="<? echo $gData[$groupData];?>" ><? echo $color_library[$row[csf('gmts_color')]]; ?></td>
                <td  rowspan="<? echo $gData[$groupData];?>" ><? echo change_date_format($sub_date_arr[$row[csf('dtls_id')]]);?></td>
              <?
			  	$date_array[]=$groupData;
                $r++;
               	 }
				 ?>
                <td><? echo $body_part[$row[csf('body_part')]]; ?></td>
                <td><? echo $fab_detail; ?></td>
               <td><? echo $color_type[$row[csf('color_type_id')]]; ?></td>
                <td><? echo $color_library[$row[csf('fabric_color')]];//$row[csf('dia_width')];$row[csf('grey_fabric')] ?></td>
                <td><? echo $row[csf('item_size')];//$row[csf('dia_width')];$row[csf('grey_fabric')] ?></td>
                <td align="right"><? echo $row[csf('dia_width')];//  ?></td>
                <td align="right"><? echo number_format($row[csf('grey_fabric')],2); $total_grey_fabric+=$row[csf('grey_fabric')];//number_format($row[csf('process_loss')],2); ?></td>
                <td align="right"><? echo number_format($row[csf('process_loss')],2);?></td>
                <td align="right"><? echo number_format($row[csf('finish_fabric')],2);$total_finish_fabric+=$row[csf('finish_fabric')];//process_loss  ?></td>


                <td align="center"><? if(str_replace("'","",$cbo_fabric_natu)==2){echo "KG";}if(str_replace("'","",$cbo_fabric_natu)==3){echo "Yds";};?></td>
                 <td><? echo $fabric_source_new[$row[csf('fabric_source')]];?></td>
                <td><? echo $row[csf('remarks')];?></td>
                <?

				//echo $fabric_source;
                if($fabric_source ==2)
                {
                    ?>
                    <td align="right"><? echo number_format($row[csf('rate')],2); $toatl_rate+=$row[csf('rate')]; ?></td>
                    <td align="right"><? echo number_format($row[csf('amount')],2); $total_amount+=$row[csf('amount')];?></td>
                    <?
                }
                ?>
			</tr>
			<?
			$tot_process_loss+=$row[csf('process_loss')];
			$i++;
        }
        ?>
        </tbody>

            <tr>
                <th colspan="11" align="right">Total </th>
                <th align="right"><? echo number_format($total_grey_fabric,2);  ?></th>

                <th align="right"><? //echo number_format($tot_process_loss,2); ?></th>
                 <th align="right"><? echo number_format($total_finish_fabric,2); ?></th>
                <th align="right"><? //echo number_format($total_grey_fabric,4);  ?></th>
                <th align="right"><? //echo number_format($total_grey_fabric,4);  ?></th>
                <th align="right"></th>

                <?
                if($fabric_source ==2)
                {
					?>
					<th align="right"><? echo number_format($toatl_rate,2);?></th>
					<th align="right"><? echo number_format($total_amount,2); ?></th>
					<?
                }
                ?>
            </tr>

    </table>
    <br/>
    <div style="width:1330px; float:left">

    <table align="left" class="rpt_table" width="1330"  border="1" cellpadding="0" cellspacing="0" rules="all">

        <thead align="center">
           <tr>
           		 <th align="left" colspan="6"><strong>Accessoris Requirement</strong></th>
           </tr>
               <tr>
                <th width="30">Sl</th>
                <th width="250">Item</th>
                <th width="300">Desc.</th>
                <th width="80">Qnty</th>
                <th width="80">UOM</th>
                <th width="">Remarks</th>
         </tr>

       </thead>
       </table>
       <table class="rpt_table" width="1330"  border="1" cellpadding="0" cellspacing="0" rules="all">
        <tbody>
            <?
       $k=1;$total_acc_qty=0;
	  if($db_type==0)
		{
			 $sql_acces="select id,booking_no,item_group_id,description,uom,qty,remarks from wo_non_ord_booking_acc_dtls where booking_no='$txt_booking_no' and description!=''";
		}
		if($db_type==2)

		{
			 $sql_acces="select id,booking_no,item_group_id,description,uom,qty,remarks from wo_non_ord_booking_acc_dtls where booking_no='$txt_booking_no' and description is not null";
		}
		$sql_result= sql_select( $sql_acces);
		 foreach($sql_result as $row)
		 {
	   ?>
           <tr>
                <td width="30" align="center"><? echo $k; ?></td>
                <td width="250"><? echo $item_library[$row[csf('item_group_id')]]; ?></td>
                <td width="300"><? echo $row[csf('description')]; ?></td>
                <td width="80" align="right"><? echo $row[csf('qty')]; ?></td>
                <td width="80" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                <td width=""><? echo $row[csf('remarks')]; ?></td>

        </tr>
        <?
		$k++;
		$total_acc_qty+=$row[csf('qty')];
		 }
		?>
         </tbody>


 </table>
 </div>
 <br/>
 <table align="left" class="rpt_table" width="100%"  border="0" cellpadding="2" cellspacing="0" rules="all">
 <tr>
 <td>&nbsp; </td>
 </tr>
 </table>
 <?

$size_array=array(); $qty_array=array(); $sample_arr=array();

	if($db_type==0)
		{
			$sql_req=sql_select("select a.booking_no,a.gmts_color,a.gmts_size,sum(a.rf_qty) as tsizeqty,sum(a.bh_qty) as tbh_qty,a.sample_type from wo_non_ord_samp_booking_dtls a where a.booking_no='$txt_booking_no' and a.style_id='' and is_deleted=0 and status_active=1 group by a.booking_no,a.gmts_color,a.gmts_size,a.sample_type");
		}

	if($db_type==2)
		{

			$sql_req=sql_select("select a.booking_no,a.gmts_color,a.gmts_size,sum(a.rf_qty) as tsizeqty,sum(a.bh_qty) as tbh_qty,a.sample_type from wo_non_ord_samp_booking_dtls a where a.booking_no='$txt_booking_no' and a.style_id is null and is_deleted=0 and status_active=1 group by a.booking_no,a.gmts_color,a.gmts_size,a.sample_type");
		}
		$bhGtotal="";
		$rfGtotal="";
	foreach( $sql_req  as $row)
	{
		$key=$row[csf('gmts_color')].'__'.$row[csf('sample_type')];
		$qty_array[$key][$row[csf('gmts_size')]]['size_qty']=$row[csf('tsizeqty')];
		$bhGtotal=$qty_array[$key][$row[csf('gmts_size')]]['size_qty']=$row[csf('tsizeqty')];
		$qty_array[$key][$row[csf('gmts_size')]]['bh_qty']=$row[csf('tbh_qty')];
		$size_array[$row[csf('gmts_size')]]=$row[csf('gmts_size')];

	}
//print_r($bhGtotal);
		if($db_type==0)
		{
 		$sql_data_color=sql_select("select gmts_color,sum(bh_qty) as bh_qty,sample_type from wo_non_ord_samp_booking_dtls  where booking_no='$txt_booking_no'  and status_active=1 and is_deleted=0 and bh_qty>0 and style_id=''   group by gmts_color,sample_type  order by sample_type ");

		}
		if($db_type==2)
		{
 $sql_data_color=sql_select("select gmts_color,sum(bh_qty) as bh_qty,sample_type from wo_non_ord_samp_booking_dtls  where booking_no='$txt_booking_no'  and status_active=1 and is_deleted=0 and bh_qty>0 and style_id is null   group by gmts_color,sample_type  order by sample_type ");
		}

$color_array=array();
$sample_arr=array();
foreach($sql_data_color as $row)
{
	$key=$row[csf('gmts_color')].'__'.$row[csf('sample_type')];
	$color_array[$key]=$row[csf('gmts_color')];
	$sample_arr[$key]=$row[csf('sample_type')];
	$sampleSpan[$row[csf('sample_type')]]+=1;
}
 $sizearr=return_library_array("select id,size_name from lib_size where status_active =1 and is_deleted=0","id","size_name");
 $colorarr=return_library_array("select id,color_name from  lib_color where status_active =1 and is_deleted=0","id","color_name");
 $width=400+(count($size_array)*150);


 ?>
 <table align="left" cellspacing="0" width="<? echo $width?>"  border="1" rules="all" class="rpt_table" >
  <caption> <strong>Sample Requirement</strong></caption>
        <thead  align="center">
            <tr>
                <th width="30" rowspan="2">SL</th>
                <th width="80" rowspan="2" align="center">Sample</th>
                <th width="80" rowspan="2" align="center">Color/Size</th>
                <?
                //$size_name=array_filter(array_unique(explode('__',$size_array)));
                foreach ($size_array as $sizid)
                {
                //$size_count=count($sizid);
                ?>
                <th width="" colspan="2"><strong><? echo  $sizearr[$sizid];  ?></strong></th>

                <?
                } ?>
               <th width="80" rowspan="2" align="center">Total Qnty.</th>
           </tr>

            <tr>
             <?
            foreach ($size_array as $sizid)
            {
            //$size_count=count($sizid);
            ?>
            <th width="75"> BH &nbsp;</th> <th width="75"> Rf.&nbsp;</th>
            <?
            } ?>
            </tr>
        </thead>



        <tbody>
			<?
            $j=1;
            $tot_qnty=array();
                foreach($color_array as $key=>$cid)
                {
                   $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$color_count=count($cid);

					$rowSpan=$sampleSpan[$sample_arr[$key]];
                    ?>

                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <? if($tempData[$sample_arr[$key]]!=1){ ?>
                        	<td rowspan="<? echo $rowSpan;?>"><? echo $j;?></td>
                            <td rowspan="<? echo $rowSpan;?>"><? echo $sample_library[$sample_arr[$key]]; ?></td>
                        <?
							$tempData[$sample_arr[$key]]=1;
							$j++;
						}
						?>
                        <td><? echo $colorarr[$cid]; ?></td>
		                <?
						$total_rf="";
						$total_bh="";
						foreach ($size_array as $sizval)
						{
						$tot_qnty[$key]+=$qty_array[$key][$sizval]['size_qty'];
						$tot_qnty[$key]+=$qty_array[$key][$sizval]['bh_qty'];
						$qty_array[$sizval]['bh_qty']+=$qty_array[$key][$sizval]['bh_qty'];
						$qty_array[$sizval]['size_qty']+=$qty_array[$key][$sizval]['size_qty'];
						?>
						<td width="75" align="right"> <? echo $qty_array[$key][$sizval]['bh_qty']; $total_bh+=$qty_array[$key][$sizval]['bh_qty'];?></td>
                        <td width="75" align="right"> <? $rf=$qty_array[$key][$sizval]['size_qty']; if ($rf==0){ echo "";} else { echo $rf;} $total_rf+=$rf; ?></td>
						<?
						}
						?>
                        <td align="right"><? echo $tot_qnty[$key]; ?></td>
                    </tr>
                    <?
					$production_quantity+=$tot_qnty[$key];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="3" align="right"><strong>Grand Total :</strong></td>

				 <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $qty_array[$sizval]['bh_qty']; ?></td>
                    <td align="right"><?php echo $qty_array[$sizval]['size_qty']; ?></td>
                    <?
				}

					//$non_imge_arr=return_library_array( "select master_tble_id,image_location from  common_photo_library where file_type=1 and form_name='sample_booking_non' and master_tble_id='$txt_booking_no' ",'master_tble_id','image_location');
					//print_r($non_imge_arr);
					$sql_non_img=sql_select("select master_tble_id,image_location from  common_photo_library where file_type=1 and form_name='sample_booking_non' and master_tble_id='$txt_booking_no'");
			  ?>



            <td align="right"><?php echo $production_quantity; ?></td>
        </tr>
    </table>
     <br clear="all">
          <table align="left" class="rpt_table" width="auto"  border="0" cellpadding="0" cellspacing="10">
           <tr>
            <?
            foreach($sql_non_img as $row)
            {
			?>
        	 <td align="left" width="80"> <?
            if($row[csf('image_location')]!="")
			{
			?>
            <p align="left"><img border="1"  src='../../<? echo $row[csf('image_location')]; ?>' height='150' width='130' /></p>
            <?
			}
			else "&nbsp;";?>
             </td>
           <?
			}
		   ?>

           </tr>
          </table>
        <br/>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="100%">
                    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th width="3%">Sl</th><th width="97%" align="left">Special Instruction</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form = 90 and booking_no='$txt_booking_no'");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>
                                </tr>
                            <?
						}
					}
					/*else
					{
						$i=0;
					$data_array=sql_select("select id, terms from  lib_terms_condition");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>

                                </tr>
                    <?
						}
					} */
					?>
                </tbody>
                </table>
                </td>

            </tr>
        </table>
          <?
		 	echo signature_table(6, $cbo_company_name, "1330px");
			echo "****".custom_file_name($varcode_booking_no,$style_sting,'');
		  ?>
       </div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
        </script>
       <?

}

if($action=="show_fabric_booking_report7")// For UG
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//$deal_merchant_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=9");
	list($nameArray_approved_row)=$nameArray_approved;
	?>
	<div style="width:1330px" align="center">
    										<!--    Header Company Information         -->
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100">
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php
								echo $company_library[$cbo_company_name];
								?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
                            ?>
                                            <? echo $result[csf('plot_no')]; ?> &nbsp;
                                            Level No: <? echo $result[csf('level_no')]?> &nbsp;
                                           <? echo $result[csf('road_no')]; ?>  &nbsp;
                                            <? echo $result[csf('block_no')];?>  &nbsp;
                                            City No: <? echo $result[csf('city')];?>
                                            Zip Code: <? echo $result[csf('zip_code')]; ?>
                                           <?php echo $result[csf('province')]; ?>
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                            Email Address: <? echo $result[csf('email')];?>
                                            Website No: <? echo $result[csf('website')];
                            }
                                            ?>

                               </td>
                            </tr>
                            <tr>

                            <td align="center" style="font-size:20px">
                                <strong><? if($report_title !=""){echo $report_title;} else {echo "Sample Fabric Booking -Without order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             </td>

                              <td>
                              <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <strong> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
                                  <br/>

								  <?
								 }
							  	?>
                             </td>

                            </tr>
                      </table>
                </td>
                <td width="250" id="barcode_img_id">

                </td>
            </tr>
       </table>

                <?
				$season="";
				$buyer_req_no="";
				$nameseason=sql_select( "select a.season, b.buyer_req_no  from  sample_development_mst a, sample_development_dtls b, wo_non_ord_samp_booking_dtls c  where  a.id=b. sample_mst_id and a.id=c.style_id and c.booking_no=$txt_booking_no");
				foreach ($nameseason as $season_row)
				{
					$season=$season_row[csf('season')];
					$buyer_req_no=$season_row[csf('buyer_req_no')];
				}
                $nameArray=sql_select( "select buyer_id,pay_mode,fabric_source,attention 	,booking_no,booking_date,supplier_id,currency_id,exchange_rate,attention,delivery_date,source,team_leader,dealing_marchant from wo_non_ord_samp_booking_mst   where  booking_no=$txt_booking_no");
				foreach ($nameArray as $result)
				{
					$fab_source=$result[csf('fabric_source')];
					$varcode_booking_no=$result[csf('booking_no')];
				?>
       <table width="100%" style="border:1px solid black">
            <tr>
                <td colspan="6" valign="top"></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:12px"><b>Fab. Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>

            </tr>
            <tr>

                <td width="100"><span style="font-size:12px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
                 <td width="100"><span style="font-size:12px"><b>Pay Mode</b></span></td>
                <td width="110">:&nbsp;<? echo $pay_mode[$result[csf('pay_mode')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<?
					if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}

				?></td>

            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $season; ?></td>
                <td  width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td  width="110" >:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Source</b></td>
                <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
            </tr>
              <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
               <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;<?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
				$comAdd=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$result[csf('supplier_id')]);
						foreach ($comAdd as $comAddRow){
						echo $comAddRow[csf('plot_no')].'&nbsp;';
						echo $comAddRow[csf('level_no')].'&nbsp;' ;
						echo $comAddRow[csf('road_no')].'&nbsp;';
						echo $comAddRow[csf('block_no')].'&nbsp;';
						echo $comAddRow[csf('city')].'&nbsp;';
					}
				}
				else{
					echo $supplier_address_arr[$result[csf('supplier_id')]];
				}
				?></td>
            </tr>
            <tr>
                <td width="100"><span style="font-size:12px"><b></b></span></td>
                <td width="110">&nbsp;<? //echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
                <td width="100"><span style="font-size:12px"><b>Buyer Req. No</b></span></td>
                <td width="110">:&nbsp;<? //echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
                <td width="100"><span style="font-size:12px"><b>Attention</b></span></td>
                <td width="110">:&nbsp;<? echo $result[csf('attention')]; ?></td>
            </tr>
        </table>
        <?
			}
		?>

      <br/>

      <?
	$composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 order by b.id";
	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
		}
	}



	$sql_smn= "select style_id, uom as uom_id, style_des, sample_type, body_part, color_type_id, construction, composition, gsm_weight, gmts_color, fabric_color, gmts_size, item_size, dia_width, finish_fabric, process_loss, grey_fabric, rate, amount, id, lib_yarn_count_deter_id as lib_yarn_count_deter_id, yarn_details, remarks, fabric_source FROM wo_non_ord_samp_booking_dtls  WHERE booking_no =$txt_booking_no  and status_active=1 and is_deleted=0 order by id";
	//echo $sql_smn;
	$sql_result= sql_select($sql_smn);
	foreach ($sql_result as $row)
        {
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['style_id']=$row[csf('style_id')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['style_des']=$row[csf('style_des')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['sample_type']=$row[csf('sample_type')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['body_part']=$row[csf('body_part')];

			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['color_type_id']=$row[csf('color_type_id')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['construction']=$row[csf('construction')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['composition']=$row[csf('composition')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['gsm_weight']=$row[csf('gsm_weight')];

			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['gmts_color']=$row[csf('gmts_color')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['fabric_color']=$row[csf('fabric_color')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['gmts_size']=$row[csf('gmts_size')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['item_size']=$row[csf('item_size')];

			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['finish_fabric']=$row[csf('finish_fabric')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['grey_fabric']=$row[csf('grey_fabric')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['process_loss']=$row[csf('process_loss')];
			//$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['grey_fabric']=$row[csf('item_sgrey_fabricize')];

			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['rate']=$row[csf('rate')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['amount']=$row[csf('amount')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['lib_yarn_count_deter_id']=$row[csf('lib_yarn_count_deter_id')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['yarn_details']=$row[csf('yarn_details')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['remarks']=$row[csf('remarks')];
			$uom_data_type[$row[csf('uom_id')]][$row[csf('id')]]['fabric_source']=$row[csf('fabric_source')];
		}
		//print_r($uom_data_type);
?>
    <table class="rpt_table" width="100%"  border="2" cellpadding="2" cellspacing="0" rules="all">
        <thead>
            <tr style="border-top:3px solid black;">
                <th width="30">Sl</th>
                <th width="100">Sample</th>
                <th width="80">Fabric Source</th>
                <th width="100">Style</th>
                <th width="100">Style Des</th>
                <th width="90">Body Part</th>
                <th width="80">Color Type</th>
                <th width="80">Construction</th>
                <th width="100">Composition & Yarn</th>
                <th width="40">GSM</th>
                <th width="50">Dia/ Width</th>
                <th width="80">Gmts.Color</th>
                <th width="80">Fab.Color</th>
                <th width="80">Gmts.Size</th>
                <th width="80">Item Size</th>
                <th width="60">Fabric Qty</th>

                <?
                if($fab_source !=1)
                {
                ?>
                <th width="60">Rate</th>
                <th width="70">Amount</th>
                <?
                }
                ?>
                <th>Remarks</th>
            </tr>
        </thead>
        <?
		 $style_id="";
        $total_finish_fabric=0;
        $total_grey_fabric=0;
        $toatl_rate=0;
        $total_amount=0;

        $i=1;
		foreach ($uom_data_type as $uom_key=>$uom_data)
        {
		?>
          <tr>
            	<td colspan="19" align="center"> <b><? echo 'Fabric Details in '. $unit_of_measurement[$uom_key]; ?></b></td>
        </tr>
        <?

       foreach ($uom_data as $dtls_id=>$val)
       {
            $style_id.=$val[('style_id')].",";
            if($val[('style_id')])$style_sting.=$style_library[$val[('style_id')]].'_';

			//echo $val['grey_fabric'].'ff';
			?>

			<tr>
                <td align="center"><? echo $i; ?></td>
                <td><? echo $sample_library[$val[('sample_type')]]; ?></td>
                <td><? echo $fabric_source[$val['fabric_source']]; ?></td>
                <td><? echo $style_library[$val[('style_id')]]; ?></td>
                <td><? echo $val[('style_des')]; ?></td>
                <td><? echo $body_part[$val[('body_part')]]; ?></td>
                <td><? echo $color_type[$val[('color_type_id')]]; ?></td>
                <td><? echo $val[('construction')]; ?></td>
                <td><? echo $composition_arr[$val[('lib_yarn_count_deter_id')]]; ?></td>
                <td><? echo $val[('gsm_weight')];?></td>
                <td><? echo $val[('dia_width')];?></td>

                <td><? echo $color_library[$val[('gmts_color')]];?></td>
                <td align="center"><? echo $color_library[$val[('fabric_color')]]; ?></td>
                <td align="center"><? echo $size_library[$val['gmts_size']];//$row[csf('dia_width')];$row[csf('grey_fabric')] ?></td>
                 <td align="center"><? echo $val['item_size'];//$row[csf('dia_width')];$row[csf('grey_fabric')] ?></td>

                <td align="right"><? echo number_format($val['grey_fabric'],2);$total_finish_fabric+=$val['grey_fabric'];//process_loss  ?></td>
                 <?
                if($fab_source !=1)
                {
                    ?>
                    <td align="right"><? echo number_format($val[('rate')],2); $toatl_rate+=$val[('rate')]; ?></td>
                    <td align="right"><? echo number_format($val[('amount')],2); $total_amount+=$val[('amount')];?></td>
                    <?
                }
                ?>

                <td><? echo $val[('remarks')];?></td>

			</tr>
			<?
			$tot_process_loss+=$val[('process_loss')];
			$i++;
	   	}
		?>
         <tr>
                <th colspan="12" align="right">Total </th>
                <th align="right" ><? //echo number_format($total_grey_fabric,2);$total_grey_fabric=0;  ?></th>

                <th align="right"><? //echo number_format($tot_process_loss,2); ?></th>
                <th align="right"><? //echo number_format($total_grey_fabric,4);  ?></th>
                  <th align="right" ><? echo number_format($total_finish_fabric,2);$total_finish_fabric=0; ?></th>

                <?
                if($fab_source !=1)
                {
					?>
					<th align="right"><? //echo number_format($toatl_rate,2);$toatl_rate=0;?></th>
					<th align="right"><? echo number_format($total_amount,2);$total_amount=0; ?></th>
					<?
                }
                ?>
                  <th align="right"><? //echo number_format($total_grey_fabric,4);  ?></th>
            </tr>
        <?
	   }
	   ?>





    </table>
 <br/>
        <?
		$sql_img=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='sample_booking_non' and master_tble_id=$txt_booking_no and file_type=1");
		?>
        <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
        <caption align="center"><b>View Photo</b></caption>
        <tr>
        <td colspan="8" height="100">
       <?
        foreach($sql_img as $img)
		{
		?>
        	<img  src='../../<? echo $img[csf('image_location')]; ?>' height='90px' width='90px' /> &nbsp;&nbsp;
        <?
		}
		?>
        </td>
        </tr>
        </table>
        <br/>


        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="100%">
                    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    <tr>
                        	<th width="50" colspan="5" align="left">Approval Status</th>

                        </tr>
                    	<tr>
                        	<th width="30">Sl</th>
                            <th width="100">Name/Designation</th>
                            <th width="100">Approval Date</th>
                            <th width="80">Approval No</th>

                        </tr>
                    </thead>
                    <tbody>
                    <?

					$user_name_arr=return_library_array("select id,user_name from user_passwd where valid=1 order by user_name", "id", "user_name");
					$nameArray_approved=sql_select( "select b.approved_no as approved_no,b.approved_date,b.approved_by from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no=$txt_booking_no and b.entry_form=9");

					if ( count($nameArray_approved)>0)
					{
						$k=1;
						foreach( $nameArray_approved as $row )
						{
							$i++;

							?>
                            	<tr id="settr_1" align="">
                                    <td width="30"><? echo $k; ?></td>
                                    <td width="100"><? echo $user_name_arr[$row[csf('approved_by')]]; ?></td>
                                    <td width="100"><? echo change_date_format($row[csf('approved_date')]); ?></td>
                                    <td width="80"><? echo $row[csf('approved_no')]; ?></td>

                                </tr>
                            <?
						}
					}
					?>

                </tbody>
                </table>
                </td>

            </tr>
        </table>
        <br/>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="100%">
                    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th width="3%">Sl</th><th width="97%" align="left">Spacial Instruction</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form = 90 and booking_no=$txt_booking_no ");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>
                                </tr>
                            <?
						}
					}
					/*else
					{
						$i=0;
					$data_array=sql_select("select id, terms from  lib_terms_condition");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>

                                </tr>
                    <?
						}
					} */
					?>
                </tbody>
                </table>
                </td>

            </tr>
        </table>
          <?
		 	echo signature_table(6, $cbo_company_name, "1330px");
			echo "****".custom_file_name($varcode_booking_no,$style_sting,'');
		  ?>
       </div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
        </script>
       <?
     exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if($db_type==0)
		{
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SMN', date("Y",time()), 5, "select id, booking_no_prefix, booking_no_prefix_num from wo_non_ord_samp_booking_mst where company_id=$cbo_company_name and booking_type=4 and YEAR(insert_date)=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		else if($db_type==2)
		{
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SMN', date("Y",time()), 5, "select id, booking_no_prefix, booking_no_prefix_num from wo_non_ord_samp_booking_mst where company_id=$cbo_company_name and booking_type=4 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}

		$id=return_next_id( "id", "wo_non_ord_samp_booking_mst", 1 ) ;
		$field_array="id,booking_type,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,brand_id,entry_form_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,source,booking_date,delivery_date,supplier_id,attention,ready_to_approved,team_leader,dealing_marchant,uom_id,inserted_by,insert_date";
		 $data_array ="(".$id.",4,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$cbo_brand_name.",90,".$cbo_fabric_natu.",".$cbo_fabric_source.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_source.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_supplier_name.",".$txt_attention.",".$cbo_ready_to_approved.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$cbouom_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		 //echo "10** insert into wo_non_ord_samp_booking_mst ($field_array) values $data_array";die;
		 $rID=sql_insert("wo_non_ord_samp_booking_mst",$field_array,$data_array,0);

		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0]."**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0]."**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		$yarnAllo=sql_select("select id from inv_material_allocation_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		if(count($yarnAllo)>0){
			echo "yarnallocation**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}

		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

		$receive_mrr=0;
		$sqlre=sql_select("select recv_number from inv_receive_master where booking_no=$txt_booking_no");
		foreach($sqlre as $rows){
			$receive_mrr=$rows[csf('recv_number')];
		}
		if($receive_mrr){
			echo "rec1**".str_replace("'","",$txt_booking_no)."**".$receive_mrr;
			disconnect($con);die;
		}

		$issue_mrr=0;
		//$sqlis=sql_select("select issue_number from inv_issue_master where booking_no=$txt_booking_no");
		$sqlis=sql_select("select a.issue_number from inv_issue_master a,inv_transaction b where  a.id=b.mst_id and a.booking_no=$txt_booking_no and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.issue_number ");
		foreach($sqlis as $rows){
			$issue_mrr=$rows[csf('issue_number')];
		}
		if($issue_mrr){
			echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
			disconnect($con);die;
		}

		$field_array="buyer_id*brand_id*item_category*fabric_source*currency_id*exchange_rate*pay_mode*source*booking_date*delivery_date*supplier_id*attention*ready_to_approved*team_leader*dealing_marchant*uom_id*updated_by*update_date";
		 $data_array ="".$cbo_buyer_name."*".$cbo_brand_name."*".$cbo_fabric_natu."*".$cbo_fabric_source."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_supplier_name."*".$txt_attention."*".$cbo_ready_to_approved."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$cbouom_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("wo_non_ord_samp_booking_mst",$field_array,$data_array,"id","".$update_id."",0);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
			    oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3) $is_approved=1; else $is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		$yarnAllo=sql_select("select id from inv_material_allocation_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		if(count($yarnAllo)>0){
			echo "yarnallocation**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}

		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id and b.work_order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

		$pplbook=0;
		$ppl=sql_select("select b.id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.booking_no=$txt_booking_no and a.is_sales!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id");
		foreach($ppl as $pplrow){
			$pplbook=$pplrow[csf('id')];
		}

		if($pplbook!=0){
			echo "PPL**".str_replace("'","",$txt_booking_no)."**".$pplbook;
			disconnect($con);die;
		}

		$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			disconnect($con);die;
		}

		$receive_mrr=0;
		$sqlre=sql_select("select recv_number from inv_receive_master where booking_no=$txt_booking_no");
		foreach($sqlre as $rows){
			$receive_mrr=$rows[csf('recv_number')];
		}
		if($receive_mrr){
			echo "rec1**".str_replace("'","",$txt_booking_no)."**".$receive_mrr;
			disconnect($con);die;
		}

		$issue_mrr=0;
		$sqlis=sql_select("select issue_number from inv_issue_master where booking_no=$txt_booking_no");
		foreach($sqlis as $rows){
			$issue_mrr=$rows[csf('issue_number')];
		}
		if($issue_mrr){
			echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
			disconnect($con);die;
		}

		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$flag=1;
		$rID=sql_delete("wo_non_ord_samp_booking_mst",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		if($rID==1) $flag=1; else $flag=0;
		$rID2=sql_delete("wo_non_ord_samp_booking_dtls",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		if($rID2==1) $flag=1; else $flag=0;
		$rID3=sql_delete("wo_non_ord_samp_yarn_dtls",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		if($rID3==1) $flag=1; else $flag=0;

		if($db_type==0)
		{
			if($flag ){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag ){
				 oci_commit($con);
				echo "2**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else{
				 oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="fabric_description_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	?>
	<script>
	function toggle( x, origColor )
	{
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	function js_set_value(data)
	{
		var data=data.split('_');
		var fabric_yarn_description=return_global_ajax_value(data[0], 'fabric_yarn_description', '', 'sample_booking_non_order_controller');
		var fabric_yarn_description_arr=fabric_yarn_description.split("**");
		var fabric_description=trim(data[2])+' '+trim(fabric_yarn_description_arr[0]);
	    document.getElementById('fab_des_id').value=data[0];
		document.getElementById('fab_nature_id').value=data[1];
		document.getElementById('fab_desctiption').value=trim(fabric_description);
		document.getElementById('fab_gsm').value=trim(data[3]);
		document.getElementById('yarn_desctiption').value=trim(fabric_yarn_description_arr[1]);
		document.getElementById('process_loss').value=trim(data[4]);
		document.getElementById('construction').value=trim(data[2]);
		document.getElementById('composition').value=trim(fabric_yarn_description_arr[0]);
	    parent.emailwindow.hide();
	}
	</script>
	</head>
	<body>
	<div align="center">
	<form>
	<input type="hidden" id="fab_des_id" name="fab_des_id" />
	<input type="hidden" id="fab_nature_id" name="fab_des_id" />
	<input type="hidden" id="fab_desctiption" name="fab_des_id" />
	<input type="hidden" id="fab_gsm" name="fab_gsm" />
	<input type="hidden" id="yarn_desctiption" name="yarn_desctiption" />
	<input type="hidden" id="process_loss" name="process_loss" />
	<input type="hidden" id="construction" name="construction" />
	<input type="hidden" id="composition" name="composition" />
    <table cellspacing="0" width="1030" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
        <thead>
            <tr>
                <th colspan="9" align="center"><?=create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
        </thead>
    </table>
	<?
    $composition_arr=array();
    $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$lib_fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where status_active=1", "id", "fabric_composition_name");
	$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id,b.id";
	$data_array=sql_select($sql);
	if (count($data_array)>0)
    {
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";

			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
		}
	}

	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,a.id,a.fabric_composition_id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.fab_nature_id= '$fabric_nature' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,a.fabric_composition_id order by a.id";

	$arr=array (0=>$item_category, 3=>$color_range,6=>$composition_arr,7=>$lib_fabric_composition,8=>$lib_yarn_count,9=>$yarn_type);
	echo  create_list_view ( "list_view", "Fab Nature,Construction,GSM/ Weight,Color Range,Stich Length,Process Loss,Composition,Fab Composition", "100,100,50,100,50,50,300,180","1030","350",0, $sql, "js_set_value", "id,fab_nature_id,construction,gsm_weight,process_loss", "",1, "fab_nature_id,0,0,color_range_id,0,0,id,fabric_composition_id", $arr , "fab_nature_id,construction,gsm_weight,color_range_id,stich_length,process_loss,id,fabric_composition_id", "../merchandising_details/requires/yarn_count_determination_controller", 'setFilterGrid("list_view",-1);','0,0,1,0,1,1,0') ;
	?>
	</form>
	</div>
	</body>
	</html>
	<?
	exit();
}

if($action =="fabric_yarn_description")
{
	$fab_description="";
	$yarn_description="";
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");

	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id,b.id";

	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if($fab_description!="")
			{
				$fab_description=$fab_description." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$fab_description=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}

			if($yarn_description!="")
			{
				$yarn_description=$yarn_description."__".$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')];

			}
			else
			{
				$yarn_description=$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')];

			}
		}
	}
	echo $fab_description."**".$yarn_description;

}

if($action=="sample_description_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id,style_no,sample_id,article_no,bh_qty,gmt_color,size_qty,dtls_id,sample_name)
		{

			document.getElementById('style_id').value=trim(id);
			document.getElementById('style_no').value=trim(style_no);
			document.getElementById('sample_id').value=trim(sample_id);
			document.getElementById('article_no').value=trim(article_no);
			document.getElementById('bh_qty').value=trim(bh_qty);
			document.getElementById('gmt_color').value=trim(gmt_color);
			document.getElementById('size_qty').value=trim(size_qty);
			document.getElementById('hid_dtls_id').value=trim(dtls_id);
			document.getElementById('sample_name_id').value=trim(sample_name);
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="470" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                    <thead>
                        <tr>
                            <th width="70">Style ID</th>
                            <th width="100">Style Ref.</th>
                            <th width="100">Sample Name</th>
                            <th width="100">Sample Color</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                        	<input type="hidden" id="style_id" name="style_id" />
                            <input type="hidden" id="style_no" name="style_no" />
                            <input type="hidden" id="sample_id" name="sample_id" />
                            <input type="hidden" id="article_no" name="article_no" />
                            <input type="hidden" id="bh_qty" name="bh_qty"  />
                            <input type="hidden" id="gmt_color" name="gmt_color"  />
                            <input type="hidden" id="size_qty" name="size_qty"  />
                            <input type="hidden" id="hid_dtls_id" name="hid_dtls_id"  />
                            <input type="hidden" id="sample_name_id" name="sample_name_id"  />
                            <input type="text" style="width:60px" class="text_boxes_numeric" name="txt_sample_id" id="txt_sample_id" />
                        </td>

                        <td><input type="text" style="width:90px" class="text_boxes" name="txt_style_ref" id="txt_style_ref" /></td>
                        <td><? echo create_drop_down( "cbo_sample_type", 100, "select a.id, a.sample_name from lib_sample a, sample_development_dtls b, sample_development_mst c where a.id=b.sample_name and c.id=b.sample_mst_id and b.is_deleted=0 and c.is_deleted=0 and c.company_id ='$cbo_company_name' and c.buyer_name ='$cbo_buyer_name' group by a.id, a.sample_name order by a.sample_name ASC","id,sample_name", 1, "-Select", $selected,"","", "" ); ?></td>
                        <td><? echo create_drop_down( "cbo_sample_color", 100, "select a.id, a.color_name from  lib_color a, sample_development_dtls b, sample_development_mst c where a.id=b.sample_color and c.id=b.sample_mst_id and b.is_deleted=0 and c.is_deleted=0 and c.company_id ='$cbo_company_name' and c.buyer_name ='$cbo_buyer_name' group by a.id, a.color_name order by a.color_name ASC","id,color_name", 1, "-Select-", $selected,"","", "" ); ?></td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_sample_id').value+'_'+document.getElementById('txt_style_ref').value+'_'+document.getElementById('cbo_sample_type').value+'_'+document.getElementById('cbo_sample_color').value+'_'+'<? echo $cbo_company_name; ?>'+'_'+'<? echo $cbo_buyer_name; ?>', 'create_sample_des_list_view', 'search_div', 'sample_booking_non_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" /></td>
                    </tr>
                </table>
            </form>
        </div>
        <div id="search_div"></div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_sample_des_list_view")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$ex_data=explode("_",$data);
	$sample_id=$ex_data[0];
	$style_ref=$ex_data[1];
	$sample_type=$ex_data[2];
	$sample_color=$ex_data[3];

	$cbo_company_name=$ex_data[4];
	$cbo_buyer_name=$ex_data[5];

	if($sample_id!="") $sample_id_cond="and a.id='$sample_id'"; else $sample_id_cond="";
	if($style_ref!="") $style_ref_cond="and a.style_ref_no='$style_ref'"; else $style_ref_cond="";
	if($sample_type!=0) $sample_type_cond="and b.sample_name='$sample_type'"; else $sample_type_cond="";
	if($sample_color!=0) $sample_color_cond="and b.sample_color='$sample_color'"; else $sample_color_cond="";

	$bh_qty_arr=array();

	$bh_qty_sql=sql_select("select a.id,b.id as dtls_id,sum(c.size_qty) as tsizeqty,sum(c.bh_qty) as tbh_qty from sample_development_mst a,sample_development_dtls b,sample_development_size c where a.id=b.sample_mst_id and  b.id=c.dtls_id group by a.id,b.id");
	foreach( $bh_qty_sql  as $row)
	{
		$bh_qty_arr[$row[csf('id')]][$row[csf('dtls_id')]]['size_qty']=$row[csf('tsizeqty')];
		$bh_qty_arr[$row[csf('id')]][$row[csf('dtls_id')]]['bh_qty']=$row[csf('tbh_qty')];
		//echo $bh_qty_arr[$row[csf('id')]]['bh_qty'];
	}

	$sql= "select  a.id, a.style_ref_no, a.company_id, a.buyer_name, a.article_no, b.id as bid, b.sample_name, b.sample_color, a.requisition_number_prefix_num, b.working_factory, b.receive_date_from_factory, b.sent_to_factory_date, b.sent_to_buyer_date, b.approval_status, b.status_date, b.recieve_date_from_buyer from sample_development_mst a, sample_development_dtls b where a.id=b.sample_mst_id and a.company_id ='$cbo_company_name' and buyer_name ='$cbo_buyer_name' $sample_id_cond $style_ref_cond $sample_type_cond $sample_color_cond and a.is_deleted=0 and b.is_deleted=0 order by a.id DESC";
	$nameArray=sql_select( $sql );
	?>
	<div style="width:920px;">
	    <table width="900" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" align="left">
	    	<thead>
				<tr>
					<th width="30">SL</th>
					<th width="50">Style Id</th>
                    <th width="100">Style</th>
                    <th width="50">Req. No</th>
					<th width="100">Sample Name</th>
					<th width="100">Sample Color</th>
					<th width="120">Working Factory</th>
					<th width="70">Buyer Recieve Date</th>
					<th width="70">Sent To factory</th>
					<th width="70">Submission to Buyer</th>
					<th width="60">Approval Status</th>
					<th>Status Date</th>
				</tr>
            </thead>
        </table>
		<div style="max-height:320px; width:920px; overflow-y:scroll" >
            <table width="900" cellspacing="0" cellpadding="0" border="0" rules="all" id="tbl_list_search"  class="rpt_table" align="left">
                <tbody>
                <?
                $sample_name_arr=return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
                $color_name_arr=return_library_array( "select id, color_name from  lib_color where status_active =1 and is_deleted=0",'id','color_name');

                $i=1;
                foreach ($nameArray as $row)
                {
                    ?>
                    <tr  bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $row[csf("id")]; ?>','<? echo $row[csf("style_ref_no")]; ?>','<? echo $row[csf("sample_name")]; ?>','<? echo $row[csf("article_no")]; ?>','<? echo $bh_qty_arr[$row[csf('id')]][$row[csf('bid')]]['bh_qty'] ?>','<? echo $color_name_arr[$row[csf("sample_color")]]; ?>','<? echo $bh_qty_arr[$row[csf('id')]][$row[csf('bid')]]['size_qty']; ?>','<? echo $row[csf('bid')]; ?>','<? echo $row[csf('sample_name')]; ?>')">
                        <td width="30"><? echo $i; ?></td>
                        <td width="50" align="center"><? echo $row[csf("id")];?></td>
                        <td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf("style_ref_no")];?></td>
                        <td width="50" align="center"><? echo $row[csf("requisition_number_prefix_num")];?></td>
                        <td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $sample_name_arr[$row[csf("sample_name")]];?></td>
                        <td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $color_name_arr[$row[csf("sample_color")]];?></td>
                        <td width="120" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf("working_factory")];?></td>
                        <td width="70"><? echo change_date_format($row[csf("recieve_date_from_buyer")]);?></td>
                        <td width="70"><? echo change_date_format($row[csf("sent_to_factory_date")]);?></td>
                        <td width="70"><? echo change_date_format($row[csf("sent_to_buyer_date")]);?></td>
                        <td width="60"><? echo $approval_status[$row[csf("approval_status")]];?></td>
                        <td><? echo change_date_format($row[csf("status_date")]);?></td>
                    </tr>
                    <? $i++; }?>
                </tbody>
            </table>
	    </div>
	</div>
    </body>
    </html>
    <?
    exit();
}

if($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//cbo_company_name*cbo_buyer_name*txt_delivery_date*cbo_team_leader
	$lib_sql_pre = "select b.MONTH, b.YEAR, b.BUDGET_QTY from mm_yr_fab_budget_mst m, mm_yr_fab_budget_dtls d, mm_yr_fab_budget_brkdwn b where m.company_id=$cbo_company_name   and m.team_leader=$cbo_team_leader  and d.buyer_id=$cbo_buyer_name and m.id=b.mst_id and d.mst_id=b.mst_id and b.dtls_id=d.id and b.status_active=1 and b.is_deleted=0 ";
	//echo "10**=".$lib_sql_pre ;die;
	$lib_sql_result = sql_select($lib_sql_pre);
	foreach ($lib_sql_result as $row) {
		$mon=str_pad($row["MONTH"], 2, '0', STR_PAD_LEFT);
		$year=$row["YEAR"];
		$yr_mon='01-'.$mon.'-'.$year;
		$yr_mon_cal=date('M-Y',strtotime($yr_mon));
		//echo $mon.'='.$yr_mon.'='.$yr_mon_cal.'<br>';
        $month_wise_budget_qty_arr[$yr_mon_cal]['budget_qty'] += $row['BUDGET_QTY'];
    }

	$txtGray_qty=str_replace("'",'',$txt_grey_qnty);
	$delivery_date=str_replace("'",'',$txt_delivery_date);
	$yr_mon=date('M-Y',strtotime($delivery_date));
	// echo "13**Grey Qty is greater than Sample Fabric Budget Qty. ".$yr_mon_cal.',Grey='.$txtGray_qty.',Budget='.$budget_qty;
	// disconnect($con);
	// die;
	$budget_qty= $month_wise_budget_qty_arr[$yr_mon]['budget_qty'];
	if($txtGray_qty>$budget_qty && $budget_qty>0)
	{
		echo "13**Grey Qty is greater than Sample Fabric Budget Qty. ".$yr_mon_cal.',Grey='.$txtGray_qty.',Budget='.$budget_qty;
		disconnect($con);
		die;
	}


	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 //if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		$id=return_next_id( "id", "wo_non_ord_samp_booking_dtls", 1 ) ;

		if($db_type==0)
		{
			$field_array="id, booking_mst_id,booking_no, style_id, style_des, sample_type, body_part, body_type_id, item_qty, knitting_charge, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, gmts_color, fabric_color, gmts_size, item_size, dia_width, finish_fabric, process_loss, grey_fabric, rate, amount, bh_qty, rf_qty, yarn_breack_down, process_loss_method, article_no, yarn_details, remarks, fabric_source, dtls_id, inserted_by, insert_date, delivery_date, uom, width_dia_type";
		}
		else if($db_type==2)
		{
			$field_array="id, booking_mst_id,booking_no, style_id, style_des, sample_type, body_part, body_type_id, item_qty, knitting_charge, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, gmts_color, fabric_color, gmts_size, item_size, dia_width, finish_fabric, process_loss, grey_fabric, rate, amount, bh_qty, rf_qty, yarn_breack_down, process_loss_method, article_no, yarn_details, remarks, fabric_source, dtls_id, inserted_by, insert_date, delivery_date, uom, width_dia_type";
		}

		 $field_array2="id, booking_mst_id,wo_non_ord_samp_book_dtls_id, booking_no, count_id, copm_one_id, percent_one, type_id, cons_ratio, cons_qnty, inserted_by, insert_date";
		 $wo_non_ord_samp_yarn_dtls_id=return_next_id( "id", "wo_non_ord_samp_yarn_dtls", 1 ) ;
		 $add_comma_yarn=0;

		 	$gmt_color_str = str_replace("'", "", $txt_gmt_color);
			$gmt_color_nam=strtoupper(trim($gmt_color_str));
			$fab_color_str = str_replace("'", "", $txt_color);
			$fab_color_nam=strtoupper(trim($fab_color_str));

			$new_array_gmts_color=array();
			if(str_replace("'","",$txt_gmt_color)!="")
			{
				if (!in_array(str_replace("'","",$txt_gmt_color),$new_array_gmts_color))
				{
					$gmts_color_id = return_id_lib_common( str_replace("'","",$txt_gmt_color), $color_library, "lib_color", "id,color_name","90");
					$new_array_gmts_color[$gmts_color_id]=str_replace("'","",$txt_gmt_color);
				}
				else $gmts_color_id =  array_search(str_replace("'","",$txt_gmt_color), $new_array_gmts_color);
			}
			else $gmts_color_id =0;


			$new_array_color=array();
			if($gmt_color_nam==$fab_color_nam)
			{
				$color_id=$gmts_color_id;
			}
			else
			{
				if(str_replace("'","",$txt_color)!="")
				{
					if (!in_array(str_replace("'","",$txt_color),$new_array_color))
					{
						$color_id = return_id_lib_common( str_replace("'","",$txt_color), $color_library, "lib_color", "id,color_name","90");
						$new_array_color[$color_id]=str_replace("'","",$txt_color);
					}
					else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
				}
				else $color_id =0; //	echo "10**".$gmt_color_nam.'='.$fab_color_nam;die;
			}

		/*$new_array_gmts_color=array();
		if(str_replace("'","", trim($txt_gmt_color))!="")
		{
			if (!in_array(str_replace("'","", trim($txt_gmt_color)),$new_array_gmts_color))
			{
				$gmts_color_id = return_id( str_replace("'","", trim($txt_gmt_color)), $color_library, "lib_color", "id,color_name","90");
				$new_array_gmts_color[$gmts_color_id]=str_replace("'","", trim($txt_gmt_color));
			}
			else $gmts_color_id =  array_search(str_replace("'","", trim($txt_gmt_color)), $new_array_gmts_color);
		}
		else $gmts_color_id =0;

		$new_array_color=array();
		if(str_replace("'","", trim($txt_color))!="")
		{
			if (!in_array(str_replace("'","", trim($txt_color)),$new_array_color))
			{
				$color_id = return_id( str_replace("'","", trim($txt_color)), $color_library, "lib_color", "id,color_name","90");
				$new_array_color[$color_id]=str_replace("'","", trim($txt_color));
			}
			else $color_id =  array_search(str_replace("'","", trim($txt_color)), $new_array_color);
		}
		else $color_id =0;*/

		$new_array_gmts_size=array();
		if(str_replace("'","", trim($txt_gmts_size))!="")
		{
			if (!in_array(str_replace("'","", trim($txt_gmts_size)),$new_array_gmts_size))
			{
			$gmts_size_id = return_id_lib_common( str_replace("'","", trim($txt_gmts_size)), $size_library, "lib_size", "id,size_name","90");
			$new_array_gmts_size[$gmts_size_id]=str_replace("'","", trim($txt_gmts_size));
			}
			else $gmts_size_id =  array_search(str_replace("'","", trim($txt_gmts_size)), $new_array_gmts_size);
		 }
		 else $gmts_size_id = 0;

		$data_array="(".$id.",".$update_id.",".$txt_booking_no.",".$txt_style.",".$txt_style_des.",".$cbo_sample_type.",".$cbo_body_part.",".$cbo_body_type.",".$txt_item_qty.",".$txt_knitting_charge.",".$cbo_color_type.",".$libyarncountdeterminationid.",".$construction.",".$composition.",".$txt_fabricdescription.",".$txt_gsm.",'".$gmts_color_id."',".$color_id.",'".$gmts_size_id."',".$txt_size.",".$txt_dia_width.",".$txt_finish_qnty.",".$txt_process_loss.",".$txt_grey_qnty.",".$txt_rate.",".$txt_amount.",".$txt_bh_qty.",".$txt_rf_qty.",".$yarnbreackdown.",".$process_loss_method.",".$txt_article_no.",".$txt_yarn_details.",".$txt_remarks.",".$cbo_fabric_source_dtls.",".$txt_style_dtls_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_delivery_dates.",".$cbouom.",".$cbo_dia_width_type.")";

		//Yarn break down ===================================================================================================
			$separator = '';

			if(str_replace("'",'',$cbo_fabric_source_dtls)==1)
			{
				for($i=1 ; $row_num >= $i; $i++)
				{
					$yarn_dtls_id="yarn_dtls_id_".$i;
					$cbocount="cbocount_".$i;
					$cbocompone="cbocompone_".$i;
					$percentone="percentone_".$i;
					$cbotype="cbotype_".$i;
					$consqnty="consqnty_".$i;
					if($i !=1) $separator = "__";
					$yarnbreackdown_data.= $separator.str_replace("'","",$$cbocount).'_'.str_replace("'","",$$cbocompone).'_100_'.str_replace("'","",$$cbotype).'_'.str_replace("'","",$$percentone);
				}

				$yarnbreckdown_array=explode('__',str_replace("'",'',$yarnbreackdown_data));
				for($c=0;$c < count($yarnbreckdown_array);$c++)
				{
					$yarnbreckdownarr=explode('_',$yarnbreckdown_array[$c]);
					if(str_replace("'",'',$cbo_fabric_natu)==2)
					{
						$cons=(str_replace("'",'',$txt_grey_qnty)*$yarnbreckdownarr[4])/100;
					}
					if(str_replace("'",'',$cbo_fabric_natu)==3)
					{
						$cons=(str_replace("'",'',$txt_gsm)*$yarnbreckdownarr[4])/100;
					}
					if ($add_comma_yarn!=0)
					{
						$data_array2 .=",";

					}
					$data_array2 .="(".$wo_non_ord_samp_yarn_dtls_id.",".$update_id.",".$id.",".$txt_booking_no.",'".$yarnbreckdownarr[0]."','".$yarnbreckdownarr[1]."','".$yarnbreckdownarr[2]."','".$yarnbreckdownarr[3]."','".$yarnbreckdownarr[4]."','".$cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

					//$data_array4 .="(".$wo_pre_cost_fab_yarnbreakdown_id.",".$id.",".$update_id.",".$yarnbreckdownarr[0].",'".$yarnbreckdownarr[1]."','".$yarnbreckdownarr[2]."','".$yarnbreckdownarr[3]."','".$yarnbreckdownarr[4]."','".$cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatus.",0)";
					$wo_non_ord_samp_yarn_dtls_id=$wo_non_ord_samp_yarn_dtls_id+1;
					//$wo_pre_cost_fab_yarnbreakdown_id=$wo_pre_cost_fab_yarnbreakdown_id+1;
					$add_comma_yarn++;
				}
			}
		// Yarn break down end ===============================================================================================


		//echo "10**insert into wo_non_ord_samp_booking_dtls (".$field_array.") Values ".$data_array."";die;
		$flag=1;
		 $rID_in2=1;
		 $rID=sql_insert("wo_non_ord_samp_booking_dtls",$field_array,$data_array,1);
		 if( $rID==1 && $flag==1) $flag=1; else $flag=1;
 		 if ($data_array2!="")
		 {
		 	//echo "10**insert into wo_non_ord_samp_yarn_dtls (".$field_array2.") Values ".$data_array2."";die;
			$rID_in2=sql_insert("wo_non_ord_samp_yarn_dtls",$field_array2,$data_array2,0);
			 if( $rID_in2==1 && $flag==1) $flag=1; else $flag=1;
		 }

		$rID3= execute_query( "update fabric_sales_order_mst set is_apply_last_update=2 where sales_booking_no =".$txt_booking_no." and status_active=1 and is_deleted=0",0);
		if( $rID3==1 && $flag==1) $flag=1; else $flag=1;
		//echo "10**".$rID.'##'.$rID_in2;die;

		 //check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "0**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$id)."**".str_replace("'","",$libyarncountdeterminationid)."**".$yarnbreackdown;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "0**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$id)."**".str_replace("'","",$libyarncountdeterminationid)."**".$yarnbreackdown;
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // update Here
	{
		$con = connect();
		$is_approved=0;

		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		$yarnAllo=sql_select("select id from inv_material_allocation_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		if(count($yarnAllo)>0){
			echo "yarnallocation**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}


		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

		$receive_mrr=0;
		$sqlre=sql_select("select recv_number from inv_receive_master where booking_no=$txt_booking_no and status_active=1 and is_deleted=0 ");
		foreach($sqlre as $rows){
			$receive_mrr=$rows[csf('recv_number')];
		}
		if($receive_mrr){
			echo "rec1**".str_replace("'","",$txt_booking_no)."**".$receive_mrr;
			disconnect($con);die;
		}

		$issue_mrr=0;
		$sqlis=sql_select("select a.issue_number from inv_issue_master a,inv_transaction b where  a.id=b.mst_id and a.booking_no=$txt_booking_no and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.issue_number ");
		foreach($sqlis as $rows){
			$issue_mrr=$rows[csf('issue_number')];
		}
		if($issue_mrr){
			echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
			disconnect($con);die;
		}

		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	    //if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		if($db_type==0)
		{
		$field_array_up="booking_no*style_id*style_des*sample_type*body_part*body_type_id*item_qty*knitting_charge*color_type_id*lib_yarn_count_deter_id*construction*composition*fabric_description*gsm_weight*gmts_color*fabric_color*gmts_size*item_size*dia_width*finish_fabric*process_loss*grey_fabric*rate*amount*bh_qty*rf_qty*yarn_breack_down*process_loss_method*article_no*yarn_details*remarks*fabric_source*dtls_id*updated_by*update_date*delivery_date*uom*width_dia_type";
		 $field_array2="id,wo_non_ord_samp_book_dtls_id,booking_no,count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty,inserted_by,insert_date";
		}
		if($db_type==2)
		{
		$field_array_up="booking_no*style_id*style_des*sample_type*body_part*body_type_id*item_qty*knitting_charge*color_type_id*lib_yarn_count_deter_id*construction*composition*fabric_description*gsm_weight*gmts_color*fabric_color*gmts_size*item_size*dia_width*finish_fabric*process_loss*grey_fabric*rate*amount*bh_qty*rf_qty*yarn_breack_down*process_loss_method*article_no*yarn_details*remarks*fabric_source*dtls_id*updated_by*update_date*delivery_date*uom*width_dia_type";
		 $field_array2="id,wo_non_ord_samp_book_dtls_id,booking_no,count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty,inserted_by,insert_date";
		}
		 $wo_non_ord_samp_yarn_dtls_id=return_next_id( "id", "wo_non_ord_samp_yarn_dtls", 1 ) ;
		 $add_comma_yarn=0;
		//txt_knitting_charge,knitting_charge
			$gmt_color_str = str_replace("'", "", $txt_gmt_color);
			$gmt_color_nam=strtoupper(trim($gmt_color_str));
			$fab_color_str = str_replace("'", "", $txt_color);
			$fab_color_nam=strtoupper(trim($fab_color_str));

			$new_array_gmts_color=array();
			if(str_replace("'","",$txt_gmt_color)!="")
			{
				if (!in_array(str_replace("'","",$txt_gmt_color),$new_array_gmts_color))
				{
					$gmts_color_id = return_id_lib_common( str_replace("'","",$txt_gmt_color), $color_library, "lib_color", "id,color_name","90");
					$new_array_gmts_color[$gmts_color_id]=str_replace("'","",$txt_gmt_color);
				}
				else $gmts_color_id =  array_search(str_replace("'","",$txt_gmt_color), $new_array_gmts_color);
			}
			else $gmts_color_id =0;


			$new_array_color=array();
			if($gmt_color_nam==$fab_color_nam)
			{
				$color_id=$gmts_color_id;
			}
			else
			{
				if(str_replace("'","",$txt_color)!="")
				{
					if (!in_array(str_replace("'","",$txt_color),$new_array_color))
					{
						$color_id = return_id_lib_common( str_replace("'","",$txt_color), $color_library, "lib_color", "id,color_name","90");
						$new_array_color[$color_id]=str_replace("'","",$txt_color);
					}
					else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
				}
				else $color_id =0;
			}
			//echo  "10**".$gmt_color_nam.'='.$fab_color_nam.'='.$gmts_color_id.'='.$color_id;die;




		$new_array_gmts_size=array();
		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_gmts_size),$new_array_gmts_size))
			{
				$gmts_size_id = return_id_lib_common( str_replace("'","",$txt_gmts_size), $size_library, "lib_size", "id,size_name","90");
				$new_array_gmts_size[$gmts_size_id]=str_replace("'","",$txt_gmts_size);
			}
			else $gmts_size_id =  array_search(str_replace("'","",$txt_gmts_size), $new_array_gmts_size);
		 }
		 else $gmts_size_id = 0;

		$data_array_up="".$txt_booking_no."*".$txt_style."*".$txt_style_des."*".$cbo_sample_type."*".$cbo_body_part."*".$cbo_body_type."*".$txt_item_qty."*".$txt_knitting_charge."*".$cbo_color_type."*".$libyarncountdeterminationid."*".$construction."*".$composition."*".$txt_fabricdescription."*".$txt_gsm."*'".$gmts_color_id."'*".$color_id."*'".$gmts_size_id."'*".$txt_size."*".$txt_dia_width."*".$txt_finish_qnty."*".$txt_process_loss."*".$txt_grey_qnty."*".$txt_rate."*".$txt_amount."*".$txt_bh_qty."*".$txt_rf_qty."*".$yarnbreackdown."*".$process_loss_method."*".$txt_article_no."*".$txt_yarn_details."*".$txt_remarks."*".$cbo_fabric_source_dtls."*".$txt_style_dtls_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_delivery_dates."*".$cbouom."*".$cbo_dia_width_type."";
		//echo $data_array_up; die;
		//Yarn break down ===================================================================================================
		$separator = '';
		//echo "10**".$yarnbreackdown_new; die;
		if(str_replace("'",'',$cbo_fabric_source_dtls)==1)
		{
			$rID_in2=execute_query( "delete from wo_non_ord_samp_yarn_dtls where  wo_non_ord_samp_book_dtls_id =".$update_id_details."",0);
			for($i=1 ; $row_num >= $i; $i++)
			{
				$yarn_dtls_id="yarn_dtls_id_".$i;
				$cbocount="cbocount_".$i;
				$cbocompone="cbocompone_".$i;
				$percentone="percentone_".$i;
				$cbotype="cbotype_".$i;
				$consqnty="consqnty_".$i;
				if($i !=1) $separator = "__";
				$yarnbreackdown_data.= $separator.str_replace("'","",$$cbocount).'_'.str_replace("'","",$$cbocompone).'_100_'.str_replace("'","",$$cbotype).'_'.str_replace("'","",$$percentone);
			}
			$yarnbreckdown_array=explode('__',$yarnbreackdown_data);
			for($c=0;$c < count($yarnbreckdown_array);$c++)
			{
				$yarnbreckdownarr=explode('_',$yarnbreckdown_array[$c]);
				if(str_replace("'",'',$cbo_fabric_natu)==2)
				{
					$cons=(str_replace("'",'',$txt_grey_qnty)*$yarnbreckdownarr[4])/100;
				}
				if(str_replace("'",'',$cbo_fabric_natu)==3)
				{
					$cons=(str_replace("'",'',$txt_gsm)*$yarnbreckdownarr[4])/100;
				}
				if ($add_comma_yarn!=0)
				{
					$data_array2 .=",";
				}
				if($yarnbreckdownarr[0]) $yarnbreckdownarr[0]=$yarnbreckdownarr[0];else $yarnbreckdownarr[0]=0;
				$data_array2 .="(".$wo_non_ord_samp_yarn_dtls_id.",".$update_id_details.",".$txt_booking_no.",".$yarnbreckdownarr[0].",'".$yarnbreckdownarr[1]."','".$yarnbreckdownarr[2]."','".$yarnbreckdownarr[3]."','".$yarnbreckdownarr[4]."','".$cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				$wo_non_ord_samp_yarn_dtls_id=$wo_non_ord_samp_yarn_dtls_id+1;
				$add_comma_yarn++;
			}
		}
		// Yarn break down end ===============================================================================================


		//=========================================collar & Cuff start=======================================================================

	    	$booking=str_replace("'","",$txt_booking_no);
			$collarCuff_data=explode(",",str_replace("'",'',$hidden_collarCuff_data));
			$collar_cuff_ids=explode(",",str_replace("'",'',$hidden_collar_cuff_ids));

			if(count($collar_cuff_ids)>0){
				foreach($collar_cuff_ids as $val){
					$delete_field_array="updated_by*update_date*status_active*is_deleted";
					$delete_data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
					$ccID=sql_delete("wo_booking_colar_culff_dtls",$delete_field_array,$delete_data_array,"id","".$val."",1);
				}
			}
			// echo $hidden_collarCuff_data;

		$cc_id=return_next_id( "id", "wo_booking_colar_culff_dtls",1);
		$cc_field_array="id,booking_no, wo_non_ord_samp_booking_dtl_id,body_part_id,gmts_size, item_size,qty,inserted_by,insert_date";

		$cc_data_array="";
		foreach($collarCuff_data as $val){
	        $cc_data=explode("_",$val);
			// echo "<pre>";
			// print_r($cc_data);
			if ($cc_data_array!="") $cc_data_array .=",";
			$cc_data_array .="(".$cc_id.",'".trim($booking)."',".$cc_data[4].",".$cc_data[0].",'".$cc_data[1]."','".$cc_data[2]."',".$cc_data[3].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$cc_id=$cc_id+1;
		}

		$rID_cc=sql_insert("wo_booking_colar_culff_dtls",$cc_field_array,$cc_data_array,0);
		//    echo "10**insert into wo_booking_colar_culff_dtls (".$cc_field_array.") values ".$cc_data_array; die; // mamun;
		//=========================================collar & Cuff end=======================================================================





		$flag=1;
	    $rID=sql_update("wo_non_ord_samp_booking_dtls",$field_array_up,$data_array_up,"id","".$update_id_details."",0);
		 if( $rID==1 && $flag==1) $flag=1; else $flag=1;
	    $rID_in2 =1;
		if ($data_array2!="")
		 {
		 	// echo "10**Insert into wo_non_ord_samp_booking_dtls ($field_array_up) values $data_array2"; die;
			$rID_in2=sql_insert("wo_non_ord_samp_yarn_dtls",$field_array2,$data_array2,0);
			if( $rID_in2==1 && $flag==1) $flag=1; else $flag=1;
		 }
		 $rID3= execute_query( "update fabric_sales_order_mst set is_apply_last_update=2 where sales_booking_no =".$txt_booking_no." and status_active=1 and is_deleted=0",0);
		 if( $rID3==1 && $flag==1) $flag=1; else $flag=1;
		//echo  "10**".$rID; die;
	    //check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id_details)."**".$libyarncountdeterminationid."**".$yarnbreackdown;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id_details)."**".$libyarncountdeterminationid."**".$yarnbreackdown;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id_details)."**".$libyarncountdeterminationid."**".$yarnbreackdown;
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id_details)."**".$libyarncountdeterminationid."**".$yarnbreackdown;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Insert Here
	{
		$con = connect();
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3) $is_approved=1; else $is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		$yarnAllo=sql_select("select id from inv_material_allocation_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		if(count($yarnAllo)>0){
			echo "yarnallocation**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}

		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id and b.work_order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

		$pplbook=0;
		$ppl=sql_select("select b.id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.booking_no=$txt_booking_no and a.is_sales!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id");
		foreach($ppl as $pplrow){
			$pplbook=$pplrow[csf('id')];
		}

		if($pplbook!=0){
			echo "PPL**".str_replace("'","",$txt_booking_no)."**".$pplbook;
			disconnect($con);die;
		}

		$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			disconnect($con);die;
		}

		$receive_mrr=0;
		$sqlre=sql_select("select recv_number from inv_receive_master where booking_no=$txt_booking_no");
		foreach($sqlre as $rows){
			$receive_mrr=$rows[csf('recv_number')];
		}
		if($receive_mrr){
			echo "rec1**".str_replace("'","",$txt_booking_no)."**".$receive_mrr;
			disconnect($con);die;
		}

		$issue_mrr=0;
		//$sqlis=sql_select("select issue_number from inv_issue_master where booking_no=$txt_booking_no");
		$sqlis=sql_select("select a.issue_number from inv_issue_master a,inv_transaction b where  a.id=b.mst_id and a.booking_no=$txt_booking_no and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.issue_number ");
		foreach($sqlis as $rows){
			$issue_mrr=$rows[csf('issue_number')];
		}
		if($issue_mrr){
			echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
			disconnect($con);die;
		}


		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$flag=1;

		$rID2=sql_delete("wo_non_ord_samp_booking_dtls",$field_array,$data_array,"id","".$update_id_details."",1);
		if($rID2==1) $flag=1; else $flag=0;
		$rID3=sql_delete("wo_non_ord_samp_yarn_dtls",$field_array,$data_array,"wo_non_ord_samp_book_dtls_id","".$update_id_details."",1);
		if($rID3==1) $flag=1; else $flag=0;

		//$rID=execute_query( "update wo_non_ord_samp_booking_dtls set status_active=0,is_deleted =1 where  id =$update_id_details",0);
		//$rID_de2=execute_query( "delete from wo_non_ord_samp_yarn_dtls where  wo_non_ord_samp_book_dtls_id =".$update_id_details."",0);
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="fabric_booking_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
  	extract($_REQUEST);
	?>
	<script>
		var company="<? echo $company; ?>";
		$('#cbo_company_mst').val(company);
		function js_set_value(booking_no)
		{
			document.getElementById('selected_booking').value=booking_no;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
       <thead>
            <th colspan="7">
              <?
               echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
              ?>
            </th>
         </thead>
        <thead>
            <th width="150" class="must_entry_caption">Company Name</th>
            <th width="150">Buyer Name</th>
            <th width="100">Booking No</th>
            <th width="80">Style Desc.</th>
            <th width="150" colspan="2">Date Range</th>
            <th>&nbsp;</th>
        </thead>
        <tr class="general">
            <td> <input type="hidden" id="selected_booking">
                <?
                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "--Select Company--", $company, "load_drop_down( 'sample_booking_non_order_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_td');");
                ?>
            </td>
        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>

        <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
        <td><input name="txt_style_desc" id="txt_style_desc" class="text_boxes" style="width:80px"></td>
        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"></td>
        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"></td>
         <td align="center">
            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_desc').value, 'create_booking_search_list_view', 'search_div', 'sample_booking_non_order_controller','setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" /></td>
    </tr>
    <tr>
        <td align="center" colspan="7" valign="middle"><?=load_month_buttons(1); ?></td>
    </tr>
 </table>
    <div id="search_div"></div>
    </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$style_desc=$data[7];
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if($style_desc=="" && trim($data[5])=="" && (trim($data[2])=="" && trim($data[3])=="")) { echo "Please Select Date Range."; die; }
	if ($data[1]!=0){$buyer=" and a.buyer_id='$data[1]'";}
	else{$buyer="";}

	if($db_type==0)
	 {
		  $booking_year_cond=" and YEAR(a.insert_date)=$data[4]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
     }
	else if($db_type==2)
	 {
		  $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	 }
	if($data[6]==4 || $data[6]==0)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
			if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '%$data[7]%' "; else $style_des_cond="";
		}
    else if($data[6]==1)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[5]'   "; else $booking_cond="";
			if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des='$data[7]' "; else $style_des_cond="";
		}
   else if($data[6]==2)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
			if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '$data[7]%' "; else $style_des_cond="";
		}
	else if($data[6]==3)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
			if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '%$data[7]' "; else $style_des_cond="";
		}
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
    $approved=array(0=>"No",1=>"Yes",3=>"Yes");
    $is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');

	$sql= "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, a.pay_mode, b.style_id, b.style_des,b.fabric_source as fabric_source_dtls from wo_non_ord_samp_booking_mst a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no and b.status_active=1 and b.is_deleted=0 where $company". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer $booking_date $booking_cond $style_des_cond and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and( a.entry_form_id is null or a.entry_form_id =90 ) order by a.id DESC";
	//echo $sql;
	?>
    <table class="rpt_table scroll" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all" >
    	<thead>
            <th width="30">Sl</th>
            <th width="100">Booking No</th>
            <th width="80">Booking Date</th>
            <th width="100">Company</th>
            <th width="100">Buyer</th>
            <th width="80">Fabric Nature</th>
            <th width="80">Fabric Source</th>
            <th width="80">Pay Mode</th>
            <th width="100">Supplier</th>
            <th width="50">Style</th>
            <th width="80">Style Desc.</th>
            <th width="50">Approved</th>
            <th>Is-Ready</th>
        </thead>
    </table>
    <div style="max-height:300px; overflow-y:scroll; width:1000px" >
    <table width="980" class="rpt_table" id="tbl_list_search" border="1" rules="all">
        <tbody>
		<?
        $i=1;
        $sql_data=sql_select($sql);
        foreach($sql_data as $row)
        {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($row[csf('fabric_source')]!=0)
			{
				$fabric_source_txt = $fabric_source[$row[csf('fabric_source')]];
			}
			else
			{
				$fabric_source_txt = $fabric_source[$row[csf('fabric_source_dtls')]];
			}
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>');" style="cursor:pointer">
                <td width="30"><? echo $i;?></td>
                <td width="100"><? echo $row[csf('booking_no_prefix_num')];?></td>
                <td width="80"><? echo date("d-m-Y",strtotime($row[csf('booking_date')]));?></td>
                <td width="100"><? echo $comp[$row[csf('company_id')]];?></td>
                <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
                <td width="80"><? echo $item_category[$row[csf('item_category')]];?></td>
                <td width="80"><? echo $fabric_source_txt;?></td>
                <td width="80"><? echo $pay_mode[$row[csf('pay_mode')]];?></td>
                <td width="100">
                <?
                if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5) echo $comp[$row[csf('supplier_id')]];
                else echo $suplier[$row[csf('supplier_id')]];
                ?>
                </td>
                <td width="50" style="word-wrap: break-word;word-break: break-all;"><? echo $style_library[$row[csf('style_id')]];?></td>
                <td width="80" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('style_des')];?></td>
                <td width="50"><? echo $approved[$row[csf('is_approved')]];?></td>
                <td><? echo $is_ready[$row[csf('ready_to_approved')]];?></td>
			</tr>
			<?
			$i++;
        }
        ?>
        </tbody>
    </table>
    </div>
    <?
	exit();
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
function add_break_down_tr(i)
 {
	var row_num=$('#tbl_termcondi_details tr').length-1;
	if (row_num!=i)
	{
		return false;
	}
	else
	{
		i++;

		 $("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }
			});
		  }).end().appendTo("#tbl_termcondi_details");
		 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
		  $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
		  $('#termscondition_'+i).val("");
	}

}

function fn_deletebreak_down_tr(rowNo)
{
	var numRow = $('table#tbl_termcondi_details tbody tr').length;
	if(numRow==rowNo && rowNo!=1)
	{
		$('#tbl_termcondi_details tbody tr:last').remove();
	}
}

function fnc_fabric_booking_terms_condition( operation )
{
	    var row_num=$('#tbl_termcondi_details tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{

			if (form_validation('termscondition_'+i,'Term Condition')==false)
			{
				return;
			}

			data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"");
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","sample_booking_non_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
}

function fnc_fabric_booking_terms_condition_reponse()
{

	if(http.readyState == 4)
	{
	    var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{
				parent.emailwindow.hide();
			}
	}
}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<? echo load_freeze_divs ("../../../",$permission);  ?>
<fieldset>
        	<form id="termscondi_1" autocomplete="off">
           <input type="text" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>"/>
            <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form = 90 and  booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                    </td>
                                </tr>
                            <?
						}
					}
					else
					{
					$data_array=sql_select("select id, terms from  lib_terms_condition  where is_default=1");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
                                    </td>
                                </tr>
                    <?
						}
					}
					?>
                </tbody>
                </table>

                <table width="650" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
						        <?
									echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ;
									?>
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="save_update_delete_fabric_booking_terms_condition")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,terms";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		 }
		// echo  $data_array;
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."",0);

		 $rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		 //check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0];
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="save_update_delete_trims_acc")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		 $id=return_next_id( "id", "wo_non_ord_booking_acc_dtls", 1 ) ;
		 $field_array="id,booking_no,booking_mst_id,item_group_id,description,uom,qty,remarks";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $trim_group="itemgroup_".$i;
			 $description="description_".$i;
			 $cons_uom="uom_".$i;
			 $qty="qty_".$i;
			 $remarks="remarks_".$i;

			if ($i!=1) $data_array .=",";
			$data_array.="(".$id.",".$txt_booking_no.",".$update_id.",".$$trim_group.",".$$description.",".$$cons_uom.",".$$qty.",".$$remarks.")";
			$id=$id+1;
		 }
		// echo  $data_array;
		$rID_de=execute_query( "delete from wo_non_ord_booking_acc_dtls where  booking_no =".$txt_booking_no."",0);

		 $rID=sql_insert("wo_non_ord_booking_acc_dtls",$field_array,$data_array,1);
		 //check_table_status( $_SESSION['menu_id'],0);

		//echo "10**".$data_array;die;

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0];
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="populate_data_from_search_popup")
{
	 $sql= "select id, booking_no, booking_date, company_id, buyer_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, booking_month, supplier_id, attention, delivery_date, source, booking_year, is_approved, ready_to_approved, team_leader,	dealing_marchant, copy_from, uom_id from wo_non_ord_samp_booking_mst where booking_no='$data'";

	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		echo "load_drop_down( 'requires/sample_booking_non_order_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "load_drop_down( 'requires/sample_booking_non_order_controller', '".$row[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant' );\n";
		echo "load_drop_down( 'requires/sample_booking_non_order_controller', '".$row[csf("buyer_id")]."', 'cbo_sample_type', 'sampletd' );\n";
		echo "load_drop_down( 'requires/sample_booking_non_order_controller', '".$row[csf("pay_mode")]."', 'load_drop_down_suplier', 'sup_td' );\n";
        echo "color_from_library('".$row[csf("company_id")]."');\n";
		//echo "document.getElementById('txt_order_no_id').value = '".$row[csf("po_break_down_id")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('cbouom_id').value = '".$row[csf("uom_id")]."';\n";
		//echo "document.getElementById('txt_job_no').value = '".$row[csf("j_no")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_fabric_natu').value = '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_copy_from_booking').value = '".$row[csf("copy_from")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		//echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		if($row[csf("is_approved")]==3){
			$is_approved=1;
		}else{
			$is_approved=$row[csf("is_approved")];
		}
		//echo "$('#cbo_fabric_source').attr('disabled','disabled')";//
		echo "document.getElementById('id_approved_id').value = '".$is_approved."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";

		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";

		if($row[csf("is_approved")]==1)
		{
			//echo "document.getElementById('app_sms').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved.';\n";
		}
		else if($row[csf("is_approved")]==3)//ISD-22-05730 by kausar
		{
			//echo "document.getElementById('app_sms').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is partial approved.';\n";
		}
		else
		{
			//echo "document.getElementById('app_sms').innerHTML = '';\n";
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
		}
		//echo "enable_disable('".$row[csf("fabric_source")]."');\n";
		//echo "enable_disable2(1);\n";
		//echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
	 }
}

if($action == "yarn_cost_dtls")
{
	$data = explode('**', $data);
	$yarnbreackdown = $data[0];
	$dtls_id = $data[1];
	$yarncountid = $data[2];
	$oldyarncountid = $data[3];
	$yarn_dtls = array();
	if($dtls_id != '')
	{
		$yarn_dtls = sql_select("SELECT id, booking_no, wo_non_ord_samp_book_dtls_id, count_id,copm_one_id, cons_ratio, type_id, cons_qnty from wo_non_ord_samp_yarn_dtls where is_deleted=0 and status_active=1 and wo_non_ord_samp_book_dtls_id =".$dtls_id."");
	}
	//print_r($ommitYarnType).'ffdddd';;

	?>
	<fieldset style="width:780px;">
		<table width="780" cellspacing="0" class="rpt_table" border="0" id="tbl_yarn_cost" rules="all">
			<thead>
		    	<tr>
		        	<th width="60">Count</th>
		            <th width="100" class="must_entry_caption">Comp.</th>
		            <th width="50" class="must_entry_caption">%</th>
		            <th width="110">Type</th>
		            <th width="75" class="must_entry_caption">Cons Qnty</th>
		            </th>
		        </tr>
		    </thead>
		    <tbody>
	<?
	$i=1;
	if($dtls_id == '' || ($dtls_id != '' && count($yarn_dtls) ==0 ) || ($dtls_id !='' && count($yarn_dtls)>0 && ($yarncountid != $oldyarncountid)))
	{
		$yarnbreackdown = explode('__', $yarnbreackdown);
		foreach ($yarnbreackdown as $yarnData) {
			$yarnData = explode('_', $yarnData);
		?>
		<tr id="yarncost_<? echo $i; ?>" align="center">
                <td>
                <? echo create_drop_down( "cbocount_".$i, 60, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select Item --", $yarnData[0]); ?>
                	<input type="hidden" name="yarn_dtls_id_<? echo $i?>" id="yarn_dtls_id_<? echo $i?>" value="">
                </td>
                <td><? echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", $yarnData[1], "control_composition($i,this.id,'percent_one')",1,"","","",$ommitComposition ); ?></td>
               <td><input type="text" id="percentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes" style="width:40px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_one')" value="<? echo $yarnData[4];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?>  readonly/>
                </td>

                <td><? echo create_drop_down( "cbotype_".$i, 110, $yarn_type,"", 1, "-- Select --", $yarnData[3], "",$disabled,"","","","" ); ?></td>
                <td>
                    <input type="text" id="consqnty_<? echo $i; ?>" name="consqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" onChange=" calculate_yarn_consumption_ratio(<? echo $i; ?>,'calculate_amount')"  value="" <? if($disabled==0){echo "";}else{echo "disabled";}?> readonly />
                </td>
            </tr>
		<?
		$i++;
		}
	}
	else
	{
		foreach ($yarn_dtls as $yarnData) {
		?>
		<tr id="yarncost_<? echo $i; ?>" align="center">
                <td>
               <? echo create_drop_down( "cbocount_".$i, 60, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select Item --", $yarnData[csf('count_id')],""); ?>
                <input type="hidden" name="yarn_dtls_id_<? echo $i?>" id="yarn_dtls_id_<? echo $i?>" value="<? echo $yarnData[csf('id')]; ?>">
                </td>
                <td><? echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", $yarnData[csf('copm_one_id')], "",1,"","","",$ommitComposition ); ?></td>
               <td><input type="text" id="percentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes" style="width:40px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_one')" value="<? echo $yarnData[csf('cons_ratio')];  ?>" readonly/>
                </td>

                <td><? echo create_drop_down( "cbotype_".$i, 110, $yarn_type,"", 1, "-- Select --", $yarnData[csf('type_id')], "",$disabled,"","","",$ommitYarnType ); ?></td>
                <td>
                    <input type="text" id="consqnty_<? echo $i; ?>" name="consqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $yarnData[csf('cons_qnty')] ?>"  readonly />
                </td>
            </tr>
		<?
		$i++;
		}
	}
	?>
			<tbody>
		</table>
	</fieldset>
	<?
}

if($action=="populate_details_data_from_for_update")
{
	$data_array=sql_select("select id, body_part, booking_no,body_type_id, item_qty, knitting_charge, style_id, style_des, sample_type, color_type_id, lib_yarn_count_deter_id as lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, gmts_color, fabric_color, gmts_size, item_size, dia_width, finish_fabric, process_loss, grey_fabric, rate, amount, yarn_breack_down, process_loss_method, article_no, yarn_details, remarks, bh_qty, rf_qty, fabric_source, dtls_id, delivery_date, uom, width_dia_type FROM wo_non_ord_samp_booking_dtls WHERE id ='".$data."'  and status_active=1 and	is_deleted=0");

	foreach ($data_array as $row)
	{
		$style_id=$row[csf("style_id")];
		$booking_no=$row[csf("booking_no")];
		$style=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
		echo "document.getElementById('cbo_body_part').value = '".$row[csf("body_part")]."';\n";
		echo "document.getElementById('hidden_bodypartID_data').value = '".$row[csf("body_part")]."';\n";
		echo "document.getElementById('cbo_color_type').value = '".$row[csf("color_type_id")]."';\n";
		echo "document.getElementById('txt_style').value = '".$row[csf("style_id")]."';\n";
		echo "document.getElementById('txt_style_no').value = '".$style."';\n";
		echo "document.getElementById('txt_style_des').value = '".$row[csf("style_des")]."';\n";
		echo "document.getElementById('cbo_sample_type').value = '".$row[csf("sample_type")]."';\n";
		echo "document.getElementById('oldlibyarncountdeterminationid').value = '".$row[csf("lib_yarn_count_deter_id")]."';\n";
		echo "document.getElementById('libyarncountdeterminationid').value = '".$row[csf("lib_yarn_count_deter_id")]."';\n";
		echo "document.getElementById('construction').value = '".$row[csf("construction")]."';\n";
		echo "document.getElementById('composition').value = '".$row[csf("composition")]."';\n";
		echo "document.getElementById('yarnbreackdown').value = '".$row[csf("yarn_breack_down")]."';\n";
		echo "document.getElementById('txt_fabricdescription').value = '".$row[csf("fabric_description")]."';\n";
		echo "document.getElementById('txt_gsm').value = '".$row[csf("gsm_weight")]."';\n";
		echo "document.getElementById('txt_gmt_color').value = '".$color_library[$row[csf("gmts_color")]]."';\n";
		echo "document.getElementById('txt_color').value = '".$color_library[$row[csf("fabric_color")]]."';\n";
		echo "document.getElementById('txt_gmts_size').value = '".$size_library[$row[csf("gmts_size")]]."';\n";
		echo "document.getElementById('txt_size').value = '".$row[csf("item_size")]."';\n";
		echo "document.getElementById('txt_dia_width').value = '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('txt_finish_qnty').value = '".$row[csf("finish_fabric")]."';\n";
		echo "document.getElementById('txt_process_loss').value = '".$row[csf("process_loss")]."';\n";
		echo "document.getElementById('txt_grey_qnty').value = '".$row[csf("grey_fabric")]."';\n";
		echo "document.getElementById('txt_knitting_charge').value = '".$row[csf("knitting_charge")]."';\n";
		echo "document.getElementById('txt_rate').value = '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_amount').value = '".$row[csf("amount")]."';\n";
		echo "document.getElementById('update_id_details').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('process_loss_method').value = '".$row[csf("process_loss_method")]."';\n";
		echo "document.getElementById('txt_article_no').value = '".$row[csf("article_no")]."';\n";
		echo "document.getElementById('txt_yarn_details').value = '".$row[csf("yarn_details")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_body_type').value = '".$row[csf("body_type_id")]."';\n";
		echo "document.getElementById('txt_item_qty').value = '".$row[csf("item_qty")]."';\n";
		echo "document.getElementById('txt_delivery_dates').value = '".change_date_format($row[csf("delivery_date")])."';\n";

		echo "document.getElementById('txt_bh_qty').value = '".$row[csf("bh_qty")]."';\n";
		echo "document.getElementById('txt_rf_qty').value = '".$row[csf("rf_qty")]."';\n";
		echo "document.getElementById('cbo_fabric_source_dtls').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('txt_style_dtls_id').value = '".$row[csf("dtls_id")]."';\n";
		echo "document.getElementById('cbouom').value = '".$row[csf("uom")]."';\n";
		echo "document.getElementById('cbo_dia_width_type').value = '".$row[csf("width_dia_type")]."';\n";
		//echo "$('#cbouom').attr('disabled',true);\n";

		$fabric_sourceId = return_field_value("fabric_source","wo_non_ord_samp_booking_mst","booking_no='$booking_no'");

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_fabric_booking_dtls',2);\n";
		$yarn_dtls = $row[csf("yarn_breack_down")].'**'.$row[csf("id")].'**'.$row[csf("lib_yarn_count_deter_id")].'**'.$row[csf("lib_yarn_count_deter_id")];
		//if($row[csf("fabric_source")] ==1)
			if($fabric_sourceId!=2)//Buyer Supplier
			{
			echo "show_list_view('".$yarn_dtls."','yarn_cost_dtls','yarn_cost_dtls','requires/sample_booking_non_order_controller','');\n";
		}
		else{
			echo "$('#yarn_cost_dtls').html('');\n";
		}
	}
		//echo "enable_disable2(2);\n";
	//echo "$('#cbo_fabric_source_dtls').attr('disabled',false);\n";
	if($style !='')
	{
		echo "$('#txt_article_no').attr('disabled','true');\n";
		echo "$('#txt_bh_qty').attr('disabled','true');\n";
		echo "$('#txt_rf_qty').attr('disabled','true');\n";
		echo "$('#txt_gmts_size').attr('disabled','true');\n";
		echo "$('#cbo_sample_type').attr('disabled','true');\n";
		echo "$('#txt_gmt_color').attr('disabled','true');\n";
		echo "$('#txt_article_no').attr('disabled','true');\n";
	}
	echo "$('#txt_fabricdescription').attr('title', 'Yarn Count Determination ID: ".$row[csf("lib_yarn_count_deter_id")]."' );\n";

	exit();
}

if($action=="acc_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//$lib_item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name", "id", "item_name");
	 //$lib_item_uom_arr=return_library_array( "select trim_uom,id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by trim_uom", "id", "trim_uom");

	?><script> var trim_uom_arr=Array(); </script><?

	$itemArray=sql_select( "select item_name,trim_uom,id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name" );
	foreach ($itemArray as $row)
	{
	?>
		<script> trim_uom_arr[<? echo $row[csf('id')];?>]='<? echo $row[csf('trim_uom')].'*'.$unit_of_measurement[$row[csf('trim_uom')]];?>' </script>
	<?
		$lib_item_group_arr[$row[csf('id')]]=$row[csf('item_name')];
	}


?>
	<script>

function load_trims_uom(item_group,str){
	var uom=trim_uom_arr[item_group].split('*');
	var html="<option value='"+uom[0]+"'>"+uom[1]+"</option>";
	document.getElementById('uom_'+str).innerHTML=html;
}


function add_break_down_tr(i)
 {
	var row_num=$('#tbl_accessories_details tr').length-1;
	if (row_num!=i)
	{
		return false;
	}
	else
	{
		i++;

		 $("#tbl_accessories_details tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return '' }
			});
		  }).end().appendTo("#tbl_accessories_details");

		 $('#itemgroup_'+i).removeAttr("onChange").attr("onChange","load_trims_uom(this.value,"+i+");");

		 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
		 $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");

		 $('#increase_'+i).removeAttr("value").attr("value","+");
		 $('#decrease_'+i).removeAttr("value").attr("value","-");


		 $('#termscondition_'+i).val("");
		 $('#tbl_accessories_details tbody tr:last td:first-child').text(i);
	}

}

function fn_deletebreak_down_tr(rowNo)
{

		var numRow = $('table#tbl_accessories_details tbody tr').length;
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_accessories_details tbody tr:last').remove();
		}

}


function fnc_trims_acc( operation )
{
	    var row_num=$('#tbl_accessories_details tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{


			data_all=data_all+get_submitted_data_string('txt_booking_no*update_id*itemgroup_'+i+'*description_'+i+'*uom_'+i+'*qty_'+i+'*remarks_'+i,"");
		}
		var data="action=save_update_delete_trims_acc&operation="+operation+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","sample_booking_non_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_trims_acc_reponse;
}

function fnc_trims_acc_reponse()
{

	if(http.readyState == 4)
	{
	    var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{
				parent.emailwindow.hide();
			}
	}
}


    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<? echo load_freeze_divs ("../../../",$permission);  ?>
<fieldset>
    <form id="termscondi_1" autocomplete="off">
    <input type="hidden" id="txt_booking_no"   name="txt_booking_no"  value="<? echo str_replace("'","",$txt_booking_no);?>" />
    <input type="hidden" id="update_id"   name="update_id"  value="<? echo str_replace("'","",$update_id);?>"  />
    <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_accessories_details" rules="all">
            <thead>
                <tr>
                    <th width="30">Sl</th>
                    <th width="100">Item Group</th>
                    <th width="100">Description</th>
                    <th width="70">UOM</th>
                    <th width="70">Qty</th>
                    <th>Remarks</th>
                    <th width="80"></th>
                </tr>
            </thead>
            <tbody>
            <?

           $sql = "select booking_no,qty,item_group_id,description, uom,remarks from wo_non_ord_booking_acc_dtls where booking_no=".$txt_booking_no."";

            $data_array=sql_select($sql);

            if ( count($data_array)>0)
            {

                $i=0;
                foreach( $data_array as $row )
                {
                    $i++;
                    ?>
                        <tr id="settr_1" align="center">
                            <td><? echo $i;?></td>
                            <td>
                            <?
                            echo create_drop_down( "itemgroup_$i", 95, $lib_item_group_arr,"", 1, "-- Select --", $row[csf('item_group_id')], "load_trims_uom(this.value,$i)",0,"" );
                            ?>
                            </td>

                            <td>
                            <input type="text" id="description_<? echo $i;?>"   name="description_<? echo $i;?>" style="width:80%"  class="text_boxes"  value="<? echo $row[csf('description')]; ?>"  />
                            </td>

                            <td id="uom_td_<? echo $i;?>">
                            <?
                            echo create_drop_down( "uom_$i", 65, $unit_of_measurement,"", 1, "-- Select --", $row[csf('uom')], "",1,$row[csf('uom')] );
                            ?>
                            </td>

                            <td>
                            <input type="text" id="qty_<? echo $i;?>"   name="qty_<? echo $i;?>" style="width:80%"  class="text_boxes_numeric"  value="<? echo$row[csf('qty')];?>"  />
                            </td>

                            <td>
                            <input type="text" id="remarks_<? echo $i;?>"   name="remarks_<? echo $i;?>" style="width:90%"  class="text_boxes"  value="<? echo$row[csf('remarks')];?>"  />
                            </td>


                            <td>
                            <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                            <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />

                            </td>
                        </tr>
                    <?
                }
            }
            else
            {

                $i=1;
                    ?>
                        <tr id="settr_1" align="center">
                            <td><? echo $i;?></td>
                            <td>
                            <?
                            echo create_drop_down( "itemgroup_$i", 95, $lib_item_group_arr,"", 1, "-- Select --", $row[csf('trim_group')], "load_trims_uom(this.value,$i)",0,"" );
                            ?>
                            </td>

                            <td>
                            <input type="text" id="description_<? echo $i;?>"   name="description_<? echo $i;?>" style="width:80%"  class="text_boxes"  value="<? echo $row[csf('description')]; ?>"  />
                            </td>

                            <td id="uom_td_<? echo $i;?>">
                            <?
                            echo create_drop_down( "uom_$i", 65, $unit_of_measurement,"", 1, "-- Select --", $row[csf('cons_uom')], "",0,$row[csf('cons_uom')] );
                            ?>
                            </td>

                            <td>
                            <input type="text" id="qty_<? echo $i;?>"   name="qty_<? echo $i;?>" style="width:80%"  class="text_boxes_numeric"  value=""  />
                            </td>

                            <td>
                            <input type="text" id="remarks_<? echo $i;?>"   name="remarks_<? echo $i;?>" style="width:90%"  class="text_boxes"  value=""  />
                            </td>


                            <td>
                            <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                            <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />

                            </td>
                        </tr>
                    <?
            }
            ?>
        </tbody>
        </table>

        <table width="650" cellspacing="0" class="" border="0">
            <tr>
                <td align="center" height="15" width="100%"> </td>
            </tr>
            <tr>
                <td align="center" width="100%" class="button_container">
                       <?
                        echo load_submit_buttons( $permission, "fnc_trims_acc", 0,0 ,"reset_form('termscondi_1','','','','')",1) ;
                        ?>
                </td>
            </tr>
        </table>
    </form>
</fieldset>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=='check_dtls_part'){
	list($status,$bookingId)=explode('_',str_replace("'","",$data));

	if($status==1){
		if($bookingId==''){
			echo "alert('Without detail part save ,ready to approved request not allowed.');";
			echo "document.getElementById('is_found_dtls_part').value = '0';\n";
			exit();
			}
		$booking_id=return_field_value("booking_no","wo_non_ord_samp_booking_dtls","booking_no ='".$bookingId."' and is_deleted=0 and status_active=1");
		if($booking_id!=$bookingId){
			echo "alert('Without detail part save ,ready to approved request not allowed.');";
			echo "document.getElementById('is_found_dtls_part').value = '0';\n";
			exit();
			}
			else
			{
				echo "document.getElementById('is_found_dtls_part').value = '1';\n";
			}
	}
	else
	{
		echo "document.getElementById('is_found_dtls_part').value = '1';\n";
	}
exit();
}

if ($action == "collarCuff_info_popup")
{
	echo load_html_head_contents("Collar & Cuff Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function add_break_down_tr(i) {
			var row_num = $('#txt_tot_row').val();
			row_num++;

			var clone = $("#tr_" + i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});

			clone.find("input,select").each(function () {
				$(this).attr({
					'id': function (_, id) {
						var id = id.split("_");
						return id[0] + "_" + row_num
					},
					'name': function (_, name) {
						return name
					},
					'value': function (_, value) {
						return value
					}
				});

            }).end();//.appendTo("#tbl_list_search")

			$("#tr_" + i).after(clone);

			$('#txtGrey_' + row_num).removeAttr("value").attr("value", "");
			$('#txtFinish_' + row_num).removeAttr("value").attr("value", "");


			$('#txtQtyPcs_' + row_num).removeAttr("value").attr("value", "");
			$('#txtQtyPcs_' + row_num).removeAttr("onKeyUp").attr("onKeyUp", "calculate_tot_qnty(" + row_num + ");");

			$('#increase_' + row_num).removeAttr("value").attr("value", "+");
			$('#decrease_' + row_num).removeAttr("value").attr("value", "-");
			$('#increase_' + row_num).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + row_num + ");");
			$('#decrease_' + row_num).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + row_num + ");");

			$('#txt_tot_row').val(row_num);
			reArrangeSl();
			set_all_onclick();
		}

		function reArrangeSl() {
			var i = 0;
			$("#tbl_list_search").find('tbody tr').each(function () {
				i++;
				$(this).find("td:eq(0)").text(i);
			});
		}

		function fn_deleteRow(rowNo) {
			if (rowNo != 1) {
				$("#tr_" + rowNo).remove();
				reArrangeSl();
				calculate_tot_qnty();
			}
		}

		function fnc_close() {
			var save_string = "";
			var breakOut = true;
			$("#tbl_list_search").find('tbody tr').each(function () {
				if (breakOut == false) {
					return;
				}

				var bodyPartId = $(this).find('input[name="bodyPartId[]"]').val();
				var txtGrey = $(this).find('input[name="txtGrey[]"]').val();
				var txtFinish = $(this).find('input[name="txtFinish[]"]').val();
				var txtQtyPcs = $(this).find('input[name="txtQtyPcs[]"]').val() * 1;
				var updateDtlsId = $(this).find('input[name="updateDtlsId[]"]').val();


				if (txtQtyPcs < 1) {
					alert("Please Insert Qty. Pcs");
					$(this).find('input[name="txtQtyPcs[]"]').focus();
					breakOut = false;
					return false;
				}

				if (save_string == "") {
					save_string = bodyPartId + "_" + txtGrey + "_" + txtFinish + "_" + txtQtyPcs+"_"+updateDtlsId;
				}
				else {
					save_string += "," + bodyPartId + "_" + txtGrey + "_" + txtFinish + "_" + txtQtyPcs+"_"+updateDtlsId;
				}
			});

			if (breakOut == false) {
				return;
			}


		 var cc_update_ids=$('#hidden_collar_cuff_ids').val();



			$('#hidden_collarCuff_data').val(save_string+"**"+cc_update_ids);



			parent.emailwindow.hide();
		}

		function calculate_tot_qnty() {
			var txtTotQtyPcs = '';
			$("#tbl_list_search").find('tbody tr').each(function () {
				var txtQtyPcs = $(this).find('input[name="txtQtyPcs[]"]').val() * 1;
				txtTotQtyPcs = txtTotQtyPcs * 1 + txtQtyPcs * 1;
			});

			$('#txtTotQtyPcs').val(Math.round(txtTotQtyPcs));
		}



		function add_break_down_tr_zs(i)
		{
			var row_num = $('#txt_tot_row').val();
			row_num++;

			var clone = $("#tr_" + i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});

			clone.find("input,select").each(function () {
				$(this).attr({
					'id': function (_, id) {
						var id = id.split("_");
						return id[0] + "_" + row_num
					},
					'name': function (_, name) {
						return name
					},
					'value': function (_, value) {
						return value
					}
				});

            }).end();//.appendTo("#tbl_list_search")

			$("#tr_" + i).after(clone);

			$('#txtGrey_' + row_num).removeAttr("value").attr("value", "");
			$('#txtFinish_' + row_num).removeAttr("value").attr("value", "");


			$('#txtQtyPcs_' + row_num).removeAttr("value").attr("value", "");
			$('#txtQtyPcs_' + row_num).removeAttr("onKeyUp").attr("onKeyUp", "calculate_tot_qnty(" + row_num + ");");

			$('#increase_' + row_num).removeAttr("value").attr("value", "+");
			$('#decrease_' + row_num).removeAttr("value").attr("value", "-");
			$('#increase_' + row_num).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + row_num + ");");
			$('#decrease_' + row_num).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + row_num + ");");

			$('#txt_tot_row').val(row_num);
			reArrangeSl();
			set_all_onclick();
		}

	</script>
	</head>
	<body>
		<div style="width:530px;" align="center">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:530px; margin-top:5px">
					<input type="hidden" name="hidden_collarCuff_data" id="hidden_collarCuff_data" class="text_boxes"
					value="">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="525" class="rpt_table">
						<thead>
							<th width="30">SL</th>
							<th width="100">Body Part</th>
							<th width="100">Gmts Size</th>
							<th width="100">Finish Size</th>
							<th width="100">Qty. Pcs</th>
							<th></th>
						</thead>
					</table>
					<div style="width:525px; overflow-y:scroll; max-height:230px;" id="buyer_list_view">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="505" class="rpt_table"
						id="tbl_list_search">
						<tbody>
							<?

							$sql = "select id,body_part_id,gmts_size,item_size, qty from wo_booking_colar_culff_dtls where wo_non_ord_samp_booking_dtl_id=$update_dtls_id and status_active=1";
								$collar_cuff_data_arr = sql_select($sql);
							if(count($collar_cuff_data_arr)>0)
							{


								$i = 1;
								$totQtyPcs = 0;
								$sl = 1;
								$hidden_collarCuffId="";
								foreach ($collar_cuff_data_arr as $row)
								{
									$collar_data = explode("_", $row);
									$totQtyPcs += $collar_data[3];



									?>
									<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl++; ?>">
										<td width="30" align="center"><? echo $sl; ?></td>
										<td width="100">
											<input type="text" name="txtBodyPartId[]" id="txtBodyPartId_<?php echo $i ?>"
											value="<? echo $body_part[$hidden_bodypartID_data]; //$body_part[$collar_data[0]]; ?>" class="text_boxes"
											style="width:80px" disabled/>
											<input type="hidden" name="bodyPartId[]" id="bodyPartId_<?php echo $i ?>"
											value="<? echo $hidden_bodypartID_data; ?>"/>
											<input type="hidden" name="updateDtlsId[]" id="updateDtlsId_<?php echo $i ?>"
											value="<? echo $update_dtls_id; ?>"/>


										</td>
										<td width="100"><input type="text" name="txtGrey[]" id="txtGrey_<? echo $i; ?>"
											class="text_boxes" style="width:80px"
											value="<?php echo $row[csf('gmts_size')]; ?>"/>
										</td>
										<td width="100"><input type="text" name="txtFinish[]" id="txtFinish_<? echo $i; ?>"
											class="text_boxes" style="width:80px"
											value="<?php echo $row[csf('item_size')];; ?>"/>
										</td>
										<td width="100">
											<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $i; ?>"
											class="text_boxes_numeric" style="width:80px"
											value="<?php echo $row[csf('qty')]; ?>" onKeyUp="calculate_tot_qnty();"/>
										</td>
										<td>
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]"
											style="width:30px" class="formbuttonplasminus" value="+"
											onClick="add_break_down_tr( <? echo $i; ?> )"/>
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]"
											style="width:30px" class="formbuttonplasminus" value="-"
											onClick="fn_deleteRow(<? echo $i; ?>);"/>
										</td>
									</tr>
									<?
									if($sl==1){
										$hidden_collarCuffId=$row[csf('id')].",";
									  }else{
										  $hidden_collarCuffId.=$row[csf('id')].",";
									  }
									$i++;
								}
								?>
								<input type="hidden" name="hidden_collar_cuff_ids" id="hidden_collar_cuff_ids"	value="<?=$hidden_collarCuffId;?>"/>
								<?
							}
							else
							{





								$i = 1;
								$totQtyPcs = 0;
								$sl = 1;

									?>
									<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl++; ?>">
										<td width="30" align="center"><? echo $sl; ?></td>
										<td width="100">
											<input type="text" name="txtBodyPartId[]" id="txtBodyPartId_<?php echo $i ?>"
											value="<? echo $body_part[$hidden_bodypartID_data]; //$body_part[$collar_data[0]]; ?>" class="text_boxes"
											style="width:80px" disabled/>
											<input type="hidden" name="bodyPartId[]" id="bodyPartId_<?php echo $i ?>"
											value="<? echo $hidden_bodypartID_data; ?>"/>
											<input type="hidden" name="collarCuffId[]" id="collarCuffId_<?php echo $i ?>"
											value=""/>
											<input type="hidden" name="updateDtlsId[]" id="updateDtlsId_<?php echo $i ?>"
											value="<? echo $update_dtls_id; ?>"/>

										</td>
										<td width="100"><input type="text" name="txtGrey[]" id="txtGrey_<? echo $i; ?>"
											class="text_boxes" style="width:80px"
											value=""/>
										</td>
										<td width="100"><input type="text" name="txtFinish[]" id="txtFinish_<? echo $i; ?>"
											class="text_boxes" style="width:80px"
											value=""/>
										</td>
										<td width="100">
											<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $i; ?>"
											class="text_boxes_numeric" style="width:80px"
											value="" onKeyUp="calculate_tot_qnty();"/>
										</td>
										<td>
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]"
											style="width:30px" class="formbuttonplasminus" value="+"
											onClick="add_break_down_tr( <? echo $i; ?> )"/>
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]"
											style="width:30px" class="formbuttonplasminus" value="-"
											onClick="fn_deleteRow(<? echo $i; ?>);"/>
										</td>
									</tr>
									<?
									$i++;

							}
							?>
						</tbody>
						<tfoot>
							<th colspan="4">Total</th>
							<th style="text-align:center"><input type="text" name="txtTotQtyPcs" id="txtTotQtyPcs"
								class="text_boxes_numeric" style="width:80px"
								value="<? echo $totQtyPcs; ?>" disabled/><input
								type="hidden" name="txt_tot_row" id="txt_tot_row" value="<? echo $i - 1; ?>"/></th>
								<th></th>
							</tfoot>
						</table>
					</div>
					<table width="500" id="tbl_close">
						<tr>
							<td align="center">
								<input type="button" name="close" class="formbutton" value="Close" id="main_close"
								onClick="fnc_close();" style="width:100px"/>
							</td>
						</tr>
					</table>
			</fieldset>
		</form>
	</div>
 </body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
?>