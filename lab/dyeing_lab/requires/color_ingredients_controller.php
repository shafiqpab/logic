<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_buyer")
{
	$data=explode('_',$data);
	if($data[1]==1){
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Buyer-", $selected, "" );
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-Buyer-", $selected, "" );
		exit();
	}
	
}


if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type in(5,6,7,23) group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", 0, "fn_item_details(this.value)",0 );
	exit();
}

if($action=="colorref_popup")
{
	echo load_html_head_contents("Color Ref. Search Popup","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	//echo $data.'=='.$cbo_company_id;
	?>
	<script>
		function js_set_value(str)
		{ 
			$("#selected_str_data").val(str);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead> 
					<tr>
						<th colspan="7"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
					</tr>
					<tr>               	 
						<th width="140" class="must_entry_caption">Company Name</th>
						<th width="100">Color Ref.</th>
                        <th width="100">Color</th> 
                        <th width="100">Shade Brightness</th>
						<th width="80">Dye Type</th>
						<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /> </th>
					</tr>           
				</thead>
				<tbody>
					<tr class="general">
						<td><? echo create_drop_down( "cbo_company_name", 140, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", "", "",0); ?><input type="hidden" id="selected_str_data">
						</td>
						<td><input type="text" name="txt_colorref" id="txt_colorref" class="text_boxes" style="width:90px" placeholder="" /></td>
						<td><input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:90px" placeholder="" /></td>
						<td><? echo create_drop_down( "cbo_shadebrightness", 100, $dyeinglab_shadeBrightness_arr,"", 1, "-- Select --","", "",0 ); ?></td>
                        <td><? echo create_drop_down( "cbo_dyetype", 80, $dyeinglab_dyetype_arr,"", 1, "-- Select --","", "",0 ); ?></td>
                        <td>
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_colorref').value+'_'+document.getElementById('txt_color').value+'_'+document.getElementById('cbo_shadebrightness').value+'_'+document.getElementById('cbo_dyetype').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_colorref_search_list_view', 'search_div', 'color_ingredients_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
                        </tr>
						</tbody>
					</table>    
				</form>
                <div id="search_div"></div>
			</div>
		</body>           
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_colorref_search_list_view")
{
	$exdata=explode('_',$data);
	$cbo_company_id=$exdata[0];
	$colorref=$exdata[1];
	$color=$exdata[2];
	$shadebrightness=$exdata[3];
	$dyetype=$exdata[4];
	$search_type =$exdata[5];
	
	if($cbo_company_id!=0) $companyCond=" and company_id='$cbo_company_id'"; else { echo "Please Select Company First."; die; }
	
	$colorref_cond=""; $color_cond=""; $shadebrightness_cond=""; $dyetype_cond="";
	if($search_type==1)
	{
		if($colorref!="") $colorref_cond="and color_ref='$colorref'";
		//if($color!="") $color_cond="and a.order_no='$color'";
		if ($shadebrightness!=0) $shadebrightness_cond=" and shade_brightness = '$shadebrightness' ";
		if ($dyetype!=0) $dyetype_cond=" and dye_type = '$dyetype' ";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($colorref!="") $colorref_cond="and color_ref  like '%$colorref%'";
		//if($color!="") $color_cond="and a.order_no  like '%$color%'";
		if($shadebrightness!=0) $shadebrightness_cond=" and shade_brightness = '$shadebrightness' ";
		if($dyetype!=0) $dyetype_cond=" and dye_type = '$dyetype' ";
	}
	else if($search_type==2)
	{
		if($colorref!="") $colorref_cond="and color_ref  like '$colorref%'";
		//if($color!="") $color_cond="and a.order_no  like '$color%'";
		if($shadebrightness!=0) $shadebrightness_cond=" and shade_brightness = '$shadebrightness' ";
		if($dyetype!=0) $dyetype_cond=" and dye_type = '$dyetype' ";
	}
	else if($search_type==3)
	{
		if($colorref!="") $colorref_cond="and color_ref  like '%$colorref'";
		//if($color!="") $color_cond="and a.order_no  like '%$color'";
		if($shadebrightness!=0) $shadebrightness_cond=" and shade_brightness = '$shadebrightness' ";
		if($dyetype!=0) $dyetype_cond=" and dye_type = '$dyetype' ";
	}	
	$color_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name" );
	//echo "<pre>";
	//print_r($buyer_po_arr);
	?>
    <body>
		<div align="center">
			<fieldset style="width:600px;">
				<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
						<thead>
							<th width="30">SL</th>
							<th width="100">Color Ref.</th>
                            <th width="100">Color</th>
                            <th width="60">Color Code</th>
                            <th width="80">Shade Brightness</th>
                            <th width="60">Shade Code</th>
                            <th width="80">Dye Type</th>
                            <th>Dye Type Code</th>
						</thead>
					</table>
					<div style="width:600px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="580" class="rpt_table" id="list_view" >
							<?
							$sql= "select id, company_id, color_id, color_code, shade_brightness, shade_code, dye_type, dyetype_code, colorref_prefix, colorref_prefix_num, color_ref from lab_color_reference where status_active=1 and is_deleted=0 $companyCond $colorref_cond $color_cond $shadebrightness_cond $dyetype_cond order by id DESC";
							//echo $sql; die;
							$sql_res=sql_select($sql);
							$i=1; 
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value('<?=$row[csf('id')]; ?>')"> 
									<td width="30" align="center"><?=$i; ?></td>	
									<td width="100" align="center"><?php echo $row[csf('color_ref')]; ?></td>
                                    <td width="100" style="word-break:break-all"><?php echo $color_arr[$row[csf("color_id")]]; ?></td>
                                    <td width="60" style="word-break:break-all"><?php echo $row[csf("color_code")]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $dyeinglab_shadeBrightness_arr[$row[csf("shade_brightness")]]; ?></td>
                                    <td width="60" style="word-break:break-all"><?php echo $row[csf('shade_code')]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $dyeinglab_dyetype_arr[$row[csf('dye_type')]]; ?></td>
                                    <td style="word-break:break-all"><?php echo $dyeinglab_dyecode_arr[$row[csf('dyetype_code')]]; ?></td>
								</tr>
								<?
								$i++;
							}
							?>
						</table>
					</div>
				</form>
			</fieldset>
		</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=18 and report_id=282 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#Print').hide();\n";
	echo "$('#print2').hide();\n";
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==134){echo "$('#Print').show();\n";}
			if($id==135){echo "$('#print2').show();\n";}		
		}
	}
	exit();
}

if($action=="populate_data_from_search_popup")
{
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$data_array=sql_select("select id, company_id, color_id, color_code, shade_brightness, shade_code, dye_type, dyetype_code, colorref_prefix, colorref_prefix_num, color_ref from lab_color_reference where id='$data'");
	foreach ($data_array as $row)
	{
		$shadeGp=$row[csf("color_code")].$row[csf("shade_code")];
		$shadeGpBrightness=$color_arr[$row[csf("color_id")]].' '.$dyeinglab_shadeBrightness_arr[$row[csf("shade_brightness")]];
		echo "disable_enable_fields('cbo_company_name',1);\n";
		echo "document.getElementById('txt_color_ref').value = '".$row[csf("color_ref")]."';\n";
		echo "document.getElementById('hid_colorref_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_lab_source').value = '1';\n";
		echo "load_drop_down( 'requires/color_ingredients_controller', '".$row[csf("company_id")].'_1'."', 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('txt_dyeCode').value = '".$dyeinglab_dyecode_arr[$row[csf("dyetype_code")]]."';\n";
		echo "document.getElementById('txt_dyeType').value = '".$dyeinglab_dyetype_arr[$row[csf("dye_type")]]."';\n";
		echo "document.getElementById('txt_shadeGrp').value = '".$shadeGp."';\n";
		echo "document.getElementById('txt_shadeGrpColor').value = '".$shadeGpBrightness."';\n";
		echo "document.getElementById('txt_shadeBrightness').value = '".$dyeinglab_shadeBrightness_arr[$row[csf("shade_brightness")]]."';\n";
		//echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_createColorRef_entry',1);\n";
	}
	exit();
}

if($action=="colorrefentry_popup")
{
	echo load_html_head_contents("Color Ref. Entry Popup","../../../", 1, 1, '','1','');
	extract($_REQUEST); 
	$permission=$_SESSION['page_permission'];
	$user_level=$_SESSION['logic_erp']["user_level"];
	
	?>
    <script>
	var permission='<? echo $permission; ?>';
	//var str_color = [<? //echo substr(return_library_autocomplete( "select color_name from lib_color where status_active=1 and is_deleted=0 order by color_name ASC", "color_name" ), 0, -1); ?>];
	
	function add_break_down_tr( i )
	{
		var row_num=$('#tbl_colorref tr').length;
		//alert(row_num)
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			$("#tbl_colorref tbody tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name },
					'value': function(_, value) { return value }              
				});
			}).end().appendTo("#tbl_colorref tbody");
			
			$("#tbl_colorref tbody tr:last").removeAttr('id').attr('id','tr_'+i);
			$('#tr_'+i).find("td:eq(0)").text( i );
			
			$('#cbocolor_'+i).removeAttr("onBlur").attr("onBlur","fnc_create_colorcode("+i+")");
			$('#cboshadeBrightness_'+i).removeAttr("onchange").attr("onchange","fnc_create_shadecode("+i+")");
			$('#cboDyeType_'+i).removeAttr("onchange").attr("onchange","fncdyetypecode("+i+")");
			
			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+")");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_tr("+i+",'tbl_colorref')");
			
			$('#cbocolor_'+i).val('');
			$('#txtcolorcode_'+i).val(''); 
			$('#txtupid_'+i).val('');
			/*$("#txtcolor_"+i).autocomplete({
				source: str_color
			});*/
			set_all_onclick(); 
		}
	}

	function fn_delete_tr(rowNo,table_id) 
	{   
		if(table_id=='tbl_colorref')
		{
			var numRow = $('table#tbl_colorref tbody tr').length; 
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_colorref tbody tr:last').remove();
			}
		}
	}

	function js_set_value()
	{
		parent.emailwindow.hide();
	}
	
	function fnc_createColorRef_entry( operation )
	{
		var tot_row=$('#tbl_colorref tr').length;
		var all_data='';
		for(var i=1; i<=tot_row; i++)
		{
			if( form_validation('cbocompanyid_'+i+'*cbocolor_'+i+'*txtcolorcode_'+i+'*cboshadeBrightness_'+i+'*txtshadecode_'+i+'*cboDyeType_'+i+'*cboDyeTypeCode_'+i,'Company Name*Color*Color Code*Shade Brightness*Shade Code*Dye Type*Dye Type Code')==false)
			{ 
				return;
			}
			all_data+=get_submitted_data_string('cbocompanyid_'+i+'*cbocolor_'+i+'*txtcolorcode_'+i+'*cboshadeBrightness_'+i+'*txtshadecode_'+i+'*cboDyeType_'+i+'*cboDyeTypeCode_'+i+'*txtupid_'+i,"../../../",i);
		}
		//alert(all_data);
		var data="action=save_update_delete_colorref&operation="+operation+'&tot_row='+tot_row+all_data;
		freeze_window(operation);
		http.open("POST","color_ingredients_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_colorref_entry_reponse;
	}

	function fnc_colorref_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			release_freezing();
			if(reponse[0]==0 || reponse[0]==1)
			{
				show_msg(trim(reponse[0]));
				$('#tbl_colorref tr:not(:first)').remove();
				$('#cbocolor_1').val('');
				$('#txtcolorcode_1').val('');
				//parent.emailwindow.hide();
				set_button_status(0, permission, 'fnc_createColorRef_entry',1);
				show_list_view( $('#cbocompanyid_1').val(),'colorref_list_view','save_up_list_view','color_ingredients_controller','setFilterGrid(\'tbl_upListView\',-1)');
			}
		}
	}
	
	function fnc_create_colorcode(inc)
	{
		var color=get_dropdown_text( 'cbocolor_'+inc );//trim($('#cbocolor_'+inc).val());
		var colorName=color.toUpperCase();
		//var colorcode = color.charAt(0);
		'','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',''
		var colorcode="";
		if(colorName=="BLUE") colorcode="B";
		else if(colorName=="ANTHRACITE") colorcode="A";
		else if(colorName=="BROWN") colorcode="BR";
		else if(colorName=="BURGUNDY") colorcode="BN";
		else if(colorName=="BLACK") colorcode="BK";
		else if(colorName=="BEIGE") colorcode="BG";
		else if(colorName=="CHARCOAL") colorcode="CC";
		else if(colorName=="CHOCOLATE") colorcode="C";
		else if(colorName=="DK GREY") colorcode="DG";
		else if(colorName=="GREEN") colorcode="G";
		else if(colorName=="GREY") colorcode="GR";
		else if(colorName=="GRAY MELANGE") colorcode="GM";
		else if(colorName=="KHAKI") colorcode="KK";
		else if(colorName=="LILAC") colorcode="L";
		else if(colorName=="FLUO") colorcode="F";
		else if(colorName=="WASH") colorcode="H";
		else if(colorName=="SGCK") colorcode="K";
		else if(colorName=="MULTI TONES") colorcode="M";
		else if(colorName=="NOIR") colorcode="N";
		else if(colorName=="NAVY") colorcode="NV";
		else if(colorName=="NEON YELLOW") colorcode="NY";
		else if(colorName=="NEON GREEN") colorcode="NG";
		else if(colorName=="NEON PINK") colorcode="NP";
		else if(colorName=="NEON ORANGE") colorcode="NO";
		else if(colorName=="ORANGE") colorcode="O";
		else if(colorName=="OFF WHITE") colorcode="OW";
		else if(colorName=="OLIVE") colorcode="OL";
		else if(colorName=="PURPLE") colorcode="P";
		else if(colorName=="PINK") colorcode="PK";
		else if(colorName=="RED") colorcode="R";
		else if(colorName=="ROYAL") colorcode="RY";
		else if(colorName=="VIOLET") colorcode="V";
		else if(colorName=="TURQUOISE") colorcode="T";
		else if(colorName=="WHITE") colorcode="W";
		else if(colorName=="YELLOW") colorcode="Y";
		
		$('#txtcolorcode_'+inc).val( colorcode.toUpperCase() );
	}
	
	function fnc_create_shadecode(inc)
	{
		var shadeval=get_dropdown_text( 'cboshadeBrightness_'+inc );
		var shadecode = shadeval.charAt(0);
		$('#txtshadecode_'+inc).val( shadecode.toUpperCase() );
	}
	
	function fncdyetypecode(inc)
	{
		$('#cboDyeTypeCode_'+inc).val( $('#cboDyeType_'+inc).val() );
	}
	
	function fncLoadData(id)
	{
		if(id==1)	
		{
			show_list_view( $('#cbocompanyid_1').val(),'colorref_list_view','save_up_list_view','color_ingredients_controller','setFilterGrid(\'tbl_upListView\',-1)');
		}
	}
	
	function get_colorref_data(id)
	{
		get_php_form_data(id, "populate_data_from_colorref", "color_ingredients_controller" );
	}
    </script>
    <body onLoad="set_hotkey();">
    <div style="display:none"><? echo load_freeze_divs ("../../../",$permission); ?></div>
    <form name="colorcreatfrm_1"  id="colorcreatfrm_1" autocomplete="off">
        <table cellspacing="0" cellpadding="0" width="720" >
        <tr><td width="720" valign="top">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="9" align="center">Create Color Ref. &nbsp;</th>
                    </tr>
                    <tr>
                        <th width="20">SL</th>
                        <th width="100" class="must_entry_caption">Company</th>
                        <th width="120" class="must_entry_caption">Color</th>
                        <th width="60" class="must_entry_caption">Color Code</th>
                        <th width="80" class="must_entry_caption">Shade Brightness</th>
                        <th width="60" class="must_entry_caption">Shade Code</th>
                        <th width="120" class="must_entry_caption">Dye Type</th>
                        <th width="60" class="must_entry_caption">Dye Type Code<input type="hidden" name="template_break_data" id="template_break_data" value="" /></th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
              </table>
              <div style="width:720px; overflow-y:scroll; max-height:220px;" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" id="tbl_colorref" >
                	<tbody id="tbd_colorref">
                        <tr id="tr_1">
                            <td width="20">1</td>
                            <td width="100"><? echo create_drop_down( "cbocompanyid_1", 100, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-Company-", $selected, "fncLoadData(1);"); ?></td>
                            <td width="120"><? echo create_drop_down( "cbocolor_1", 100, "select distinct min(id) as id, color_name from lib_color where status_active =1 and is_deleted=0 and color_name in ('BLUE','FLUO','WASH','SGCK','MULTI TONES','NOIR','ORANGE','PURPLE','RED/PINK','TURQUOISE','WHITE','YELLOW','RED', 'GREEN', 'VIOLET', 'LILAC', 'GREY', 'KHAKI', 'OLIVE', 'PINK', 'ANTHRACITE', 'DK GREY', 'CHOCOLATE', 'BROWN', 'BURGUNDY', 'NAVY', 'BLACK', 'ROYAL', 'CHARCOAL', 'NEON YELLOW', 'NEON GREEN', 'NEON PINK', 'NEON ORANGE', 'BEIGE', 'GRAY MELANGE', 'OFF WHITE') group by color_name order by color_name ASC","id,color_name", 1, "-Color-", $selected, "fnc_create_colorcode(1);"); ?>
						
						</td>
                            <td width="60"><input style="width:47px;" type="text" id="txtcolorcode_1" name="txtcolorcode_1" class="text_boxes" readonly/></td>
                            <td width="80"><? echo create_drop_down( "cboshadeBrightness_1", 80, $dyeinglab_shadeBrightness_arr,"", 1, "-Select-", 0, "fnc_create_shadecode(1);", "", "", "", "", "", "", "", "cboshadeBrightness[]" ); ?></td>
                            <td width="60"><input style="width:47px;" type="text" id="txtshadecode_1" name="txtshadecode_1" readonly class="text_boxes" /></td>
                            <td width="120"><? echo create_drop_down( "cboDyeType_1", 120, $dyeinglab_dyetype_arr,"", 1, "-Select-", 0, "fncdyetypecode(1);", "", "", "", "", "", "", "", "cboDyeType[]" ); ?></td>
                           <td width="60"><? echo create_drop_down( "cboDyeTypeCode_1", 60, $dyeinglab_dyecode_arr,"", 1, "-Select-", 0, "", "", "", "", "", "", "", "", "cboDyeTypeCode[]" ); ?>
                           		<input style="width:40px;" type="hidden" id="txtupid_1" />
                           </td>
                           <td>&nbsp;<input type="button" id="increaseset_1" style="width:23px;display:none" class="formbutton" value="+" onClick="add_break_down_tr(1);" />&nbsp;
                                <input type="button" id="decreaseset_1" style="width:23px;display:none" class="formbutton" value="-" onClick="javascript:fn_delete_tr(1 ,'tbl_colorref');"/></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <table width="720" cellspacing="0" border="0">
                <tr>
                    <td align="center" class="button_container">
						<? echo load_submit_buttons($permission,"fnc_createColorRef_entry",0,0,"reset_form('colorcreatfrm_1','save_up_list_view','','','$(\'#tbl_colorref tr:not(:first)\').remove();')",1); ?> </td> 
                </tr>
                <tr><td align="center"><input type="button" id="btn" style="width:70px" class="formbutton" value="Close" onClick="js_set_value();" /></td></tr>
            </table>
        </td>
        <td width="10">&nbsp;</td>
        <td align="center" valign="top"><div id="save_up_list_view"></div></td></tr>
        </table>
    </form>
    </body>
   <!-- <script> $("#txtcolor_1").autocomplete({
		source: str_color
	});</script>-->
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
	exit();
}

if($action=='save_update_delete_colorref')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"); 
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//echo "0**".$tot_row;
		$id=return_next_id( "id", "lab_color_reference", 1);
		$maxref=return_next_id( "colorref_prefix_num", "lab_color_reference", 1);
		$data_arr="";
		$field_arr="id, company_id, color_id, color_code, shade_brightness, shade_code, dye_type, dyetype_code, colorref_prefix, colorref_prefix_num, color_ref, inserted_by, insert_date, status_active, is_deleted";
		$m=1; $n=1;
		for ($i=1;$i<=$tot_row;$i++)
		{
			$cbocompanyid="cbocompanyid_".$i;
			$cbocolor="cbocolor_".$i;
			$txtcolorcode="txtcolorcode_".$i;
			$cboshadeBrightness="cboshadeBrightness_".$i;
			$txtshadecode="txtshadecode_".$i;
			$cboDyeType="cboDyeType_".$i;
			$cboDyeTypeCode="cboDyeTypeCode_".$i;
			$txtupid="txtupid_".$i;
			
			$autoColorRefPrefix=str_replace("'","",$$cbocompanyid).str_replace("'","",$$txtcolorcode).str_replace("'","",$$txtshadecode).$dyeinglab_dyecode_arr[str_replace("'","",$$cboDyeTypeCode)];			
			$autoColorRefNo=str_replace("'","",$$cbocompanyid).str_replace("'","",$$txtcolorcode).str_replace("'","",$$txtshadecode).$dyeinglab_dyecode_arr[str_replace("'","",$$cboDyeTypeCode)].str_pad($maxref,7,'0',STR_PAD_LEFT); 
			
			/*if(str_replace("'","",$$txtcolor)!="")
			{
				if (!in_array(str_replace("'","",$$txtcolor),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$txtcolor), $color_arr, "lib_color", "id,color_name","2");
					$new_array_color[$color_id]=str_replace("'","",$$txtcolor);
				}
				else $color_id =  array_search(str_replace("'","",$$txtcolor), $new_array_color);
			}
			else $color_id=0;*/
			
			if ($i!=1) $data_arr .=",";
			$data_arr .="(".$id.",".$$cbocompanyid.",".$$cbocolor.",".$$txtcolorcode.",".$$cboshadeBrightness.",".$$txtshadecode.",".$$cboDyeType.",".$$cboDyeTypeCode.",'".$autoColorRefPrefix."','".$maxref."','".$autoColorRefNo."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$id++;
			$maxref++;
		}//die;
		$rID=sql_insert("lab_color_reference",$field_arr,$data_arr,1);
		//echo "10**insert into lab_color_reference (".$field_arr.") values ".$data_arr;die;
		//echo "10**".$rID; die;
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**";
			}
			else{
				oci_rollback($con);
				echo "10**";
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
		$rowid=return_next_id( "id", "lab_color_reference", 1);
		//$tempid=return_next_id( "temp_id", "qc_template", 1);
		$data_arr="";
		$field_arr_up="color_id*color_code*shade_brightness*shade_code*dye_type*dyetype_code*updated_by*update_date";
		$m=1; $n=1;
		for ($i=1;$i<=$tot_row;$i++)
		{
			$cbocompanyid="cbocompanyid_".$i;
			$cbocolor="cbocolor_".$i;
			$txtcolorcode="txtcolorcode_".$i;
			$cboshadeBrightness="cboshadeBrightness_".$i;
			$txtshadecode="txtshadecode_".$i;
			$cboDyeType="cboDyeType_".$i;
			$cboDyeTypeCode="cboDyeTypeCode_".$i;
			$txtupid="txtupid_".$i;
			
			/*if(str_replace("'","",$$txtcolor)!="")
			{
				if (!in_array(str_replace("'","",$$txtcolor),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$txtcolor), $color_arr, "lib_color", "id,color_name","2");
					$new_array_color[$color_id]=str_replace("'","",$$txtcolor);
				}
				else $color_id =  array_search(str_replace("'","",$$txtcolor), $new_array_color);
			}
			else $color_id=0;*/
			
			$id_arr[]=str_replace("'",'',$$txtupid);
			$data_arr_up[str_replace("'",'',$$txtupid)] =explode("*",("".$$cbocolor."*".$$txtcolorcode."*".$$cboshadeBrightness."*".$$txtshadecode."*".$$cboDyeType."*".$$cboDyeTypeCode."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
		}
		//echo "0**".$data_arr; die;
		$flag=1;
		
		$rID=execute_query(bulk_update_sql_statement("lab_color_reference", "id",$field_arr_up,$data_arr_up,$id_arr ));
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		if($db_type==0)
		{
			if($flag==1 ){
				mysql_query("COMMIT");  
				echo "0**";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1 ){
				oci_commit($con);
				echo "0**";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}  // Update End
}

if($action=="colorref_list_view")
{
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	?>
    <div style="width:400px;" align="center">
    <legend>Update Color Ref. List</legend>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Color Ref.</th>
                <th width="100">Color</th>
                <th width="90">Shade Brightness</th>
                <th>Dye Type</th>
            </thead>
     	</table>
        <div style="width:400px; overflow-y:scroll; max-height:220px;" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="380" class="rpt_table" id="tbl_upListView" >
            <?
				$sql_ref="select id, company_id, color_id, color_code, shade_brightness, shade_code, dye_type, dyetype_code, colorref_prefix, colorref_prefix_num, color_ref from lab_color_reference where company_id='$data' and status_active=1 and is_deleted=0 order by id DESC";
				$sql_ref_res=sql_select($sql_ref);
				$i=1; 
				foreach($sql_ref_res as $row)
				{
					if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr id="tr_<?=$i; ?>" bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_colorref_data('<?=$row[csf('id')]; ?>');"> 
                        <td width="30" align="center"><?=$i; ?></td>
                        <td width="80" style="word-break:break-all"><?=$row[csf('color_ref')]; ?></td>
                        <td width="100" style="word-break:break-all"><?=$color_arr[$row[csf('color_id')]]; ?></td>
                        <td width="90" style="word-break:break-all"><?=$dyeinglab_shadeBrightness_arr[$row[csf('shade_brightness')]]; ?></td>
                        <td style="word-break:break-all"><?=$dyeinglab_dyetype_arr[$row[csf('dye_type')]]; ?></td>
                    </tr>
                    <?
					$i++;
				}
			?>
            </table>
        </div>
     </div>
    <?
	exit();
}

if($action=="populate_data_from_colorref")
{
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$data_array=sql_select("select id, company_id, color_id, color_code, shade_brightness, shade_code, dye_type, dyetype_code, colorref_prefix, colorref_prefix_num, color_ref from lab_color_reference where id='$data'");
	foreach ($data_array as $row)
	{
		echo "disable_enable_fields('cbocompanyid_1',1);\n";
		echo "document.getElementById('cbocompanyid_1').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbocolor_1').value = '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txtcolorcode_1').value = '".$row[csf("color_code")]."';\n";
		echo "document.getElementById('cboshadeBrightness_1').value = '".$row[csf("shade_brightness")]."';\n";
		echo "document.getElementById('txtshadecode_1').value = '".$row[csf("shade_code")]."';\n";
		echo "document.getElementById('cboDyeType_1').value = '".$row[csf("dye_type")]."';\n";
		echo "document.getElementById('cboDyeTypeCode_1').value = '".$row[csf("dyetype_code")]."';\n";
		echo "document.getElementById('txtupid_1').value = '".$row[csf("id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_createColorRef_entry',1);\n";
	}
	exit();
}

if($action=="item_details")
{
	$data=explode("***",$data);
	$company_id=$data[0];
	$update_id=$data[1];
	$store_id=$data[2];

	$item_group_arr=return_library_array( "SELECT id, item_name from lib_item_group where status_active=1 and is_deleted=0",'id','item_name');

	$colorref_data_arr=array(); $colorref_prod_id_arr=array(); $product_data_arr=array();
	if($update_id!=0)
	{	
		$colorDataSql=sql_select("SELECT id, mst_id, prod_id, dose_base, ratio, item_lot, seq_no, remarks, store_id from lab_color_ingredients_dtls where mst_id=$update_id and status_active=1 and is_deleted=0 order by seq_no");
		foreach($colorDataSql as $row)
		{
			$prod_key=$row[csf('prod_id')]."__".$row[csf('store_id')]."__".$row[csf('item_lot')];
			$colorref_data_arr[$prod_key]['id']=$row[csf('id')];
			$colorref_data_arr[$prod_key]['item_lot']=$row[csf('item_lot')];
			$colorref_data_arr[$prod_key]['dose_base']=$row[csf('dose_base')];
			$colorref_data_arr[$prod_key]['ratio']=$row[csf('ratio')];
			$colorref_data_arr[$prod_key]['seq_no']=$row[csf('seq_no')];
			$colorref_data_arr[$prod_key]['remarks']=$row[csf('remarks')];
			$colorref_prod_id_arr[$prod_key]=$prod_key;
		}
	}
	//echo "<pre>"; print_r($colorref_data_arr);//die;
	//echo "<pre>"; print_r($colorref_prod_id_arr);

	$sql="SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.cons_qty as store_stock, b.lot  
	from product_details_master a, inv_store_wise_qty_dtls b, use_for_lab_machine_finishing c
	where a.id=b.prod_id and c.prod_id=b.prod_id and c.use_for=1  and a.company_id='$company_id' and b.store_id=$store_id and a.item_category_id in(5,6,7,23) and b.cons_qty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	order by a.item_category_id, a.id";
	// echo $sql;
	$nameArray=sql_select( $sql );
	foreach($nameArray as $row)
	{
		$prod_key=$row[csf('id')]."__".$store_id."__".$row[csf('lot')];
		$product_data_arr[$prod_key]=$row[csf('item_category_id')]."**".$row[csf('item_group_id')]."**".$row[csf('sub_group_name')]."**".$row[csf('item_description')]."**".$row[csf('item_size')]."**".$row[csf('unit_of_measure')]."**".$row[csf('current_stock')]."**".$row[csf('store_stock')]."**".$row[csf('lot')];
	}
	//echo "<pre>"; print_r($product_data_arr);die;

	?>
    <div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table" >
			<thead>
				<th width="30">SL</th>
				<th width="80">Item Category</th>
				<th width="100">Item Group</th>
				<th width="70">Sub Group</th>
				<th width="150">Item Description</th>
				<th width="80">Item Lot</th>
				<th width="40">UOM</th>
				<th width="70" class="must_entry_caption" style="display:none">Dose Base</th>
				<th width="70" class="must_entry_caption">Dosage</th>
				<th width="70" class="must_entry_caption">Seq. No</th>
				<th width="50">Prod. ID</th>
				<th width="100">Stock Qty</th>
				<th>Comments</th>
			</thead>
		</table>
		<div style="width:1200px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1180" class="rpt_table" id="tbl_list_search">
				<tbody>
				<?
					$i=1;
					foreach($colorref_prod_id_arr as $prodKey=>$colordata)
					{
						$prodId_ref=explode("__",$prodKey);
						$prodId=$prodId_ref[0];
						$store_id=$prodId_ref[1];
						$item_lot=$prodId_ref[2];
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$prodData=explode("**",$product_data_arr[$prodKey]);
						$item_category_id=$prodData[0];
						$item_group_id=$prodData[1];
						$sub_group_name=$prodData[2];
						$item_description=$prodData[3];
						$item_size=$prodData[4];
						$unit_of_measure=$prodData[5];
						$current_stock=$prodData[6];
						$store_stock=$prodData[7];
						$lot=$prodData[8];

						$dtls_id=$colorref_data_arr[$prodKey]['id'];
						$item_lot=$colorref_data_arr[$prodKey]['item_lot'];
						$selected_dose=$colorref_data_arr[$prodKey]['dose_base'];
						$ratio=$colorref_data_arr[$prodKey]['ratio'];
						$seq_no=$colorref_data_arr[$prodKey]['seq_no'];
						$comments=$colorref_data_arr[$prodKey]['remarks'];
						$bgcolor="yellow";

						?>
						<tr bgcolor="<?=$bgcolor; ?>" id="search<?=$i; ?>">
                            <td width="30" align="center" id="sl_<?=$i; ?>"><?=$i; ?></td>
                            <td width="80" id="item_category_<?=$i; ?>" style="word-break:break-all"><?=$item_category[$item_category_id]; ?></td>
                            <td width="100" id="item_group_id_<?=$i; ?>" style="word-break:break-all"><?=$item_group_arr[$item_group_id]; ?></td>
                            <td width="70" id="sub_group_name_<?=$i; ?>" style="word-break:break-all"><?=$sub_group_name; ?>&nbsp;</td>
                            <td width="150" id="item_description_<?=$i; ?>" style="word-break:break-all"><?=$item_description." ".$item_size; ?></td>
                            <td width="80" id="item_lot_<?=$i; ?>">
                            	<input type="text" name="txt_item_lot[]" id="txt_item_lot_<?=$i; ?>" class="text_boxes" style="width:68px" value="<?=$item_lot; ?>" readonly>
                            </td>
                            <td width="40" align="center" id="uom_<?=$i; ?>"><?=$unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                            <td width="70" align="center" id="ratio_<?=$i; ?>">
                            <span style="display:none" id="dose_base_<?=$i; ?>"><?=create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-",$selected_dose); ?></span>
                            <input type="text" name="txt_ratio[]" id="txt_ratio_<?=$i; ?>" class="text_boxes_numeric" style="width:40px;" value="<?=$ratio; ?>" onBlur="seq_no_val(<?=$i; ?>);" <?=$disbled; ?>></td>
                            <td width="70" align="center" id="seqno_<?=$i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<?=$i; ?>" class="text_boxes_numeric" style="width:30px" value="<?=$seq_no; ?>" onBlur="row_sequence(<?=$i; ?>);"></td>
                            <td width="50" align="center" id="product_id_<?=$i; ?>"><?=$prodId; ?><input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<?=$i; ?>" value="<?=$prodId; ?>"><input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $dtls_id; ?>"></td>
                            <td align="right" width="100" title="<?="current stock=".$current_stock."store stock=".$store_stock; ?>" id="stock_qty_<?=$i; ?>"><?=number_format($store_stock,3,'.',''); ?></td>
                            <td align="center" id="comments_<?=$i; ?>"><input type="text" name="txt_comments[]" id="txt_comments_<?=$i; ?>" class="text_boxes" style="width:310px" value="<?=$comments; ?>"></td>
                        </tr>
						<?
						$i++;
					}

					foreach($product_data_arr as $prodKey=>$data)
					{
						if(!in_array($prodKey,$colorref_prod_id_arr))
						{
							$prodId_ref=explode("__",$prodKey);
							$prodId=$prodId_ref[0];
							$store_id=$prodId_ref[1];
							$item_lot=$prodId_ref[2];
							//$prodId
							$prodData=explode("**",$data);
							$item_category_id=$prodData[0];
							$item_group_id=$prodData[1];
							$sub_group_name=$prodData[2];
							$item_description=$prodData[3];
							$item_size=$prodData[4];
							$unit_of_measure=$prodData[5];
							$current_stock=$prodData[6];
							$store_stock=$prodData[7];
							$lot=$prodData[8];

							$ratio=''; $seq_no=''; $disbled=""; $comments='';
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<?=$bgcolor; ?>" id="search<?=$i; ?>">
								<td width="30" align="center" id="sl_<?=$i; ?>"><?=$i; ?></td>
								<td width="80" id="item_category_<?=$i; ?>" style="word-break:break-all"><?=$item_category[$item_category_id]; ?></td>
								<td width="100" id="item_group_id_<?=$i; ?>" style="word-break:break-all"><?=$item_group_arr[$item_group_id]; ?></td>
								<td width="70" id="sub_group_name_<?=$i; ?>" style="word-break:break-all"><?=$sub_group_name; ?>&nbsp;</td>
								<td width="150" id="item_description_<?=$i; ?>" style="word-break:break-all"><?=$item_description." ".$item_size; ?></td>
								<td width="80" id="item_lot_<?=$i; ?>"><input type="text" name="txt_item_lot[]" id="txt_item_lot_<?=$i; ?>" class="text_boxes" style="width:68px" value="<?=$item_lot; ?>" readonly>
								</td>
								<td width="40" align="center" id="uom_<?=$i; ?>"><?=$unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
								
								<td width="70" align="center" id="ratio_<?=$i; ?>">
                                <span style="display:none" id="dose_base_<?=$i; ?>"><?=create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-",$selected_dose); ?></span>
                                <input type="text" name="txt_ratio[]" id="txt_ratio_<?=$i; ?>" class="text_boxes_numeric" style="width:40px;" value="<?=$ratio; ?>" onBlur="seq_no_val(<?=$i; ?>);"></td>
								<td width="70" align="center" id="seqno_<?=$i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<?=$i; ?>" class="text_boxes_numeric" style="width:30px" value="<?=$seq_no; ?>" onBlur="row_sequence(<?=$i; ?>);"></td>
								<td width="50" align="center" id="product_id_<?=$i; ?>"><?=$prodId; ?><input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<?=$i; ?>" value="<?=$prodId; ?>"><input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? //echo $dtls_id; ?>"></td>
								<td align="right" width="100" title="<?="current stock=".$current_stock."store stock=".$store_stock; ?>" id="stock_qty_<?=$i; ?>"><?=number_format($store_stock,3,'.',''); ?></td>
								<td align="center" id="comments_<?=$i; ?>"><input type="text" name="txt_comments[]" id="txt_comments_<?=$i; ?>" class="text_boxes" style="width:310px" value="<?=$comments; ?>"></td>
							</tr>
							<?
							$i++;
						}
					}
				?>
                </tbody>
            </table>
        </div>
    </div>
	<?
	exit();
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$flag=1; 
	$colorref_id=str_replace("'","",$hid_colorref_id);
	$str_rep=array("/", "&", "*", "(", ")", "=","'",",",'"','#');
	
	if ($operation==0)  // Insert Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$sysPrefix=$sysPrefixNum="";
		if($type==6 || $type==7)
		{
			if($colorref_id=="")
			{
				echo "11**Color Ref. Not Found";disconnect($con);die;
			}
			$sysNo=str_replace("'","",$txt_sysNo);
			$sql_sysDtls=sql_select("select sys_prefix, sys_prefix_num from lab_color_ingredients_mst where lab_company_id=$cbo_lab_company_name and sys_no='$sysNo' and status_active=1 and is_deleted=0");
			$sysPrefix=$sql_sysDtls[0][csf('sys_prefix')];
			$sysPrefixNum=$sql_sysDtls[0][csf('sys_prefix_num')];
		}
		
		if($type==6 && $colorref_id>0)
		{
			$correctionNo=str_replace("'","",$txt_correction);
			if(str_replace("'","",$txt_sysNo)!='' && str_replace("'","",$txt_update_id)!='')
			{
				$sysNo=str_replace("'","",$txt_sysNo);
				$sql_correctionNo=sql_select("select max(correction) as correction from lab_color_ingredients_mst where sys_no='$sysNo' and color_ref_id=$colorref_id and status_active=1 and is_deleted=0");
				if($sql_correctionNo[0][csf('correction')]=="") $correction_no=0; else $correction_no=$sql_correctionNo[0][csf('correction')]+1;
			}
			else $correction_no=0;
			
			$sample_no=str_replace("'","",$cbosample_no);
			$update_id=str_replace("'","",$txt_update_id);
			$sys_prefix=$sysPrefix;
			$sys_prefix_num=$sysPrefixNum;
		}
		else if($type==7 && $colorref_id>0)
		{
			$sampleNo=str_replace("'","",$cbosample_no);
			if(str_replace("'","",$txt_sysNo)!='' && str_replace("'","",$txt_update_id)!='')
			{
				$sysNo=str_replace("'","",$txt_sysNo);
				$sql_sample=sql_select("select max(sample_no) as sample_no from lab_color_ingredients_mst where sys_no='$sysNo' and color_ref_id=$colorref_id and status_active=1 and is_deleted=0");
				if($sql_sample[0][csf('sample_no')]=="") $sample_no=1; else $sample_no=$sql_sample[0][csf('sample_no')]+1;
			}
			else $sample_no=1;
			
			$correction_no=str_replace("'","",$txt_correction);
			$update_id=str_replace("'","",$txt_update_id);
			$sys_prefix=$sysPrefix;
			$sys_prefix_num=$sysPrefixNum;
		}
		else
		{
			if($db_type==0) $date_cond=" YEAR(insert_date)"; else if($db_type==2) $date_cond="to_char(insert_date,'YYYY')";
			$correction_no=0;
			$sample_no=1;
			$new_sys_no=explode("*",return_mrr_number( str_replace("'","",$cbo_lab_company_name), '', 'LAB', date("Y",time()), 6, "select max(sys_prefix) as sys_prefix, max(sys_prefix_num) as sys_prefix_num from lab_color_ingredients_mst where lab_company_id=$cbo_lab_company_name and $date_cond=".date('Y',time())." and sys_prefix is not null and sys_prefix_num!=0 order by id DESC", "sys_prefix", "sys_prefix_num" ));
			$update_id=0;
			$sysNo=$new_sys_no[0];
			$sys_prefix=$new_sys_no[1];
			$sys_prefix_num=$new_sys_no[2];
		}
		
		$txt_colorDesc=str_replace($str_rep,' ',str_replace("'","",$txt_colorDesc));
		$txt_merchan_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_merchan_remarks));
		$mst_id=return_next_id("id", "lab_color_ingredients_mst", 1);
		$field_array_mst=" id, sys_prefix, sys_prefix_num, sys_no, correction, copy_from, sample_no, color_ref_id, company_id, section_id,lab_source, client_id, color_desc, panton, shade_num, commonly_used, fab_type_cps, remarks,order_no,style_ref,ratio,yarn_lot,lab_recipie_date,construction,blend,shade,cmc_de,whiteness,primary_source,ref_no,disperse_dying,reactive_dying,matching_standard, inserted_by, insert_date, status_active, is_deleted, lab_company_id, store_id, dyeing_part, color_range, dyeing_upto";
		
		$data_array_mst="(".$mst_id.",'".$sys_prefix."','".$sys_prefix_num."','".$sysNo."','".$correction_no."','".$update_id."','".$sample_no."',".$hid_colorref_id.",".$cbo_company_name.",".$cbo_section.",".$cbo_lab_source.",".$cbo_buyer_name.",'".$txt_colorDesc."',".$txt_pantone.",".$txt_shadeNo.",".$txt_commonlyUsed.",".$txt_fabricTypeCps.",'".$txt_merchan_remarks."',".$txt_order_no.",".$txt_style_ref.",".$cbo_ratio.",".$txt_yarn_lot.",".$txt_lab_recipie_date.",".$txt_construction.",".$txt_blend.",".$cbo_shade.",".$txt_cmc_de.",".$txt_whiteness.",".$txt_primary_source.",".$txt_ref_no.",".$txt_disperse_dying.",".$txt_reactive_dying.",".$txt_matching_standard.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,".$cbo_lab_company_name.",".$cbo_store_name.",".$cbo_dyeing_part.",".$cbo_color_range.",".$cbo_dyeing_upto.")";
		
		$dtls_id=return_next_id("id", "lab_color_ingredients_dtls", 1);
		$data_array_dtls="";
		$field_array_dtls="id, mst_id, store_id, prod_id, dose_base, ratio, item_lot, seq_no, remarks, inserted_by, insert_date, status_active, is_deleted";
		for ($i = 1; $i <= $total_row; $i++)
		{
			$product_id = "product_id_" . $i;
			$txt_seqno = "txt_seqno_" . $i;
			$txt_item_lot = "txt_item_lot_" . $i;
			$txt_comments = "txt_comments_" . $i;
			$cbo_dose_base = "cbo_dose_base_" . $i;
			$txt_ratio = "txt_ratio_" . $i;
			$updateIdDtls = "updateIdDtls_" . $i;
			if ($i != 1) $data_array_dtls .= ",";
			$data_array_dtls .= "(".$dtls_id.",".$mst_id.",".$cbo_store_name.",".$$product_id.",".$$cbo_dose_base.",".$$txt_ratio.",".$$txt_item_lot.",".$$txt_seqno.",".$$txt_comments.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

			$dtls_id = $dtls_id + 1; 
		}
		//echo $data_arr_buyer_meeting; die;
		//echo "10**insert into lab_color_ingredients_mst (".$field_array_mst.") values ".$data_array_mst;die;
		
		$flag=$rID1=1;
		
		$rID=sql_insert("lab_color_ingredients_mst",$field_array_mst,$data_array_mst,0);	
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		if($total_row>0)
		{
			$rID1=sql_insert("lab_color_ingredients_dtls",$field_array_dtls,$data_array_dtls,0);	
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo '10**'.$flag.'='.$rID.'='.$rID1; die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$mst_id).'**'.str_replace("'",'',$sysNo).'**'.str_replace("'",'',$correction_no).'**'.str_replace("'",'',$sample_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".str_replace("'",'',$mst_id).'**'.str_replace("'",'',$sysNo).'**'.str_replace("'",'',$correction_no).'**'.str_replace("'",'',$sample_no);
			}
			else
			{
				oci_rollback($con); 
				echo "10**";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;	
	}
	else if ($operation==1)  // Update Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$prevIdsArr=array();
		$colorDataSql=sql_select("SELECT id, seq_no from lab_color_ingredients_dtls where mst_id=$txt_update_id and status_active=1 and is_deleted=0");
		foreach($colorDataSql as $row)
		{
			$prevIdsArr[]=$row[csf('id')];
		}
		
		$field_array_mst="correction*color_ref_id*section_id*lab_source*client_id*color_desc*panton*shade_num*commonly_used*fab_type_cps*remarks*order_no*style_ref*ratio*yarn_lot*lab_recipie_date*construction*blend*shade*cmc_de*whiteness*primary_source*ref_no*disperse_dying*reactive_dying*matching_standard*dyeing_part*color_range*dyeing_upto*updated_by*update_date";
		
		$data_array_mst="".$txt_correction."*".$hid_colorref_id."*".$cbo_section."*".$cbo_lab_source."*".$cbo_buyer_name."*".$txt_colorDesc."*".$txt_pantone."*".$txt_shadeNo."*".$txt_commonlyUsed."*".$txt_fabricTypeCps."*".$txt_merchan_remarks."*".$txt_order_no."*".$txt_style_ref."*".$cbo_ratio."*".$txt_yarn_lot."*".$txt_lab_recipie_date."*".$txt_construction."*".$txt_blend."*".$cbo_shade."*".$txt_cmc_de."*".$txt_whiteness."*".$txt_primary_source."*".$txt_ref_no."*".$txt_disperse_dying."*".$txt_reactive_dying."*".$txt_matching_standard."*".$cbo_dyeing_part."*".$cbo_color_range."*".$cbo_dyeing_upto."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array_dtlsup="dose_base*ratio*item_lot*seq_no*remarks*updated_by*update_date";
		
		$dtls_id=return_next_id("id", "lab_color_ingredients_dtls", 1);
		$data_array_dtls="";
		$field_array_dtls="id, mst_id, store_id, prod_id, dose_base, ratio, item_lot, seq_no, remarks, inserted_by, insert_date, status_active, is_deleted";
		for ($i = 1; $i <= $total_row; $i++)
		{
			$product_id = "product_id_" . $i;
			$txt_seqno = "txt_seqno_" . $i;
			$txt_item_lot = "txt_item_lot_" . $i;
			$txt_comments = "txt_comments_" . $i;
			$cbo_dose_base = "cbo_dose_base_" . $i;
			$txt_ratio = "txt_ratio_" . $i;
			$updateIdDtls = "updateIdDtls_" . $i;
			
			if(str_replace("'",'',$$updateIdDtls)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateIdDtls);
				$data_array_dtlsup[str_replace("'",'',$$updateIdDtls)]=explode("*",("".$$cbo_dose_base."*".$$txt_ratio."*".$$txt_item_lot."*".$$txt_seqno."*".$$txt_comments."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			else
			{
				if ($i != 1) $data_array_dtls .= ",";
				$data_array_dtls .= "(".$dtls_id.",".$txt_update_id.",".$cbo_store_name.",".$$product_id.",".$$cbo_dose_base.",".$$txt_ratio.",".$$txt_item_lot.",".$$txt_seqno.",".$$txt_comments.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
	
				$dtls_id = $dtls_id + 1; 
			}
		}
		
		if(implode(',',$id_arr)!="")
		{
			$distance_delete_id=array_diff($prevIdsArr,$id_arr);
		}
		else
		{
			$distance_delete_id=$prevIdsArr;
		}
		
		//echo "10**insert into lab_color_ingredients_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		/*$meetMst_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetPer_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetDtls_id=return_next_id("id", "qc_meeting_mst", 1);*/
		$flag=1; $rID1=$rID2=$rID5=1;
		
		$rID=sql_update("lab_color_ingredients_mst",$field_array_mst,$data_array_mst,"id","".$txt_update_id."",1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		//echo bulk_update_sql_statement("qc_fabric_dtls", "id",$field_array_fab,$data_array_fab,$id_arr ); die;
		if($data_array_dtlsup!="" && $total_row>0)
		{
			$rID1=execute_query(bulk_update_sql_statement("lab_color_ingredients_dtls", "id",$field_array_dtlsup,$data_array_dtlsup,$id_arr ));
			//$rID_fab=sql_insert("qc_fabric_dtls",$field_array_fab,$data_array_fab,0);	
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($data_array_dtls!="" && $total_row>0)
		{
			$rID2=sql_insert("lab_color_ingredients_dtls",$field_array_dtls,$data_array_dtls,0);	
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		$field_array_del="status_active*is_deleted*updated_by*update_date";
		$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//print_r ($distance_delete_id);

		foreach($distance_delete_id as $id_val)
		{
			$rID5=sql_update("lab_color_ingredients_dtls",$field_array_del,$data_array_del,"id","".$id_val."",1);
			if($rID5==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo "10**".$flag."=".$rID."=".$rID1."=".$rID2."=".$rID5; die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_update_id).'**'.str_replace("'",'',$txt_sysNo).'**'.str_replace("'",'',$txt_correction).'**'.str_replace("'",'',$cbosample_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'",'',$txt_update_id).'**'.str_replace("'",'',$txt_sysNo).'**'.str_replace("'",'',$txt_correction).'**'.str_replace("'",'',$cbosample_no);
			}
			else
			{
				oci_rollback($con); 
				echo "10**";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;	
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");  
				echo "2**";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con); 
				echo "2**";
			}
			else{
				oci_rollback($con); 
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	//exit();
}

if ($action == "systemid_popup")
{
	echo load_html_head_contents("Color Ingredients Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id) {
			$('#hidden_update_id').val(id);
			parent.emailwindow.hide();
		}
		function load_buyer(source,type){
			if(type==1){
				if( form_validation('cbo_lab_source','Lab Source')==false )
				{
					return;
				}
				else{
					var req_company=$('#cbo_lab_source').val();
					var buyer_data=source+'_'+req_company;
					load_drop_down( 'color_ingredients_controller', buyer_data, 'load_drop_down_buyer', 'buyer_td');
				}
			}
			if(type==2){
				if( form_validation('cbo_req_company_name','Req Company')==false )
				{
					return;
				}
				else{
					var req_company=$('#cbo_req_company_name').val();
					var buyer_data=req_company+'_'+source;
					load_drop_down( 'color_ingredients_controller', buyer_data, 'load_drop_down_buyer', 'buyer_td');
				}
			}			
		}
	</script>
    </head>
    <body>
	<div align="center" style="width:100%;">
		<form name="searchlabdipfrm" id="searchlabdipfrm">
			<fieldset style="width:800px;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="740" class="rpt_table">
					<thead>
						<tr>
							<th colspan="7"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
						</tr>
						<tr>
                        	<th width="140" class="must_entry_caption">Lab Company</th>
							<th width="100">Lab ID</th>
							<th width="120">Color Ref.</th>
                            <th width="140">Req Company</th>
                            <th width="120">Lab Source</th>
                            <th width="140">Client</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:80px;" class="formbutton"/>
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
								<input type="hidden" name="hidden_update_id" id="hidden_update_id" class="text_boxes" value="">
							</th>
						</tr>
					</thead>
					<tr class="general">
						<td><? echo create_drop_down( "cbo_company_name", 120, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-Lab Company-", $cbo_company_id, ""); ?>
						</td>
						<td><input type="text" style="width:80px;" class="text_boxes" name="txt_search_sysId" id="txt_search_sysId" placeholder="Search"/></td>
						<td><input type="text" style="width:100px;" class="text_boxes" name="txt_search_colorref" id="txt_search_colorref" placeholder="Search"/></td>
						<td><? echo create_drop_down( "cbo_req_company_name", 120, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-Req Company-", 0, "load_buyer(this.value,1)"); ?>
						</td>
						<td><? echo create_drop_down( "cbo_lab_source", 120, $lab_source_arr,"", 1, "-- Select Lab Source --", "", "load_buyer(this.value,2)","" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-Buyer-", $selected, "" ); ?></td>
						<td><input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_search_sysId').value+'_'+document.getElementById('txt_search_colorref').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_req_company_name').value+'_'+document.getElementById('cbo_lab_source').value, 'create_sys_search_list_view', 'search_div', 'color_ingredients_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;"/>
						</td>
					</tr>
				</table>
				<div style="width:100%; margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "create_sys_search_list_view")
{
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$color_ref_arr = return_library_array("select id, color_ref from lab_color_reference", 'id', 'color_ref');
	$companyArr= return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
	$data = explode("_", $data);
	$company_id = $data[0];
	$sysid = $data[1];
	$colorref = $data[2];
	$buyer_name = $data[3];
	$search_type = $data[4];
	$req_company_id = $data[5];
	$lab_source = $data[6];


	if ($search_type == 1) {
		if ($sysid != '') $sysid_cond = " and sys_prefix_num=$sysid"; else $sysid_cond = "";
	} else if ($search_type == 4 || $search_type == 0) {
		if ($sysid != '') $sysid_cond = " and sys_prefix_num like '%$sysid%' "; else $sysid_cond = "";
	} else if ($search_type == 2) {
		if ($sysid != '') $sysid_cond = " and sys_prefix_num like '$sysid%' "; else $sysid_cond = "";
	} else if ($search_type == 3) {
		if ($sysid != '') $sysid_cond = " and sys_prefix_num like '%$sysid' "; else $sysid_cond = "";
	}
	
	if ($colorref != '') 
	{
		$color_ref_id=return_field_value("id","lab_color_reference","status_active=1 and color_ref='$colorref'","id");
	}
	$colorref_cond = "";
	if($color_ref_id) $colorref_cond = " and color_ref_id=$color_ref_id";
	
	$buyer_cond=$company_cond=$source_cond="";
	if($company_id!=0) $company_cond=" and lab_company_id=$company_id";
	if($req_company_id!=0) $company_cond .=" and company_id=$req_company_id";
	if($buyer_name!=0) $buyer_cond=" and client_id=$buyer_name";
	if($lab_source!=0) $source_cond=" and lab_source=$lab_source";
	
	if($db_type==0) $yearCond=" YEAR(insert_date)"; else if($db_type==2) $yearCond="to_char(insert_date,'YYYY')";

	$sql = "select id, sys_prefix, sys_prefix_num, sys_no, $yearCond as year, correction, sample_no, color_ref_id, company_id, section_id, client_id, color_desc, panton, shade_num, commonly_used, fab_type_cps, remarks, lab_company_id, lab_source from lab_color_ingredients_mst where status_active=1 and is_deleted=0 $company_cond $buyer_cond $sysid_cond $colorref_cond $source_cond order by color_ref_id, id DESC";
	//echo $sql;
	$arr = array(4 => $dyeinglab_dyecode_arr,5 => $color_ref_arr, 6 => $lab_section, 7 => $buyer_arr, 8 => $companyArr, 10=>$lab_source_arr);

	echo create_list_view("tbl_list_search", "ID,Lab ID,Year,Correction,Sample No,Color Ref.,Section,Client,Req Company,Pantone,Source", "50,50,50,50,50,100,80,120,80,100", "900", "200", 0, $sql, "js_set_value", "id", "", 1, "0,0,0,0,sample_no,color_ref_id,section_id,client_id,company_id,0,lab_source", $arr, "id,sys_prefix_num,year,correction,sample_no,color_ref_id,section_id,client_id,company_id,panton,lab_source", "", "", '0,0,0,0,0,0,0,0,0,0', '');

	exit();
}

if ($action == 'populate_mstdata_from_search_popup')
{
	$data_array = sql_select("select id, sys_no, correction, sample_no, color_ref_id, company_id, section_id, client_id, color_desc, panton, shade_num, commonly_used, fab_type_cps, remarks,order_no,style_ref,ratio,yarn_lot,lab_recipie_date,construction,blend,shade,cmc_de,whiteness,primary_source,ref_no,disperse_dying,reactive_dying,matching_standard, lab_company_id, store_id, lab_source, dyeing_part, color_range, dyeing_upto  from lab_color_ingredients_mst where id='$data'");
	foreach ($data_array as $row)
	{
		$color_refid=$row[csf("color_ref_id")];
		echo "document.getElementById('txt_update_id').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('txt_sysNo').value 					= '" . $row[csf("sys_no")] . "';\n";
		echo "document.getElementById('hid_colorref_id').value 				= '" . $row[csf("color_ref_id")] . "';\n";
		echo "document.getElementById('txt_correction').value 				= '" . $row[csf("correction")] . "';\n";
		echo "document.getElementById('cbosample_no').value 				= '" . $row[csf("sample_no")] . "';\n";
		echo "document.getElementById('cbo_company_name').value 			= '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_lab_company_name').value 			= '" . $row[csf("lab_company_id")] . "';\n";
		echo "document.getElementById('cbo_store_name').value 			= '" . $row[csf("store_id")] . "';\n";
		echo "$('#cbo_company_name').attr('disabled','true')" . ";\n";
		echo "document.getElementById('cbo_section').value 					= '" . $row[csf("section_id")] . "';\n";
		echo "document.getElementById('cbo_lab_source').value 					= '" . $row[csf("lab_source")] . "';\n";
		echo "load_drop_down('requires/color_ingredients_controller', '".$row[csf("company_id")].'_'.$row[csf('lab_source')]."', 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_buyer_name').value 				= '" . $row[csf("client_id")] . "';\n";
		echo "document.getElementById('txt_colorDesc').value 				= '" .$row[csf("color_desc")]. "';\n";
		echo "document.getElementById('txt_pantone').value 					= '" . $row[csf("panton")] . "';\n";
		echo "document.getElementById('txt_shadeNo').value 					= '" . $row[csf("shade_num")] . "';\n";
		echo "document.getElementById('txt_commonlyUsed').value 			= '" . $row[csf("commonly_used")] . "';\n";
		echo "document.getElementById('txt_fabricTypeCps').value 			= '" . $row[csf("fab_type_cps")] . "';\n";
		echo "document.getElementById('txt_merchan_remarks').value 			= '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('txt_order_no').value 				= '" . $row[csf("order_no")] . "';\n";
		echo "document.getElementById('txt_style_ref').value 				= '" .$row[csf("style_ref")]. "';\n";
		echo "document.getElementById('cbo_ratio').value 					= '" . $row[csf("ratio")] . "';\n";
		echo "document.getElementById('txt_yarn_lot').value 					= '" . $row[csf("yarn_lot")] . "';\n";
		echo "document.getElementById('txt_lab_recipie_date').value = '".change_date_format($row[csf("lab_recipie_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_construction').value 			= '" . $row[csf("construction")] . "';\n";
		echo "document.getElementById('txt_blend').value 			= '" . $row[csf("blend")] . "';\n";
		echo "document.getElementById('cbo_shade').value 				= '" . $row[csf("shade")] . "';\n";
		echo "document.getElementById('txt_cmc_de').value 				= '" .$row[csf("cmc_de")]. "';\n";
		echo "document.getElementById('txt_whiteness').value 					= '" . $row[csf("whiteness")] . "';\n";
		echo "document.getElementById('txt_primary_source').value 					= '" . $row[csf("primary_source")] . "';\n";
		echo "document.getElementById('txt_ref_no').value 			= '" . $row[csf("ref_no")] . "';\n";
		echo "document.getElementById('txt_disperse_dying').value 			= '" . $row[csf("disperse_dying")] . "';\n";
		echo "document.getElementById('txt_reactive_dying').value 			= '" . $row[csf("reactive_dying")] . "';\n";
		echo "document.getElementById('txt_matching_standard').value 			= '" . $row[csf("matching_standard")] . "';\n";
		echo "document.getElementById('cbo_dyeing_part').value 				= '" . $row[csf("dyeing_part")] . "';\n";
		echo "document.getElementById('cbo_color_range').value 				= '" . $row[csf("color_range")] . "';\n";
		echo "document.getElementById('cbo_dyeing_upto').value 				= '" . $row[csf("dyeing_upto")] . "';\n";
		//echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_coloringredients_entry',1);\n";dyeing_part, color_range, dyeing_upto
	}
	
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$data_array=sql_select("select id, company_id, color_id, color_code, shade_brightness, shade_code, dye_type, dyetype_code, colorref_prefix, colorref_prefix_num, color_ref from lab_color_reference where id='$color_refid'");
	foreach ($data_array as $row)
	{
		$shadeGp=$row[csf("color_code")].$row[csf("shade_code")];
		$shadeGpBrightness=$color_arr[$row[csf("color_id")]].' '.$dyeinglab_shadeBrightness_arr[$row[csf("shade_brightness")]];
		echo "document.getElementById('txt_color_ref').value = '".$row[csf("color_ref")]."';\n";
		echo "document.getElementById('txt_dyeCode').value = '".$dyeinglab_dyecode_arr[$row[csf("dyetype_code")]]."';\n";
		echo "document.getElementById('txt_dyeType').value = '".$dyeinglab_dyetype_arr[$row[csf("dye_type")]]."';\n";
		echo "document.getElementById('txt_shadeGrp').value = '".$shadeGp."';\n";
		echo "document.getElementById('txt_shadeGrpColor').value = '".$shadeGpBrightness."';\n";
		echo "document.getElementById('txt_shadeBrightness').value = '".$dyeinglab_shadeBrightness_arr[$row[csf("shade_brightness")]]."';\n";
	}
	exit();
}

if($action=="lab_recipe_card_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);

	function show_company__($company_id, $show_cap, $fldlist) 
	{
		$fldarray = array("plot_no" => "plot_no", "level_no" => "level_no", "road_no" => "road_no", "block_no" => "block_no", "city" => "city", "zip_code" => "zip_code", "province" => "province", "country_id" => "country_id", "email" => "email", "website" => "website", "vat_number" => "vat_number");

		if (!is_array($fldlist)) {
			$fldlist = $fldarray;
		}

		$nameArray = sql_select("select a.plot_no, a.level_no, a.road_no, a.block_no, b.country_name as country_id, a.province, a.city, a.zip_code, a.email, a.vat_number from lib_company a left join  lib_country b on a.country_id =b.id where a.id=$company_id and a.status_active=1 and a.is_deleted=0");
		foreach ($nameArray as $result) 
		{
			foreach ($fldarray as $fld) {
				if (in_array($fld, $fldlist)) {
					if (trim($result[csf($fld)]) != "") {
						if ($show_cap == 1) {
							$address .= ucwords(str_replace("_", " ", $fld)) . "-";
							$address .= " " . trim($result[csf($fld)]);
						} else {
							$address .= " " . trim($result[csf($fld)]);
						}

						if ($address != '') {
							$address .= ",";
						}

					}
				}
			}
		}
		return $address;
	}

	$companyArr= return_library_array("select id, company_name from lib_company", "id", "company_name");
	$colorArr=return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyerArr= return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$itemGroupArr= return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	
	$colorRefSql="SELECT id, company_id, color_id, color_code, shade_brightness, shade_code, dye_type, dyetype_code, colorref_prefix, colorref_prefix_num, color_ref from lab_color_reference where id='$data[3]'";
	$colorRefArr= sql_select($colorRefSql);
	
	$dataSql="SELECT id, sys_no, correction, sample_no, color_ref_id, company_id, section_id, client_id, color_desc, panton, shade_num, commonly_used, fab_type_cps,lab_company_id, remarks from lab_color_ingredients_mst where id='$data[1]'";
	$dataArr= sql_select($dataSql);

	$lab_com_id = $dataArr[0]['LAB_COMPANY_ID'];
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$lab_com_id'","image_location");
	?>
    <div style="width:930px; font-size:6px">
        <table width="930" cellspacing="0" align="right" border="0">
        	<tr>
        		<td rowspan="4">
        			<img src="../../<?=$image_location; ?>" height="60" width="80">
        		</td>
        	</tr>
            <tr>
                <td colspan="6" align="center" style="font-size:26px"><strong><?=$companyArr[$dataArr[0]['LAB_COMPANY_ID']]; ?></strong></td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><?=show_company__($lab_com_id,'',''); ?></td>
            </tr>
            <tr>
                <td colspan="5" align="center" style="font-size:20px;padding-bottom: 25px;padding-left: 120px;"><u><strong>Laboratory Recipe Card<? //=$data[4]; ?></strong></u><span style="text-align: right;float: right;"><b><?=$dataArr[0][csf('sys_no')]; ?></b></span></td>
            </tr>
            <tr style="font-size: 17px;" valign="top" height="30">
                <td width="120"><strong>Lab Color Ref:</strong></td>
                <td width="205"><?=$colorRefArr[0][csf('color_ref')].'<b>-'.$dyeinglab_dyecode_arr[$dataArr[0][csf('sample_no')]].'-'.$dataArr[0][csf('correction')].'</b>'; ?></td>
                <td width="140"><strong>Color: </strong></td>
                <td width="175px"><?=$dataArr[0][csf('color_desc')]; ?></td>
                <td width="120"><strong>Client:</strong></td>
                <td width="155"><?=$buyerArr[$dataArr[0][csf('client_id')]]; ?></td>
            </tr>
            <tr style="font-size: 17px;" valign="top" height="30">
                <td><strong>Order No:</strong></td>
                <td>&nbsp;</td>
                <td><strong>Fabric Type/CPS:</strong></td>
                <td colspan="3"><?=$dataArr[0][csf('fab_type_cps')]; ?></td>
            </tr>
            <tr style="font-size: 17px;" valign="top" height="30">
                <td><strong>Req. Company:</strong></td>
                <td><?=$companyArr[$dataArr[0]['COMPANY_ID']]; ?></td>
                <td><strong>Remarks:</strong></td>
                <td colspan="2"><?=$dataArr[0][csf('remarks')]; ?></td>
            </tr>
        </table>
        <br clear="all"><br clear="all"><br clear="all"><br clear="all"><br clear="all">
        <!-- <table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
            <tr>
                <td align="center" rowspan="3" width="210" height="310">
                	<div style="background:url({{blogthreadlist.blogUri}}) no-repeat;background-position:center;opacity:0.6;filter:alpha(opacity=60);z-index: -1">ORIGINAL</div>
                </td>
                <td align="center" rowspan="3" width="210" height="310">
                	<div style="background:url({{blogthreadlist.blogUri}}) no-repeat;background-position:center;opacity:0.6;filter:alpha(opacity=60);z-index: -1">LAB DIP</div>
                </td>
                <td width="10" rowspan="3">&nbsp;</td>
                <td align="center" width="250" height="150">
                	<div style="background:url({{blogthreadlist.blogUri}}) no-repeat;background-position:center;opacity:0.6;filter:alpha(opacity=60);z-index: -1">BLEACH</div>
                </td>
                 <td align="center" width="250" height="150">
                 	<div style="background:url({{blogthreadlist.blogUri}}) no-repeat;background-position:center;opacity:0.6;filter:alpha(opacity=60);z-index: -1">TIKKA</div>
                 </td>
            </tr>
            <tr>
            	 <td width="10" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td align="center" width="500" height="150" colspan="2"><div style="background:url({{blogthreadlist.blogUri}}) no-repeat;background-position:center;opacity:0.6;filter:alpha(opacity=60);z-index: -1">1ST DYE</div></td>
            </tr>
        </table> -->
        <!-- <div style="border: 0px solid #000000;width: 800px;height: 350px;">
	        <div style="border: 1px solid #000000;width: 340.15748031px;height: 340.15748031px;float: left">
	        	<div style="border: 1px solid #000000;border-left: 0;border-right: 0;width: 169.5px;height: 7.5cm;float: left;margin-top: 28px">
	        		<span style="position: absolute;top: 143;left: 70;font-size: 13px;">ORIGINAL</span>
	        		
	        	</div>
	        	<div  style="border: 1px solid #000000;border-right: 0;width: 169.5px;height: 7.5cm;float: left;margin-top: 28px">
	        		<span style="position: absolute;top: 143;left: 240;font-size: 13px;">LAB DIP</span>
	        	</div>
	        </div>
	        ======================================================
	        <div style="border: 1px solid #000000;width: 7.5cm;height: 340.15748031px;float: left;margin-left: 40px;">
	        	<div style="border: 1px solid #000000;border-left: 0;border-right: 0;width: 141.2px;height: 4.5cm;float: left;margin-top: 28px">
	        		<span style="position: absolute;top: 143;left: 440;font-size: 13px;">BLEACH</span>
	        	</div>
	        	<div  style="border: 1px solid #000000;border-right: 0;width: 141.2px;height: 4.5cm;float: left;margin-top: 28px">
	        		<span style="position: absolute;top: 143;left: 580;font-size: 13px;">TIKKA</span>
	        		<span style="position: absolute;top: 340;left: 390;font-size: 13px;">1ST DYE</span>
	        	</div>
	        </div>
	    </div> -->
	    <div style="width: 1000px">
	    	<div style="width: 500px;float: left;">
	    		<table style="height: 15cm;width: 13cm;" border="1" cellspacing="0" cellpadding="0" class="rpt_table" rules="all">
	    			<tr style="height: 1.5cm;">
	    				<td width="50%"><center><b>ORIGINAL</b></center></td>
	    				<td width="50%"><center><b>LAB MATCHING</b></center></td>
	    			</tr>
	    			<tr>
	    				<td></td>
	    				<td></td>
	    			</tr>
	    		</table>
	    	</div>
	    	<div style="width: 500px;float: left;">
	    		<table style="height: 15cm;width: 12cm;" border="1" cellspacing="0" cellpadding="0" class="rpt_table" rules="all">
	    			<tr style="height: 1.5cm;">
	    				<td width="50%"><center><b>BLEACH</b></center></td>
	    				<td width="50%"><center><b>TIKKA</b></center></td>
	    			</tr>
	    			<tr style="height: 6cm;">
	    				<td></td>
	    				<td></td>
	    			</tr>
	    			<tr>
	    				<td colspan="2" valign="top"><b>1ST DYE</b></td>
	    			</tr>
	    		</table>
	    	</div>
	    	
	    </div>
        <br clear="all"><br clear="all"><br clear="all"><br clear="all"><br clear="all">
        <?
        $sql = "SELECT a.prod_id, a.dose_base, a.ratio, a.item_lot, a.seq_no,a.store_id, a.remarks, b.item_category_id, b.item_group_id, b.sub_group_name, b.item_description, b.item_size, b.unit_of_measure, b.current_stock from lab_color_ingredients_dtls a, product_details_master b where a.prod_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 order by a.seq_no, a.id ASC";
		// echo $sql;die();
		$nameArray=sql_select($sql);
		$prod_id_arr = array();
		$store_id_arr = array();
		foreach ($nameArray as $val) 
		{
		 	$prod_id_arr[$val[csf('prod_id')]] = $val[csf('prod_id')];
		 	$store_id_arr[$val[csf('store_id')]] = $val[csf('store_id')];
		} 

		$prodIds = implode(",", $prod_id_arr);
		$storeIds = implode(",", $store_id_arr);

		// ================================================================
		$sql_qty="SELECT a.id,b.cons_qty as store_stock, b.lot,b.store_id  
		from product_details_master a, inv_store_wise_qty_dtls b
		where a.id=b.prod_id and a.id in($prodIds) and b.store_id in($storeIds) and a.item_category_id in(5,6,7,23) and b.cons_qty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		// echo $sql_qty;
		$qty_res=sql_select( $sql_qty );
		foreach($qty_res as $row)
		{
			$qty_data_arr[$row[csf('id')]][$row[csf('store_id')]][$row[csf('lot')]] += $row[csf('store_stock')];
		}
		// echo "<pre>";print_r($qty_data_arr);
		?>
        <div style="width:100%;">
            <table cellspacing="0" width="955" border="1" rules="all" class="rpt_table" align="left">
                <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <th width="110">Item Cat.</th>
                    <th width="150">Item Group</th>
                    <th width="180">Item Description</th>
                    <th width="70">Item Lot</th>
                    <th width="50">UOM</th>
                    <th width="70">Dosage</th>
                    <th width="50">Seq. No</th>
                    <!-- <th width="80">Stock Qty</th> -->
                    <th>Comments</th>
                </thead>
				<?
				
				$i=1;
				foreach ($nameArray as $row) 
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$store_stock = $qty_data_arr[$row[csf('prod_id')]][$row[csf('store_id')]][$row[csf('item_lot')]];
					?>
					<tr bgcolor="<?=$bgcolor; ?>"> 
						<td width="30" style="word-wrap: break-word;word-break: break-all;"><?=$i; ?></td>
						<td width="100" style="word-wrap: break-word;word-break: break-all;"><?=$item_category[$row[csf('item_category_id')]]; ?></td>
						<td width="150" style="word-wrap: break-word;word-break: break-all;"><?=$itemGroupArr[$row[csf('item_group_id')]]; ?></td>
						<td width="180" style="word-wrap: break-word;word-break: break-all;"><?=$row[csf('item_description')]; ?></td>
						<td width="70" style="word-wrap: break-word;word-break: break-all;"><?=$row[csf('item_lot')]; ?></td>
						<td width="50" style="word-wrap: break-word;word-break: break-all;"><?=$unit_of_measurement[$row[csf('unit_of_measure')]]; ?></td>
						
						<td width="70" style="word-wrap: break-word;word-break: break-all;" align="right"><?=number_format($row[csf('ratio')],6); ?></td>
						<td width="50" style="word-wrap: break-word;word-break: break-all;" align="center"><?=$row[csf('seq_no')]; ?></td>
						<!-- <td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? //number_format($store_stock,4,'.',''); ?></td> -->
						<td style="word-wrap: break-word;word-break: break-all;"><?=$row[csf('remarks')]; ?> </td>
					</tr>
					<?
					$i++;
				}
				?>
	<!--
                <tr class="tbl_bottom">
                    <td align="right" colspan="6"><strong>Grand Total</strong></td>
                    <td align="right"><?// echo number_format($grand_tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                </tr>-->
            </table>
            <br>
			<?=signature_table(195, $lab_com_id, "880px"); ?>
        </div>
    </div>
	<?
	exit();
}

if($action=="lab_recipe_card_print2")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$companyArr= return_library_array("select id, company_name from lib_company", "id", "company_name");
	$colorArr=return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyerArr= return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$itemGroupArr= return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$user_name_arr=return_library_array( "select id,user_name from   user_passwd",'id','user_name');
	$colorRefSql="SELECT id, company_id, color_id, color_code, shade_brightness, shade_code, dye_type, dyetype_code, colorref_prefix, colorref_prefix_num, color_ref from lab_color_reference where id='$data[3]'";
	$colorRefArr= sql_select($colorRefSql);
	
	$dataSql="SELECT id, sys_no, correction, sample_no, color_ref_id, company_id, section_id, client_id, color_desc, panton, shade_num, commonly_used, fab_type_cps,lab_company_id, remarks,order_no,style_ref,ratio,yarn_lot,lab_recipie_date,construction,blend,shade,cmc_de,whiteness,primary_source,ref_no,disperse_dying,reactive_dying,matching_standard,inserted_by from lab_color_ingredients_mst where id='$data[1]'";
	$dataArr= sql_select($dataSql);

	$lab_com_id = $dataArr[0]['LAB_COMPANY_ID'];
	$inserted_by = $user_name_arr[$dataArr[0][csf('inserted_by')]];
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$lab_com_id'","image_location");
	?>
    <div style="width:830px; font-size:6px">
        <table width="830" cellspacing="0" align="right" border="0">
        	<tr>
        		<td rowspan="4">
        			<img src="../../<?=$image_location; ?>" height="60" width="80">
        		</td>
        	</tr>
            <tr>
                <td colspan="4" align="center" style="font-size:26px"><strong><?=$companyArr[$dataArr[0]['LAB_COMPANY_ID']]; ?></strong></td>
            </tr>
            <tr>
                <td colspan="4" align="center" style="font-size:20px;"><strong>Laboratory Recipe For Dyeing</strong></span></td>
            </tr>
		</table>
		<table width="830" cellspacing="0" align="right" border="1" rules="all" class="rpt_table">
            <tr style="font-size: 17px;" valign="top" height="30">
                <td width="200"><strong>Recipe Date</strong></td>
                <td width="200"><?=change_date_format($dataArr[0][csf('lab_recipie_date')]); ?></td>
                <td width="200"><strong>Blend</strong></td>
                <td width="200"><?=$dataArr[0][csf('blend')]; ?></td>
            </tr>
			<tr style="font-size: 17px;" valign="top" height="30">
                <td><strong>Buyer</strong></td>
                <td><?=$buyerArr[$dataArr[0][csf('client_id')]]; ?></td>
                <td><strong>Yarn Lot No</strong></td>
                <td><?=$dataArr[0][csf('yarn_lot')]; ?></td>
            </tr>
			<tr style="font-size: 17px;" valign="top" height="30">
                <td><strong>Order No</strong></td>
                <td><?=$dataArr[0][csf('order_no')]; ?></td>
                <td><strong>Whiteness</strong></td>
                <td><?=$dataArr[0][csf('whiteness')]; ?></td>
            </tr>
			<tr style="font-size: 17px;" valign="top" height="30">
                <td><strong>Style Ref</strong></td>
                <td><?=$dataArr[0][csf('style_ref')]; ?></td>
                <td><strong>CMC DE</strong></td>
                <td><?=$dataArr[0][csf('cmc_de')]; ?></td>
            </tr>
			<tr style="font-size: 17px;" valign="top" height="30">
                <td><strong>Color</strong></td>
                <td><?=$dataArr[0][csf('color_desc')]//$colorArr[$dataArr[0][csf('color_ref_id')]]; ?></td>
                <td><strong>Matching Standard</strong></td>
                <td><?=$dataArr[0][csf('matching_standard')]; ?></td>
            </tr>
			<tr style="font-size: 17px;" valign="top" height="30">
                <td><strong>Lab Dip No</strong></td>
                <td><?=$dataArr[0][csf('sys_no')]; ?></td>
                <td><strong>Light Source</strong></td>
                <td><?=$dataArr[0][csf('primary_source')]; ?></td>
            </tr>
			<tr style="font-size: 17px;" valign="top" height="30">
                <td><strong>Fabric Type</strong></td>
                <td><?=$dataArr[0][csf('construction')]; ?></td>
                <td><strong>Liquor Ratio</strong></td>
                <td><?=$liquor_ratioArr[$dataArr[0][csf('ratio')]]; ?></td>
            </tr>
			<tr style="font-size: 17px;" valign="top" height="30">
                <td><strong>Remarks</strong></td>
                <td colspan="3"><?=$dataArr[0][csf('remarks')]; ?></td>
            </tr>
        </table>
        <br clear="all"><br clear="all"><br clear="all"><br clear="all"><br clear="all">
        <?
        $sql = "SELECT a.prod_id, a.dose_base, a.ratio, a.item_lot, a.seq_no,a.store_id, a.remarks, b.item_category_id, b.item_group_id, b.sub_group_name, b.item_description, b.item_size, b.unit_of_measure, b.current_stock from lab_color_ingredients_dtls a, product_details_master b where a.prod_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 order by a.seq_no, a.id ASC";
		$nameArray=sql_select($sql);
		$prod_id_arr = array();
		$store_id_arr = array();
		foreach ($nameArray as $val) 
		{
		 	$prod_id_arr[$val[csf('prod_id')]] = $val[csf('prod_id')];
		 	$store_id_arr[$val[csf('store_id')]] = $val[csf('store_id')];
		} 

		$prodIds = implode(",", $prod_id_arr);
		$storeIds = implode(",", $store_id_arr);

		// ================================================================
		$sql_qty="SELECT a.id,b.cons_qty as store_stock, b.lot,b.store_id,a.item_description from product_details_master a, inv_store_wise_qty_dtls b where a.id=b.prod_id and a.id in($prodIds) and b.store_id in($storeIds) and a.item_category_id in(5,6,7,23) and b.cons_qty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$qty_res=sql_select( $sql_qty );
		foreach($qty_res as $row)
		{
			$qty_data_arr[$row[csf('id')]][$row[csf('store_id')]][$row[csf('lot')]] += $row[csf('store_stock')];
		}
		?>
        <div style="width:100%;">
            <table cellspacing="0" width="730" border="1" rules="all" class="rpt_table" align="left">
                <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <th width="200">Process Type</th>
					<th width="175">Item Description</th>
                    <th width="125">Lot No</th>
                    <th width="100">Concentration</th>
                    <th width="100">Unit</th>
                </thead>
				<?
				
				$i=1;
				foreach ($nameArray as $row) 
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$store_stock = $qty_data_arr[$row[csf('prod_id')]][$row[csf('store_id')]][$row[csf('item_lot')]];
					?>
					<tr bgcolor="<?=$bgcolor; ?>"> 
						<td width="30" style="word-wrap: break-word;word-break: break-all;" align="center"><?=$i; ?></td>
						<td width="200" style="word-wrap: break-word;word-break: break-all;"><?=$itemGroupArr[$row[csf('item_group_id')]]; ?></td>
						<td width="175" style="word-wrap: break-word;word-break: break-all;" align="center"><?=$row[csf('item_description')]; ?></td>
						<td width="125" style="word-wrap: break-word;word-break: break-all;" align="center"><?=$row[csf('item_lot')]; ?></td>
						<td width="100" style="word-wrap: break-word;word-break: break-all;" align="right"><?=number_format($row[csf('ratio')],6); ?></td>
						<td width="100" style="word-wrap: break-word;word-break: break-all;" align="center"><?
						if($row[csf('item_category_id')]==6){
							echo "% of BW";
						}else{
							echo "GPLL";
						} ?></td>
					</tr>
					<?
					$i++;
				}
				?>
            </table>
			<br clear="all"><br clear="all"><br clear="all"><br clear="all"><br clear="all">
			<table width="830" cellspacing="0" align="right" border="1" rules="all" class="rpt_table">
            <tr style="font-size: 17px;" valign="top" height="30">
                <td width="200"><strong>Disperse Dyeing</strong></td>
                <td width="600"><?=$dataArr[0][csf('disperse_dying')]; ?></td>
            </tr>
			<tr style="font-size: 17px;" valign="top" height="30">
                <td width="200"><strong>Reactive Dyeing</strong></td>
                <td width="600"><?=$dataArr[0][csf('reactive_dying')]; ?></td>
            </tr>
			</table>
			<br clear="all"><br clear="all"><br clear="all"><br clear="all"><br clear="all">
			<table width="600" cellspacing="0" align="center" border="1" rules="all" class="rpt_table">
            <tr style="font-size: 17px;" valign="top" height="300">
				<td width="300"><?//=$dataArr[0][csf('disperse_dying')]; ?></td>
                <td width="300"><?//=$dataArr[0][csf('reactive_dying')]; ?></td>
            </tr>
			<tr style="font-size: 17px;" valign="top" height="30" align="center">
				<td width="300"><strong>App : Lab Dip</strong></td>
                <td width="300"><strong>Confirmation</strong></td>
            </tr>
			</table>
			<br clear="all"><br clear="all"><br clear="all"><br clear="all"><br clear="all">
			<? echo signature_table(195, $lab_com_id, "800px",'', '', $inserted_by); ?>
        </div>
    </div>
	<?
	exit();
}