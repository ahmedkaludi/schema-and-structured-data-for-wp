<?php 
$reasons = array(
    		1 => '<li><label><input type="radio" name="saswp_disable_reason" value="temporary"/>' . __('It is only temporary', 'saswp-for-wp') . '</label></li>',
		2 => '<li><label><input type="radio" name="saswp_disable_reason" value="stopped"/>' . __('I stopped using Schema plugin on my site', 'saswp-for-wp') . '</label></li>',
		3 => '<li><label><input type="radio" name="saswp_disable_reason" value="missing"/>' . __('I miss a feature', 'saswp-for-wp') . '</label></li>
		<li><input class="mb-box missing" type="text" name="saswp_disable_text[]" value="" placeholder="Please describe the feature"/></li>',
		4 => '<li><label><input type="radio" name="saswp_disable_reason" value="technical"/>' . __('Technical Issue', 'saswp-for-wp') . '</label></li>
		<li><textarea class="mb-box technical" name="saswp_disable_text[]" placeholder="' . __('How Can we help? Please describe your problem', 'saswp-for-wp') . '"></textarea></li>',
		5 => '<li><label><input type="radio" name="saswp_disable_reason" value="another plugin"/>' . __('I switched to another plugin', 'saswp-for-wp') .  '</label></li>
		<li><input class="mb-box another" type="text" name="saswp_disable_text[]" value="" placeholder="Name of the plugin"/></li>',
		6 => '<li><label><input type="radio" name="saswp_disable_reason" value="other"/>' . __('Other reason', 'saswp-for-wp') . '</label></li>
		<li><textarea class="mb-box other" name="saswp_disable_text[]" placeholder="' . __('Please specify, if possible', 'saswp-for-wp') . '"></textarea></li>',
    );
shuffle($reasons);
?>


<div id="saswp-reloaded-feedback-overlay" style="display: none;">
    <div id="saswp-reloaded-feedback-content">
	<form action="" method="post">
	    <h3><strong><?php _e('If you have a moment, please let us know why you are deactivating:', 'saswp-for-wp'); ?></strong></h3>
	    <ul>
                <?php 
                foreach ($reasons as $reason){
                    echo $reason;
                }
                ?>
	    </ul>
	    <?php if ($email) : ?>
    	    <input type="hidden" name="saswp_disable_from" value="<?php echo $email; ?>"/>
	    <?php endif; ?>
	    <input id="saswp-reloaded-feedback-submit" class="button button-primary" type="submit" name="saswp_disable_submit" value="<?php _e('Submit & Deactivate', 'saswp-for-wp'); ?>"/>
	    <a class="button"><?php _e('Only Deactivate', 'saswp-for-wp'); ?></a>
	    <a class="saswp-for-wp-feedback-not-deactivate" href="#"><?php _e('Don\'t deactivate', 'saswp-for-wp'); ?></a>
	</form>
    </div>
</div>