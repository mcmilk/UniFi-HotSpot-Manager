<?php

/**
 * UniFi HotSpot Manager
 *
 * Copyright (c) 2017 Tino Reichardt
 * All rights reserved.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (!defined('HOTSPOT')) { exit; }

?>
<div class="row">
  <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
    <form data-toggle="validator" method="POST" action="?action=stat_voucher">
      <h2><?php echo __("Create vouchers"); ?></h2>
      <hr class="colorgraph">

      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
          <div class="form-group has-feedback">
            <div class="input-group">
              <span class="input-group-addon"><?php echo __("Number of vouchers"); ?>:</span>
              <input type="number" name="count" min="1" max="500" value="40" class="form-control input-lg" tabindex="1" required data-error="<?php echo __("Please specify the number in the range of 1-500."); ?>">
             </div>
            <span class="glyphicon form-control-feedback"></span>
            <div class="help-block with-errors"></div>
          </div>
        </div>
      </div>

      <p>
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
          <div class="form-group has-feedback">
            <div class="input-group">
              <span class="input-group-addon"><?php echo __("Limit"); ?>:</span>
              <input type="number" name="limit" min="0" max="1048576" value="0" class="form-control input-lg" tabindex="2" required data-error="<?php echo __("Please specify the limit in the range of 1-1048576."); ?>">
              <span class="input-group-addon">MB</span>
            </div>
            <span style="float: right;width: 9em;" class="glyphicon form-control-feedback"></span>
            <div class="help-block with-errors"></div>
          </div>
        </div>
      </div>

      <p>
      <div class="row">

        <div class="col-xs-12 col-sm-8 col-md-8">
          <div class="form-group has-feedback">
            <div class="input-group">
              <span class="input-group-addon"><?php echo __("Validity"); ?>:</span>
              <input type="number" name="duration" min="1" max="10800" value="480" class="form-control input-lg" tabindex="3" required data-error="<?php echo __("Please specify a value of 1-10800."); ?>">
            </div>
            <span class="glyphicon form-control-feedback"></span>
            <div class="help-block with-errors"></div>
          </div>
        </div>

        <div class="col-xs-12 col-sm-4 col-md-4">
          <div class="form-group has-feedback">
            <div class="input-group">
              <select type="text" name="dmulti" class="form-control input-lg" tabindex="4">
              <?php get_duration(); ?>
              </select>
            </div>
            <span class="glyphicon form-control-feedback"></span>
          </div>
        </div>

      </div>

      <p>
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
          <div class="form-group has-feedback"><?php echo __("Comment"); ?>:
            <input type="text" name="note" class="form-control input-lg" tabindex="5">
          </div>
        </div>
      </div>

      <p>
      <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6">
          <div class="form-group"><?php echo __("Create vouchers"); ?>:
            <input type="submit" value="<?php echo __("Create"); ?>" class="btn btn-primary btn-block btn-lg" tabindex="6">
          </div>
        </div>
      </div>

    </form>
  </div>
</div>
