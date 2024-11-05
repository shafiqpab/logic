<?
	require_once('../includes/common.php');
	echo load_html_head_contents("Database Upload", "../", 1, 1,'','','');
?>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
<br />
    <fieldset style="width:420px;">
    <legend>Batch Creation</legend> 
		<form name="batchcreation_1" id="batchcreation_1" action="restore_db.php" enctype="multipart/form-data" method="post"> 
            <table width="400" align="center" border="0">
                <tr>
                    <td width="110" class="must_entry_caption"><b>Server Name</b></td>
                    <td>
                        <input type="text" class="text_boxes" name="serverName" id="serverName" required value="" style="width:200px" />
                    </td>
                </tr>
                <tr>
                    <td width="110" class="must_entry_caption"><b>User Name</b></td>
                    <td>
                        <input type="text" class="text_boxes" name="userName" id="userName" required value="" style="width:200px" />
                    </td>
                </tr>
                <tr>
                    <td width="110"><b>Password</b></td>
                    <td>
                        <input type="password" class="text_boxes" name="password" id="password" value="" style="width:200px" />
                    </td>
                </tr>
                <tr>
                    <td width="110" class="must_entry_caption"><b>Database Name</b></td>
                    <td>
                        <input type="text" class="text_boxes" name="dbName" id="dbName" required value="" style="width:200px" />
                    </td>
                </tr>
                <tr>
                    <td width="110" class="must_entry_caption"><b>Select Database</b></td>
                    <td>
                        <input type="file" id="uploadfile" name="uploadfile" class="image_uploader" required style="width:210px" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" name="submit" value="Restore" class="formbutton" style="width:100px" />
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>