<?php
    /*
     * Code to get the contacted users list
     * */
	
    $start = 0;
    $limit = 30;
	
	if($_GET['start'] != "" && is_numeric($_GET['start'])) {
		$start = (int)$_GET['start'];
	}
	
	//Get the total records count
	$total_results = $wpdb->get_results("SELECT COUNT(sguest_id) AS total_count FROM ".$wpdb->prefix."sgcontact_information");
	$count 		   = $total_results[0]->total_count;
	
	//Get the users information
    $sel_user_info = "SELECT * FROM ".$wpdb->prefix."sgcontact_information LIMIT $start, $limit";
    $res_user_info = $wpdb->get_results($sel_user_info);

	
	//Code to delete the user
	if($_GET['salt'] != "" && $_GET['key'] != "" && is_numeric($_GET['key'])) {
		
		$user_key = (int)$_GET['key'];
		
		$delete_user_by_key   = "DELETE FROM ".$wpdb->prefix."sgcontact_information WHERE sguest_id = ".$user_key;
		$result_user_del_info = $wpdb->query($delete_user_by_key);
		
		if($result_user_del_info) $del_status = 'd=s'; else $del_status = 'd=f';
		?>
		<script>
			location.href = "<?php echo get_option('siteurl').'/wp-admin/admin.php?page=key-handle-menu&'.$del_status;?>";
		</script>
		<?php
		
	}
	
    ?>
<h1 style="color:#21759B">Contact Users List</h1>

<table cellspacing="0" class="wp-list-table widefat fixed posts">
	<thead>
	<tr>
		<th class="manage-column" id="name" scope="col" width="10%">
            <a href="javascript:void(0);">
                <span>Name</span>
                <span class="sorting-indicator"></span>
            </a>
        </th>

        <th class="manage-column" id="email" scope="col" width="14%">
            <a href="javascript:void(0);">
                <span>Email</span>
                <span class="sorting-indicator"></span>
            </a>
        </th>

        <th class="manage-column" scope="col" width="16%">
            <a href="javascript:void(0)">
                <span>Subject</span>
                <span class="sorting-indicator"></span>
            </a>
        </th>

        <th class="manage-column" scope="col">
            <a href="javascript:void(0)">
                <span>Message</span>
                <span class="sorting-indicator"></span>
            </a>
        </th>

        <th class="manage-column" scope="col" width="8%">
            <a href="javascript:void(0)">
                <span>Date</span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
    </tr>
	</thead>

	<tfoot>
	<tr>
        <th class="manage-column" scope="col">
            <a href="javascript:void(0)"><span>Name</span>
                <span class="sorting-indicator"></span>
            </a>
        </th>

        <th class="manage-column" scope="col">
            <a href="javascript:void(0)">
                <span>Email</span>
                <span class="sorting-indicator"></span>
            </a>
        </th>

        <th class="manage-column" scope="col">
            <a href="javascript:void(0)">
                <span>Subject</span>
                <span class="sorting-indicator"></span>
            </a>
        </th>

        <th class="manage-column" scope="col">
            <a href="javascript:void(0)">
                <span>Message</span>
                <span class="sorting-indicator"></span>
            </a>
        </th>

        <th class="manage-column" scope="col">
            <a href="javascript:void(0)">
                <span>Date</span>
                <span class="sorting-indicator"></span>
            </a>
        </th>

    </tr>
	</tfoot>

	<tbody id="the-list">
	<?php 
	if($_GET['d'] == 's') {
		?>
		<tr><td colspan="5" align="center"><span class="errormessage"><?php echo "Deleted successfully";?></span></td></tr>
		<?php
	}
	else if($_GET['d'] == 'f') {
		?>
		<tr><td colspan="5" align="center"><span class="errormessage"><?php echo "Failed to delete";?></span></td></tr>
		<?php
	}
	?>
    <?php
    if(count($res_user_info) > 0) {
			for($j = 0; $j < count($res_user_info); $j++) {
				?>
				<tr valign="top">
					<td class="column-title">
						<strong>
							<a href="javascript:void(0)" class="row-title">
								<?php echo $res_user_info[$j]->sgname;?>
							</a>
						</strong>
		
						<div class="row-actions">
							<span class="inline hide-if-no-js">
								<a class="editinline" href="?key=<?php echo $res_user_info[$j]->sguest_id;?>&page=key-handle-menu&action=delete&salt=<?php echo md5(crypt(rand().rand().time()));?>">Delete</a>
							</span>
						</div>
		
					</td>
					<td class="column-title"><?php echo $res_user_info[$j]->sgemail;?></td>
					<td class="column-title"><?php echo $res_user_info[$j]->sgsubject;?></td>
					<td class="column-title"><?php echo $res_user_info[$j]->sgmessage;?></td>
					<td class="date column-date"><?php echo $res_user_info[$j]->sgcreated_date;?></td>
				</tr>
				<?php
			}
	}
	else{
	?>
	<tr>
		<td colspan="5"><div class="sinformation_pagination"><h1>No records</h1></div></td>
	</tr>
	<?php	
	}
	if($count > $limit) {
    ?>
	<tr>
		<td colspan="5"><div class="sinformation_pagination"><?php pagination($start, $limit, $count, '', '&page=key-handle-menu');?></div></td>
	</tr>
	<?php
	}
	?>
	
    </tbody>
</table>