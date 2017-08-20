<?php

/**
 * UniFi HotSpot Manager
 *
 * Copyright (c) 2017 Tino Reichardt
 * All rights reserved.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * Form for langadd modal.
 */

if (!defined('HOTSPOT')) { exit; }

?>

<form data-toggle="validator" method="POST" action="">
  <fieldset>

    <div class="form-group has-feedback">
      <div class="input-group">
        <span class="input-group-addon"><?php echo __("Code"); ?>:</span>
        <input type="text" name="id" required class="form-control" placeholder="en">
      </div>
      <span class="glyphicon form-control-feedback"></span>
      <div class="help-block with-errors"></div>
    </div>

    <div class="form-group has-feedback">
      <div class="input-group">
        <span class="input-group-addon"><?php echo __("Language"); ?>:</span>
        <input type="text" name="name" required class="form-control" placeholder="English">
      </div>
      <span class="glyphicon form-control-feedback"></span>
      <div class="help-block with-errors"></div>
    </div>

    <div class="form-group has-feedback">
      <div class="input-group">
        <span class="input-group-addon"><?php echo __("Browser"); ?>:</span>
        <input type="text" name="browser" required class="form-control" placeholder="en,en-GB,en-US">
      </div>
      <span class="glyphicon form-control-feedback"></span>
      <div class="help-block with-errors"></div>
    </div>

  </fieldset>
</form>
