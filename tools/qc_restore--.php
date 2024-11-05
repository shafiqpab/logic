<?
	require_once('../includes/common.php');
	echo load_html_head_contents("Database Upload", "../", 1, 1,'','','');
	//echo count(explode(",","'168','NKD','NKD','N/A','','1,5,6,20,23','','1','0','','','','','','','','0','0','0','1','0','1','1','1','0','0','0','0','','0','1','1','2','0'"));
?>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
<br />
    <fieldset style="width:420px;">
    <legend>QC Restore DB</legend> 
		<form name="qcrestore_1" id="qcrestore_1" action="qc_restore_data.php" enctype="multipart/form-data" method="post"> 
            <table width="400" align="center" border="0">
                <tr>
                    <td width="110" class="must_entry_caption"><b>Select Database</b></td>
                    <td>
                        <input type="file" id="uploadfile" name="uploadfile" class="image_uploader" style="width:210px" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" name="submit" id="submit" value="Restore" class="formbutton" style="width:100px" />
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>