<?php

/**
 * Provide a BoxOfHope Order area view for the plugin
 *
 * This file is used to markup the woocommerce-admin-facing aspects of the plugin.
 *
 * @link       https://boms-it.pl/boxofhope
 * @since      1.0.0
 *
 * @package    Boxofhope_Return_Label_Plugin
 * @subpackage Boxofhope_Return_Label_Plugin/woocomerce/partials
 */
?>

<div class="wrap">
    <p>
      <?php echo __('By joining our initiative, you are helping to maintain the natural environment by
                        promoting the idea of a closed-loop economy. With your commitment, we can reduce waste
                        and make better use of resources. Together, we can do a lot of good for our planet. <br/> <br/>
                        That\'s why we encourage you to take advantage of this functionality and enable your
                        customers to easily donate unwanted items to charity. Together, we can work for the
                        benefit of the local community and the natural environment, making the world a better place.',
                    'boxofhope-return-label-plugin' )?>
    </p>
    <?php if ($existing_record): ?>
        <p>
            <strong><?php echo __('<a href="boxofhope.pl/en" target="_blank">BoxOfHope.pl</a> delivery number: ', 'boxofhope-return-label-plugin' ) ?></strong>
            <?php echo $existing_record->delivery_id ?>
        </p>
        <p>
            <strong><?php echo __('<a href="boxofhope.pl/en" target="_blank">BoxOfHope.pl</a> return code: ', 'boxofhope-return-label-plugin')?></strong>
            <?php echo $existing_record->return_code ?>
        </p>

        <form method="post">
            <input type="hidden" name="wc_order_action" value="boh_return_plugin_download_label_action">
            <input type="submit" name="download_label" value="<?php echo __('Download BoxOfHope label', 'boxofhope-return-label-plugin') ?>" class="button-primary">
        </form>
    <?php else: ?>
          <form method="post">
            <input type="hidden" name="wc_order_action" value="boh_return_plugin_generate_label_action">
            <input type="submit" name="download_label" value="<?php echo __('Generate BoxOfHope label', 'boxofhope-return-label-plugin') ?>" class="button-primary">
        </form>
    <?php endif; ?>
</div>