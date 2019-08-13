<script>
  jQuery(function ($) {
    if (localStorage.adminQuickbarDarkmode === 'true') {
      $('body').addClass('admin-quickbar-is-darkmode');
    } else {
      $('body').removeClass('admin-quickbar-is-darkmode');
    }

    if (localStorage.adminQuickbarHideOnWebsite === 'true') {
      $('.admin-quickbar-jumpicons').css({display: 'none'});
    }
  });
</script>