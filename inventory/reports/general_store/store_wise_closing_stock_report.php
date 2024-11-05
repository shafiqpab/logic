<?
/*-------------------------------------------- Comments
Purpose     :   This form will create Closing Stock Report
              
Functionality : 
JS Functions  :
Created by    : Jahid
Creation date   :   08-11-2016
Updated by    :     
Update date   :        
QC Performed BY :   
QC Date     : 
Comments    :
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Closing Stock Report of Accessories","../../../", 1, 1, $unicode,1,''); 
//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id,item_cate_id FROM user_passwd where id=$user_id");
$cre_company_id = $userCredential[0][csf('company_id')];
$cre_store_location_id = $userCredential[0][csf('store_location_id')];
$cre_item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($cre_company_id !='') {
    $company_credential_cond = " and comp.id in($cre_company_id)";
}
if ($cre_store_location_id !='') {
    $store_location_credential_cond = " and a.id in($cre_store_location_id)"; 
}
if($cre_item_cate_id !='') {
    $item_cate_credential_cond = $cre_item_cate_id ;  
}
//========== user credential end ==========
//var_dump($item_category);
?>  
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
  
function generate_report(operation)
{
    if( form_validation('cbo_company_name','Company Name')==false )
    {
      return;
    }
    var report_title=$( "div.form_caption" ).html(); 
    var cbo_company_name = $("#cbo_company_name").val();
    var cbo_item_category_id = $("#cbo_item_category_id").val();
    var txt_product_id = $("#txt_product_id").val();
    var item_group_id = $("#txt_item_group_id").val();
    var item_account_id = $("#txt_item_account_id").val();
    var txt_item_code = $("#txt_item_code").val();
    var cbo_store_name = $("#cbo_store_name").val();
    var from_date = $("#txt_date_from").val();
    var to_date = $("#txt_date_to").val();
    var cbo_search_by = $("#cbo_search_by").val();
    var cbo_buyer_id = $("#cbo_buyer_id").val();
    var cbo_value_with = $("#cbo_value_with").val();
  var cbo_get_upto_qnty = $("#cbo_get_upto_qnty").val();
  var txt_qnty = $("#txt_qnty").val();


    var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_store_name="+cbo_store_name+"&cbo_item_category_id="+cbo_item_category_id+"&txt_item_code="+txt_item_code+"&txt_product_id="+txt_product_id+"&from_date="+from_date+"&to_date="+to_date+"&item_account_id="+item_account_id+"&item_group_id="+item_group_id+"&cbo_search_by="+cbo_search_by+"&cbo_buyer_id="+cbo_buyer_id+"&cbo_value_with="+cbo_value_with+"&cbo_get_upto_qnty="+cbo_get_upto_qnty+"&txt_qnty="+txt_qnty+"&report_title="+report_title;
    var data="action=generate_report"+dataString;
    //alert (data);
    freeze_window(operation);
    http.open("POST","requires/store_wise_closing_stock_report_controller.php",true);
    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    http.send(data);
    http.onreadystatechange = generate_report_reponse;  
}
  
function generate_report_reponse()
{ 
    if(http.readyState == 4) 
    {  
      var reponse=trim(http.responseText).split("**");
      $("#report_container2").html(reponse[0]);  
      document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
      if(reponse[2]!=3) {
          setFilterGrid("table_body",-1);
      }
      show_msg('3');
      release_freezing();
    }
}
  
function new_window()
{
    document.getElementById('scroll_body').style.overflow="auto";
    document.getElementById('scroll_body').style.maxHeight="none"; 
    $('#scroll_body tr:first').hide();
    var w = window.open("Surprise", "#");

    var d = w.document.open();
    d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
    d.close(); 
    document.getElementById('scroll_body').style.overflow="auto"; 
    document.getElementById('scroll_body').style.maxHeight="250px";
    $('#scroll_body tr:first').show();
}

function generate_report_exel_only(operation)
{
  if(operation==1)
  {
    if( form_validation('cbo_company_name','Company Name')==false )
    {
      return;
    }
    var report_title=$( "div.form_caption" ).html(); 
    var cbo_company_name = $("#cbo_company_name").val();
    var cbo_item_category_id = $("#cbo_item_category_id").val();
    var txt_product_id = $("#txt_product_id").val();
    var item_group_id = $("#txt_item_group_id").val();
    var item_account_id = $("#txt_item_account_id").val();
    var txt_item_code = $("#txt_item_code").val();
    var cbo_store_name = $("#cbo_store_name").val();
    var from_date = $("#txt_date_from").val();
    var to_date = $("#txt_date_to").val();
    var cbo_search_by = $("#cbo_search_by").val();
    var cbo_buyer_id = $("#cbo_buyer_id").val();
    var cbo_value_with = $("#cbo_value_with").val();
    var cbo_get_upto_qnty = $("#cbo_get_upto_qnty").val();
    var txt_qnty = $("#txt_qnty").val();

    var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_store_name="+cbo_store_name+"&cbo_item_category_id="+cbo_item_category_id+"&txt_item_code="+txt_item_code+"&txt_product_id="+txt_product_id+"&from_date="+from_date+"&to_date="+to_date+"&item_account_id="+item_account_id+"&item_group_id="+item_group_id+"&cbo_search_by="+cbo_search_by+"&cbo_buyer_id="+cbo_buyer_id+"&cbo_value_with="+cbo_value_with+"&cbo_get_upto_qnty="+cbo_get_upto_qnty+"&txt_qnty="+txt_qnty+"&report_title="+report_title;
    var data="action=report_generate_exel_only"+dataString;
    //alert (data);
    freeze_window(2);
    http.open("POST","requires/store_wise_closing_stock_report_controller.php",true);
    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    http.send(data);
    http.onreadystatechange = generate_report_reponse_exel_only;
  }
}

  function generate_report_reponse_exel_only()
  {
    if(http.readyState == 4) 
    {  
      var reponse=trim(http.responseText).split("####");

      if(reponse!='')
      {
        $('#aa1').removeAttr('href').attr('href','requires/'+reponse[0]);
        document.getElementById('aa1').click();
      }
      show_msg('3');
      release_freezing();
    }
  }
  
function change_color(v_id,e_color)
{
    if (document.getElementById(v_id).bgColor=="#33CC00")
    {
      document.getElementById(v_id).bgColor=e_color;
    }
    else
    {
      document.getElementById(v_id).bgColor="#33CC00";
    }
}
  
function openmypage_itemgroup()
{
    /*if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false )
    {
      return;
    }*/
    var company = $("#cbo_company_name").val(); 
    var cbo_item_category_id = $("#cbo_item_category_id").val();
    var txt_item_group = $("#txt_item_group").val();
    var txt_item_group_id = $("#txt_item_group_id").val();
    var txt_item_group_no = $("#txt_item_group_no").val();
    var page_link='requires/store_wise_closing_stock_report_controller.php?action=item_group_such_popup&company='+company+'&cbo_item_category_id='+cbo_item_category_id+'&txt_item_group='+txt_item_group+'&txt_item_group_id='+txt_item_group_id+'&txt_item_group_no='+txt_item_group_no;
    var title="Search Item Popup";
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=370px,center=1,resize=0,scrolling=0','../../')
    emailwindow.onclose=function()
    {
      var theform=this.contentDoc.forms[0];
      var item_group_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
      var item_group_des=this.contentDoc.getElementById("txt_selected").value; // product Description
      var item_group_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
      //alert(style_no);
      $("#txt_item_group").val(item_group_des);
      $("#txt_item_group_id").val(item_group_id); 
      $("#txt_item_group_no").val(item_group_no);
    }
}
  
function openmypage_itemaccount()
{
    /*if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false )
    {
      return;
    }*/
    var company = $("#cbo_company_name").val(); 
    var cbo_item_category_id = $("#cbo_item_category_id").val();
    var txt_item_acc = $("#txt_item_acc").val();
    var txt_item_account_id = $("#txt_item_account_id").val();
    var txt_item_acc_no = $("#txt_item_acc_no").val();
    var page_link='requires/store_wise_closing_stock_report_controller.php?action=item_account_such_popup&company='+company+'&cbo_item_category_id='+cbo_item_category_id+'&txt_item_acc='+txt_item_acc+'&txt_item_account_id='+txt_item_account_id+'&txt_item_acc_no='+txt_item_acc_no;
    var title="Search Item Popup";
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=735px,height=400px,center=1,resize=0,scrolling=0','../../')
    emailwindow.onclose=function()
    {
      var theform=this.contentDoc.forms[0];
      var item_acc_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
      var item_acc_des=this.contentDoc.getElementById("txt_selected").value; // product Description
      var item_acc_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
      //alert(style_no);
      $("#txt_item_acc").val(item_acc_des);
      $("#txt_item_account_id").val(item_acc_id); 
      $("#txt_item_acc_no").val(item_acc_no);
    }
}
  
function fn_buyer_visibility(search_type)
{
    if(search_type==2 || search_type==4)
    {
      $('#show_textcbo_buyer_id').prop('disabled', true);
    }
    else
    {
      $('#show_textcbo_buyer_id').prop('disabled', false);
    }
} 
  
function getCompanyId() 
{  
    var company_id = document.getElementById('cbo_company_name').value;
    var search_type = document.getElementById('cbo_search_by').value;

    if(company_id !='') {
      var data="action=load_drop_down_store__load_drop_down_buyer&choosenCompany="+company_id;
      http.open("POST","requires/store_wise_closing_stock_report_controller.php",true);
      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
      http.send(data); 
      http.onreadystatechange = function(){
          if(http.readyState == 4) 
          {
              var response = trim(http.responseText).split("**");
              $('#store_td').html(response[0]);
              $('#buyer_td').html(response[1]);
              set_multiselect('cbo_store_name','0','0','','0');
              set_multiselect('cbo_buyer_id','0','0','','0');
              fn_buyer_visibility(search_type);
          }      
      };
    }         
}

function openmypage_stock(prod_id,store_id,date_form,date_to,uom,buyer,action)
{
    //emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=370px,center=1,resize=0,scrolling=0','../../')
  //alert(prod_id+"="+store_id+"="+date_form+"="+date_to+"="+action);return;
  //var page_link='requires/store_wise_closing_stock_report_controller.php?action=item_group_such_popup&company='+company+'&cbo_item_category_id='+cbo_item_category_id+'&txt_item_group='+txt_item_group+'&txt_item_group_id='+txt_item_group_id+'&txt_item_group_no='+txt_item_group_no;
  var page_link='requires/store_wise_closing_stock_report_controller.php?prod_id='+prod_id+'&store_id='+store_id+'&date_form='+date_form+'&date_to='+date_to+'&uom='+uom+'&buyer='+buyer+'&action='+action;
  //alert(page_link);
    var title="Stock Details";
  emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=865px,height=350px,center=1,resize=0,scrolling=0','../../');
}


</script>

</head>

<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />        
        <form name="closingstock_1" id="closingstock_1" autocomplete="off" > 
        <h3 style="width:1180px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel" style="width:1170px" >      
        <fieldset>  
        <table class="rpt_table" width="1170" cellpadding="0" cellspacing="0" border="0" rules="all">
            <thead>
              <th width="130" class="must_entry_caption">Company</th>
              <th width="130">Store</th> 
              <th width="130" style="display:none;">Item Category</th>
              <th width="90">Item Group</th>
              <th width="90">Item Account</th>
              <th width="80">Search by</th>
              <th width="130">Buyer</th>
              <th width="80">Value</th>
              <th width="70">Get Upto</th>
              <th width="60">Qty.</th>
              <th>Date</th>
              <th width="130"><input type="reset" name="res" id="res" value="Reset" style="width:50px" class="formbutton" onClick="reset_form('closingstock_1','report_container*report_container2','','','')" /></th>
            </thead>
            <tbody>
                <tr class="general">
                    <td align="center" id="td_company">
                      <? 
                       echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_credential_cond order by company_name","id,company_name", "", "--Select Company--", $selected, "" );//load_drop_down( 'requires/store_wise_closing_stock_report_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );
                      ?>                            
                    </td>

                    <td id="store_td" align="center">
                      <? 
                        echo create_drop_down( "cbo_store_name", 130, $blank_array,"", "", "--Select Store--", "", "","");
                      ?>
                    </td>

                    <td align="center" style="display:none;">
                      <?
                        echo create_drop_down( "cbo_item_category_id", 130, $general_item_category,"", 0, "", 0, "", 0,"4" );
                      ?>                            
                    </td>
                    <td align="center">
                      <input style="width:90px;"  name="txt_item_group" id="txt_item_group" onDblClick="openmypage_itemgroup()" class="text_boxes" placeholder="Browse"/>   
                      <input type="hidden" name="txt_item_group_id" id="txt_item_group_id"/>    <input type="hidden" name="txt_item_group_no" id="txt_item_group_no"/>
                    </td>
                    <td align="center">
                      <input style="width:90px;"  name="txt_item_acc" id="txt_item_acc" onDblClick="openmypage_itemaccount()" class="text_boxes" placeholder="Browse"/>   
                      <input type="hidden" name="txt_item_account_id" id="txt_item_account_id"/>    <input type="hidden" name="txt_item_acc_no" id="txt_item_acc_no"/>
                    </td>
                    <td align="center">
                    <? 
                      $search_by_arr=array(1=>'Accessories',2=>'Item Group Wise',3=>'Store Wise',4=>'General Accessories');
                      echo create_drop_down( "cbo_search_by", 80, $search_by_arr,"", 0, "", 1, "fn_buyer_visibility(this.value)" );
                    ?>
                    </td>

                    <td id="buyer_td">
                      <?
                        echo create_drop_down( "cbo_buyer_id", 130,$blank_array,"", "", "-- Select Buyer--", $selected, "",1,"","","","");
                      ?> 
                    </td>

                    <td>
                      <?   
                        $valueWithArr=array(0=>'Value With 0',1=>'Value Without 0');
                        echo create_drop_down( "cbo_value_with", 80, $valueWithArr,"",0,"",1,"","","");
                      ?>
                    </td>
                    <td> 
                      <?
                        echo create_drop_down( "cbo_get_upto_qnty", 70, $get_upto,"", 1, "- All -", 0, "",0 );
                      ?>
                    </td>
                    <td>
                        <input type="text" id="txt_qnty" name="txt_qnty" class="text_boxes_numeric" style="width:40px" value="" />
                    </td>
                    <td align="center">
                      <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:50px;" readonly />                                  
                      To
                      <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y", time()- 86400);?>" class="datepicker" style="width:50px;" readonly />                        
                    </td>
                    <td align="center">
                      <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:50px" class="formbutton" />
                      <input type="button" name="search" id="search3" value="Excel Only" onClick="generate_report_exel_only(1)" style="width:70px;" class="formbutton" />
                      <a href="" id="aa1"></a>
                    </td>
                </tr>
            </tbody>
          <tfoot>
              <tr>
                  <td colspan="11" align="center"><? echo load_month_buttons(1);  ?></td>
              </tr>
          </tfoot>
        </table> 
        </fieldset> 
        </div>
        <br /> 
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div> 
        </form>    
    </div>
</body> 
<script>
set_multiselect('cbo_company_name*cbo_store_name*cbo_item_category_id','0*0*0','0','','0*0*0');

setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_name,'0');getCompanyId();") ,3000)];  
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
