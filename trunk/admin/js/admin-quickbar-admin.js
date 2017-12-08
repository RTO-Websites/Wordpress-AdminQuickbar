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
    var target = $(e.target),
      parent = target.parent();

    parent.toggleClass('show-list');

    setPostListStorage();
  });


  /**
   * Open sidebar and postlists on dom-ready
   */
  $(function($) {
    var postLists = getPostListStorage();

    // open postlists
    $('.admin-quickbar-postlist').each(function (index, element) {
      if (postLists[$(element).data('post-type')]) {
        $(element).addClass('show-list');
      }
    });

    // open quickbar
    if (localStorage.adminQuickbarToggle == 'true') {
      $('.admin-quickbar').addClass('toggle');
    }
  });

  /**
   * Open Sidebar
   */
  $(document).on('click', '.toggle-quickbar-button', function (e) {
    $('.admin-quickbar').toggleClass('toggle');
    localStorage.adminQuickbarToggle = $('.admin-quickbar').hasClass('toggle');
  });

})(jQuery);

/**
 * Set localStorage
 */
function setPostListStorage() {
  var plStorage = {};
  jQuery('.admin-quickbar-postlist').each(function (index, element) {
    plStorage[jQuery(element).data('post-type')] = jQuery(element).hasClass('show-list');
  });

  localStorage.postList = JSON.stringify(plStorage);
}

/**
 * Get localStorage
 */
function getPostListStorage() {
  if (!localStorage.postList || typeof(localStorage.postList) != 'string') {
    localStorage.postList = '{}';
  }
  return JSON.parse(localStorage.postList);
}