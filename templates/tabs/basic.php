<div class="tab-pane active" id="dantesettings">
  <h4 class="mt-3"><?php echo _("Basic settings"); ?></h4>
  <div class="row">
    <div class="mb-3 col-12 mt-2">
      <div class="row">
        <div class="col-12">
          <?php echo htmlspecialchars($content); ?>
        </div>
      </div>

      <div class="row mt-3">
        <div class="mb-3 col-md-4">
          <label for="txtinternal"><?php echo _("Internal interface") ;?></label>
            <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="auto" title="<?php echo _("The listening network interface or IP addresses that can connect."); ?>"></i>
            <input type="text" class="form-control ip_address" name="txtinternal">
        </div>
        <div class="mb-3 col-md-2">
          <label for="txtport"><?php echo _("Port") ;?></label>
            <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="auto" title="<?php echo _("The port that the service is running on."); ?>"></i>
            <input type="text" class="form-control" name="txtport">
        </div>
      </div>
      <div class="row">
        <div class="mb-3 col-md-6">
          <label for="cbxinterface"><?php echo _("External interface") ;?></label>
            <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="auto" title="<?php echo _("The network interface used for outbound SOCKS proxy connections."); ?>"></i>
            <?php SelectorOptions('interface', $interfaces, $arrConfig['interface'], 'cbxinterface'); ?>
        </div>
      </div>

      <div class="row">
        <div class="mb-3 col-md-6">
          <label for="txtclientip"><?php echo _("Allowed client IPs") ;?></label>
            <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="auto" title="<?php echo _("For security, restrict access to specific client IP addresses. CIDR notation is supported."); ?>"></i>
              <input type="text" class="form-control cidr" name="txtclientip">
          </div>
      </div>

    </div>
  </div>
</div><!-- /.tab-pane | basic tab -->

