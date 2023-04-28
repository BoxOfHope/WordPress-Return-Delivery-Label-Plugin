<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://boms-it.pl/boxofhope
 * @since      1.0.0
 *
 * @package    Boxofhope_Return_Label_Plugin
 * @subpackage Boxofhope_Return_Label_Plugin/admin/partials
 */
?>

<div class="wrap">
  <div id="icon-themes" class="icon32"></div>  
    <h2><?php echo __('BoxOfHope Return Label Plugin Configuration', 'boxofhope-return-label-plugin') ?></h2>
    <p style="font-size: 14px"><?php echo __('By joining our initiative, you are helping to maintain the natural environment by
                        promoting the idea of a closed-loop economy. With your commitment, we can reduce waste
                        and make better use of resources. Together, we can do a lot of good for our planet. <br/> <br/>
                        That\'s why we encourage you to take advantage of this functionality and enable your
                        customers to easily donate unwanted items to charity. Together, we can work for the
                        benefit of the local community and the natural environment, making the world a better place.',
                    'boxofhope-return-label-plugin' )?>
    </p>

    <form method="post" action="options.php">
    <?php
        settings_errors( 'boh_return_label_plugin_configuration_errors' );
        settings_fields( 'boh_return_label_plugin_configuration' );
		do_settings_sections( 'boh_return_label_plugin' );
		submit_button();
     ?>
    </form>
</div>