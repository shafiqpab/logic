<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Approval Necessity Setup
				
Functionality	:	
JS Functions	:
Created by		:	Tajik 
Creation date 	: 	01-07-2017
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Approval Necessity Setup Info","../", 1, 1, $unicode,1,1); 
?>	

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
var permission='<? echo $permission; ?>';

    function fnc_LoadCompanyData(data)
    {               
        if($("#txt_date").val()=="")
        {
            return;
        }
        var response_data = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/approval_necessity_setup_controller');
        var is_exists = return_global_ajax_value( data, 'check_data_is_exis', '', 'requires/approval_necessity_setup_controller');

        if(response_data!='')
        {
            $("#approval_necessity_setup_1 tbody tr").remove();
            $("#approval_necessity_setup_1 tbody").append(response_data);

            var result=(is_exists.trim()).split('**');
            if( result[0]=='yes')
            {
                $("#mst_id").val(result[1]);
                set_button_status(1, permission, 'fnc_approval_necessity_setup',2);
            }
            else
            {
                $("#mst_id").val("");
               set_button_status(0, permission, 'fnc_approval_necessity_setup',2);
            }
            set_all_onclick();
            return;
        }
        return;
    }

    function load_grid()
    {
        var company_id=$("#cbo_company_name").val();
        show_list_view(company_id,'create_date_list_view','list_container','requires/approval_necessity_setup_controller','setFilterGrid("list_view",-1)');
    }

    function fnc_approval_necessity_setup( operation )
    {   
		freeze_window(operation);
        if (form_validation('cbo_company_name*txt_date','Company Name*Setup Date')==false)
        {
			release_freezing();
            return;
        }
        var company_name = $("#cbo_company_name").val();
        var mst_id = $("#mst_id").val();
        var txt_date = $("#txt_date").val();

        var current_date='<? echo date("d-m-Y"); ?>';
        if(date_compare(txt_date, current_date)==false)
        {
            alert("Input Date Can not Be Greater Than Current Date");
			release_freezing();
            return;
        }
        
        var row_num=$('#approval_necessity_setup_1 tbody tr').length;
        var data_all="";
        freeze_window(operation);
        for (var i=1; i<=row_num; i++) 
        {
            data_all=data_all+get_submitted_data_string('txt_page_id_'+i+'*txt_need_'+i+'*txt_allow_partial_'+i+'*cboValidatePage_'+i+'*updateDtls_'+i,"../",i);
        }
        
        var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all+'&company_name='+company_name+'&txt_date='+txt_date+'&mst_id='+mst_id;

        http.open("POST","requires/approval_necessity_setup_controller.php", true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_approval_necessity_setup_reponse;
    }
    
    function fnc_approval_necessity_setup_reponse()
    {
        if(http.readyState == 4) 
        {
            var reponse=trim(http.responseText).split('**');
            
			if(reponse[0]==0)
			{
				show_msg(reponse[0]);
                fnc_LoadCompanyData(reponse[1]+"_"+reponse[2])
                load_grid();
                //set_button_status(1, permission, 'fnc_approval_necessity_setup',2);
			}
			else if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
			}
			else if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
			}
            release_freezing();
        }
    }


</script>

</head>
<body onLoad="set_hotkey()">
    <div style="width:500px; margin:0 auto;">
        <? echo load_freeze_divs ("../",$permission);  ?>
    </div>
    <fieldset style="width:560px; margin:0 auto;">
        <legend>Approval Necessity Setup</legend>
        <form name="approval_necessity" id="approval_necessity" autocomplete="off">
        <table width="100%" cellspacing="2" cellpadding="0" border="1" rules="all">
            <tr>
                <td align="center">
                    <fieldset>
                        <table cellspacing="2" cellpadding="0" border="1" rules="all">
                            <tr>
                            	<td>Company</td>
                                <td><?=create_drop_down( "cbo_company_name", 140, "select comp.id,comp.company_name from lib_company comp where status_active =1 and is_deleted=0 and comp.core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_grid();fnc_LoadCompanyData(this.value+'_'+document.getElementById('txt_date').value);" ); ?>	
                                    <input type='hidden' id='mst_id' name='mst_id'>
                                </td>
                                <td style="float:left; margin-left:50px;">Setup Date </td>
                                <td style="float:left; margin-left:20px;"><input type="text" name="txt_date" id="txt_date" class="datepicker"  style="width:140px" onChange="fnc_LoadCompanyData(document.getElementById('cbo_company_name').value+'_'+this.value);"/>	 
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr height="10"></tr>
            <tr>
                <td align="center">
                    <fieldset>
                        <table align="center" cellspacing="0" cellpadding="0" id="approval_necessity_setup_1"  border="1" class="rpt_table"  rules="all">
                            <thead>
                                <th width="30">SL</th>
                                <th width="200">Approving Page Name</th>
                                <th width="80">Need</th>
                                <th width="80">Allow Partial</th>
                                <th>Validate Next Page</th>
                            </thead>
                            <tbody>
								<?
                                $i=1; $priceQuotAppArr=array(1=> "Order Entry", 2=> "Pre-Cost");
								$qcAppArr=array(1=> "Order Entry Matrix V2", 2=> "Pre-Cost V3", 3=> "Order Entry By Matrix Woven", 4=> "Pre-Costing V2-Woven");
								$ToppingAddingAppArr=array(1=> "Dyes And Chemical Issue Requisition");
								$gmtsDelivery=array(1=> "Garments Delivery Entry");
								$labAppArr=array(1=> "Pre Costing Approve", 2=> "Quick Costing Approve");
                                foreach ($approval_necessity_array as $page_id=>$page_name)
								{
									$validatePage=$blank_array;
									if($page_id==1) $validatePage=$priceQuotAppArr; 
									else if($page_id==5 || $page_id==6) $validatePage=$yes_no;
									else if($page_id==25) $validatePage=$gmtsDelivery;
									else if($page_id==28) $validatePage=$qcAppArr;
									else if($page_id==33) $validatePage=$labAppArr;
									else if($page_id==34) $validatePage=$ToppingAddingAppArr;
                                    else if($page_id==37) $validatePage=$gmtsDelivery;
									else $validatePage=$blank_array;
									?>		
									<tr>
                                        <td style="width:30px; text-align:center;"><?=$i; ?><input type="hidden" name="updateDtls_<?=$i; ?>" id="updateDtls_<?=$i; ?>" /></td>
                                        <td title="Page id: <?=$page_id;?>"><input style="width:180px;" type="hidden"  class="text_boxes" name="txt_page_id_<?=$i; ?>" id="txt_page_id_<?=$i; ?>" value= "<?=$page_id; ?>"/><?=$page_name; ?></td>
                                        <td><?=create_drop_down( "txt_need_".$i, 80, $yes_no,"", 1, "-- Select --", 0, "",0,"","","","",10); ?></td>
                                        <td><?=create_drop_down( "txt_allow_partial_".$i, 80, $yes_no,"", 1, "-- Select --", 2, "",0,"","","","",10); ?></td>
                                        <td><?=create_drop_down( "cboValidatePage_".$i,100,$validatePage,"",1,"-- Select --", "","",0,"","","","",10); ?></td>
									</tr>
                                	<?	$i++;
								} ?>
                            </tbody>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td align="center" class="button_container"><?=load_submit_buttons( $permission, "fnc_approval_necessity_setup", 0,0 ,"reset_form('approval_necessity', '','', '', '', '');",2); ?>
                </td>
            </tr>
        </table>
        </form>
        <div id="list_container" style=" margin:10px auto; width:365px;"></div>
    </fieldset>
<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
</body>
</html> 