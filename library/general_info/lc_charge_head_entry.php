<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create FR Interface Team.
Functionality	:	
JS Functions	:
Created by		:	Nayem 
Creation date 	: 	07-03-2021
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("LC Charge Head Entry", "../../", 1, 1,$unicode,1,'');

$lc_pay_head_arr=array(46,47,71,86,88,89,90,91,96,97,98,101,102,111,112,114,115,116,117,118,139,140,173,174,175);
$pay_head_arr=implode(',',$lc_pay_head_arr);
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fnc_charge_head(operation) {
		freeze_window(operation);
		if (form_validation('cbo_company_name*cbo_bank_name*cbo_charge_name*cbo_currency_name*txt_date','Company Name*Bank Name*Charge For*Currency Name*Date')==false)
		{
			release_freezing();
			return;
		}	
            // var cbo_type = document.getElementById('cbo_type_name').value;
			
            var row_num_export ='<?= $pay_head_arr;?>'; 
            var row_num_arr = row_num_export.split(',');
            var row_num =row_num_arr.length; 
			
			var data_mst=get_submitted_data_string('cbo_company_name*cbo_bank_name*cbo_charge_name*cbo_currency_name*txt_date*txt_remarks*update_id',"../../");

			// alert(row_num_export);return;
			var data_panel="";
			for(var ii=0; ii<row_num; ii++)
			{
                var i=row_num_arr[ii];
				data_panel += '&txtpayhead_' + i + '=' + $('#txtpayhead_'+i).val() + '&txtpayamount_' + i + '=' + $('#txtpayamount_'+i).val(); 
			}
        	// alert(data_panel); return;
			var data="action=save_update_delete&operation="+operation+"&data_mst="+data_mst+"&row_num_arr="+row_num_arr+"&data_panel="+data_panel;
            //  alert(data); return;
			http.open("POST","requires/lc_charge_head_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_charge_head_reponse;
	}
	function fnc_charge_head_reponse(){
        if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			var company_id = document.getElementById('cbo_company_name').value;
			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1)
			{
				// document.getElementById('update_id').value=reponse[1];
				show_list_view ( company_id, 'load_lc_charge_head_entry', 'search_id', 'requires/lc_charge_head_entry_controller', 'setFilterGrid(\'search_id\',-1)');
				set_button_status(0, permission, 'fnc_charge_head',1);
				reset_form('chargehead_1','','');
			}

			if(parseInt(trim(reponse[0]))==2)
			{
				location.reload();
			}
			if(reponse[0]==11)
			{
				show_msg(trim(reponse[0]));
				alert(reponse[1]);
				release_freezing();
			}
			show_msg(trim(reponse[0]));
			release_freezing();
		}
        
    }
	function fnc_show(id){
		freeze_window(5);
		get_php_form_data(id, "populate_data_from_search", "requires/lc_charge_head_entry_controller" );
		set_button_status(1, permission, 'fnc_charge_head',1);
		release_freezing();
	}
</script>
</head>
<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center" style="width:100%;">
        <fieldset style="width:600px;">
       	 <legend>LC Charge Head Entry</legend>
            <form name="chargehead_1" id="chargehead_1" autocomplete = "off">	
			<fieldset>
              <table cellpadding="0" cellspacing="2" width="100%">
              	<tr>
                  	<td width="80" class="must_entry_caption">Company</td>
                  	<td width="150"><?
					echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--- Select Company ---",  $cbo_country_id, "load_drop_down( 'requires/lc_charge_head_entry_controller', this.value, 'load_lc_charge_head_entry', 'search_id' );");
					?> <input type="hidden" id="update_id" ></td>        
                  	<td width="80" class="must_entry_caption">Bank Name</td>
					<td width="150"> <?
							echo create_drop_down("cbo_bank_name", 150, "select bank_name,id from lib_bank where is_deleted=0 and status_active=1 order by bank_name", "id,bank_name", 1, "-- Select Bank --", 0, "");
							?> </td>        
                </tr>
                <tr>
                  	<td width="80" class="must_entry_caption">Charge For</td>
                  	<td width="150"><? 
                  		echo create_drop_down( "cbo_charge_name", 150, $lc_charge_arr,'',1,'-- Select Charge --',0,"");
                  	?>	</td>            
                  	<td width="80" class="must_entry_caption">Currency</td>
                  	<td width="150"><? 
                  		echo create_drop_down( "cbo_currency_name", 150, $currency,'',1,'-- Select Currency --',0,"load_drop_down( 'requires/lc_charge_head_entry_controller', this.value, 'load_lc_currency_nam', 'currency_nam' );");
                  	?>	</td>            
                </tr>
                <tr>
                  	<td width="80" class="must_entry_caption">Date</td>
                  	<td><input class="datepicker" id="txt_date" style="width:140px" ></td>
                  	<td width="80">Remarks</td>
                  	<td><input type="text" class="text_boxes" id="txt_remarks" style="width:140px" ></td>
                </tr>
                <!-- <tr><td>&nbsp;</td><td>&nbsp;</td></tr> -->
				</table>
				</fieldset>
				<br>
				<table class="rpt_table" width="350px" cellspacing="1" rules="all" id="tbl_panel">
				<thead>
					<tr>
						<th width="20">Sl</th>
						<th width="200">Payhead</th>
						<th width="80">Amount <span id="currency_nam"></span> </th>
					</tr>		
				</thead>
				<tbody>
					<?
					$i=0;
					foreach($lc_pay_head_arr as $key=>$value){
						$i++;
						if( $i % 2 == 0 ) $bgcolor="#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>
						<tr align="center"  id="tr_<? echo $value; ?>" bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><?= $i?></td>
						<td align="left"><?= $commercial_head[$value];?><input type="hidden" id="txtpayhead_<? echo $value; ?>" value="<? echo $value; ?>" ></td>
						<td align="center"><input type="text" class="text_boxes_numeric" id="txtpayamount_<? echo $value; ?>" style="width:80px"> </td>
						</tr>
						<?
					}?>
						<tr>
							<td align="center" colspan="3" class="button_container"><? echo load_submit_buttons( $permission, "fnc_charge_head", 0,0 ,"reset_form('chargehead_1','','')",1); ?></td>
						</tr>
				</tbody>
              </table>
            </form>
			<div id="search_id" style="margin:5px"></div>
        </fieldset>
        </div>
	</body>
<script>//set_multiselect('cbo_catagory_item','0','0','','');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
