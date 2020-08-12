<script>
  if (localStorage.adminQuickbarDarkmode === 'true') {
    jQuery('body').addClass('admin-quickbar-is-darkmode');
  } else {
    jQuery('body').removeClass('admin-quickbar-is-darkmode');
  }

  if (localStorage.adminQuickbarHideOnWebsite === 'true') {
    jQuery('.admin-quickbar-jumpicons').css({display: 'none'});
  }


  jQuery(document).on('click', '.aqb-icon-external', openWindow);

  function openWindow() {
    let url = jQuery(this).next('.dashicons-edit').attr('href') + '&noaqb';
    window.open(url, '_blank', 'width=700,height=500,left=200,top=100');
  }
</script>