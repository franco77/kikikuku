
<style type="text/css">
    .detail_prod{
        width: 100%;
        display: block;
    }
</style>

<div class="container">
  <div class="content gutter">
      <section class="product-push homepage" data-module="productpush, view360" style="padding: 0px;background: #fff;padding-top: 30px;">
          <div class="row" style="padding: 15px;">
              <div class="col-lg-offset-1 col-md-offset-1 col-lg-10 col-md-10 col-sm-12 col-xs-12">
              <img src="<?php echo base_url('assets/img/logo_light.png'); ?>" alt="Dotshop Logo" style="width: 20em; display: block; text-align: center; "><br/><br/>
              </div>
          </div>
          <?php foreach($terms->result() as $data): ?>
            <?php echo $data->CONTENT; ?>
          <?php endforeach; ?>
      </section>
  </div>
</div>
<script type="text/javascript">

</script>
