(function ($) {
  'use strict';

  /**
   * All of the code for your admin-specific JavaScript source
   * should reside in this file.
   *
   * Note that this assume you're going to use jQuery, so it prepares
   * the $ function reference to be used within the scope of this
   * function.
   *
   * From here, you're able to define handlers for when the DOM is
   * ready:
   *
   * $(function() {
   *
   * });
   *
   * Or when the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and so on.
   *
   * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
   * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
   * be doing this, we should try to minimize doing that in our own work.
   */

  /**
   * Click on headlines
   */
  $(document).on('click', '.admin-quickbar-post-type', function (e) {
    let target = $(e.target),
      parent = target.parent();

    parent.toggleClass('show-list');

    setPostListStorage();
  });


  /**
   * Open sidebar and postlists on dom-ready
   */
  $(function ($) {
    let postLists = getPostListStorage();

    // open postlists
    $('.admin-quickbar-postlist').each(function (index, element) {
      if (postLists[$(element).data('post-type')]) {
        $(element).addClass('show-list');
      }
    });

    // open quickbar
    if (localStorage.adminQuickbarToggle == 'true' && localStorage.adminQuickbarKeepopen == 'true') {
      $('.admin-quickbar').addClass('toggle');
      $('body').addClass('admin-quickbar-visible');
    }

    if (localStorage.adminQuickbarKeepopen == 'true') {
      $('.admin-quickbar-keepopen input').prop('checked', true);
    }

    if (localStorage.adminQuickbarLoadthumbs == 'true') {
      $('.admin-quickbar-loadthumbs input').prop('checked', true);
      loadThumbs();
    }

    if (localStorage.adminQuickbarOverlap == 'true') {
      $('.admin-quickbar-overlap input').prop('checked', true);
      $('body').addClass('admin-quickbar-is-overlap');
    }
  });

  /**
   * Open Sidebar
   */
  $(document).on('click', '.toggle-quickbar-button', function (e) {
    $('.admin-quickbar').toggleClass('toggle');
    $('body').toggleClass('admin-quickbar-visible');
    localStorage.adminQuickbarToggle = $('.admin-quickbar').hasClass('toggle');
  });

  /**
   * Keep open
   */
  $(document).on('change', '.admin-quickbar-keepopen input', function (e) {
    localStorage.adminQuickbarKeepopen = $('.admin-quickbar-keepopen input').is(':checked');
  });

  /**
   * Keep open
   */
  $(document).on('change', '.admin-quickbar-overlap input', function (e) {
    localStorage.adminQuickbarOverlap = $('.admin-quickbar-overlap input').is(':checked');

    if (localStorage.adminQuickbarOverlap == 'true') {
      $('body').addClass('admin-quickbar-is-overlap');
    } else {
      $('body').removeClass('admin-quickbar-is-overlap');
    }
  });

  /**
   * Load thumbs
   */
  $(document).on('change', '.admin-quickbar-loadthumbs input', function (e) {
    localStorage.adminQuickbarLoadthumbs = $('.admin-quickbar-loadthumbs input').is(':checked');

    if (localStorage.adminQuickbarLoadthumbs == 'true') {
      $('.admin-quickbar-loadthumbs input').prop('checked', true);
      loadThumbs();
    } else {
      jQuery('.admin-quickbar .wp-post-image').prop('src', '');
    }
  });

})(jQuery);

/**
 * Set localStorage
 */
function setPostListStorage() {
  let postListStorage = {};
  jQuery('.admin-quickbar-postlist').each(function (index, element) {
    postListStorage[jQuery(element).data('post-type')] = jQuery(element).hasClass('show-list');
  });

  localStorage.postList = JSON.stringify(postlistStorage);
}

/**
 * Get localStorage
 */
function getPostListStorage() {
  if (!localStorage.postList || typeof (localStorage.postList) != 'string') {
    localStorage.postList = '{}';
  }
  return JSON.parse(localStorage.postList);
}

function loadThumbs() {
  jQuery('.admin-quickbar .wp-post-image').each(function (index, element) {
    jQuery(element).prop('src', jQuery(element).data('src'));
  });
}