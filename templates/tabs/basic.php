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
        <div class="mb-3 col-md-6">
          <label for="cbxinterface"><?php echo _("Interface") ;?></label>
            <?php SelectorOptions('interface', $interfaces, $arrConfig['interface'], 'cbxinterface'); ?>
        </div>
      </div>

    </div>
  </div>
</div><!-- /.tab-pane | basic tab -->

