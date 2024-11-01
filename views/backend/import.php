<style>
    .woocommerce-save-button {
        display: none !important;
    }
</style>
<div id="progress_message" class="notice notice-info inline">
    <p>
        <?php echo  __('Step', 'woo-dpd'); ?> <span id="step"> 1 </span>
        <?php echo  __('of', 'woo-dpd'); ?> 4.
        <span id="stepname"><?php echo  __('Import cities', 'woo-dpd'); ?></span>
    </p>
</div>
<div class="process">
    <div class="progress-bar-wrapper">
        <div class="progress-bar"style="width: 0%">
            <span>0%</span> 
        </div>
    </div>
</div>

<div>&nbsp;</div>

<a href="?action=importDone" class="button-primary">Прервать</a>

<script>
    <?php if ($first_run): ?>
        runImportData(0, 0);
    <?php endif;?>
</script>