<table class="form-table">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_recipient_need_pass">
                <?php echo __('Require Pass', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_recipient_need_pass">
					<input type="hidden" name="dpd[recipient_need_pass]" value="0">
					<input class=""
						   type="checkbox"
						   name="dpd[recipient_need_pass]"
						   id="dpd_recipient_need_pass"
						   value="1"
						   <?php echo checked(get_option('dpd_recipient_need_pass')); ?>
					>
                </label>
            </fieldset>
        </td>
    </tr> 
</table>