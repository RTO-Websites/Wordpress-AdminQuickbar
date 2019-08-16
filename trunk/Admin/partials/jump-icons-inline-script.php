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


    $(document).on('click', '.aqb-icon-external', openWindow);

    function openWindow() {
      let url = $(this).next('.dashicons-edit').attr('href')  + '&noaqb';
      window.open(url, '_blank', 'width=700,height=500,left=200,top=100');
    }
  });
</script>