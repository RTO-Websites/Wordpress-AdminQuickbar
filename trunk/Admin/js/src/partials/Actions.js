let AdminQuickbarActions = {

  /**
   * Adds a page to swift-cache
   *
   * @param {Event} e
   */
  addPageToSwiftCache: function(e) {
    e.preventDefault();
    let $target = $(e.currentTarget),
      url = $target.data('url');

    $target.addClass('loading');

    jQuery.post(aqbLocalize.ajaxUrl, {
      action: 'swift_performance_single_prebuild',
      '_wpnonce': $target.closest('.admin-quickbar').data('swift-nonce'),
      'url': url,
    }, function(response) {
      response = (typeof response === 'string' ? JSON.parse(response) : response);

      if (response.status === 'success') {
        $target.addClass('is-in-cache');
      }
      $target.removeClass('loading');
    });
  },


  /**
   * Removes a page from swift-cache
   *
   * @param {Event} e
   */
  refreshSwiftCache: function(e) {
    e.preventDefault();
    let $target = $(e.currentTarget),
      url = $target.data('url');

    $target.addClass('loading');

    jQuery.post(aqbLocalize.ajaxUrl, {
      action: 'swift_performance_single_clear_cache',
      '_wpnonce': $target.closest('.admin-quickbar').data('swift-nonce'),
      'url': url,
    }, function(response) {
      $target.removeClass('is-in-cache');
      AdminQuickbarActions.addPageToSwiftCache(e);
    });
  },


  /**
   * Removes all pages from swift cache
   *
   * @param {Event} e
   */
  clearAllSwiftCache: function(e) {
    e.preventDefault();
    let $target = $(e.currentTarget);

    $target.addClass('loading');

    jQuery.post(aqbLocalize.ajaxUrl, {
      action: 'swift_performance_clear_cache',
      type: 'all',
      '_wpnonce': $target.closest('.admin-quickbar').data('swift-nonce')
    }, function(response) {
      $target.removeClass('loading');
    });
  },


  /**
   * Checks if page is cached and clear/add or only add it
   *
   * @param {Event} e
   */
  checkSwiftCache: function(e) {
    e.preventDefault();
    e.stopPropagation();
    let $target = $(e.currentTarget);

    if ($target.hasClass('is-in-cache')) {
      AdminQuickbarActions.refreshSwiftCache(e);
    } else {
      AdminQuickbarActions.addPageToSwiftCache(e);
    }
  },

  /**
   * Adds a post to favorites
   * @param {int} postid
   */
  addToFavorites: function(postid) {
    let $listItem = $('.admin-quickbar-post[data-postid=' + postid + ']'),
      $listItemFav = $('.aqb-favorites .admin-quickbar-post[data-postid=' + postid + ']');

    $listItem.addClass('is-favorite');
    AdminQuickbarActions.buildFavoriteStorage();

    if (!$listItemFav.length) {
      $listItemFav = $listItem.first().clone();
      $listItemFav.css({marginLeft: ''});
      $('.aqb-favorites .admin-quickbar-postlist-inner').append($listItemFav);
    }
  },
  /**
   * Removes a post from favorites
   * @param {int} postid
   */
  removeFromFavorites: function(postid) {
    let $listItem = $('.admin-quickbar-post[data-postid=' + postid + ']'),
      $listItemFav = $('.aqb-favorites .admin-quickbar-post[data-postid=' + postid + ']');

    $listItem.removeClass('is-favorite');
    AdminQuickbarActions.buildFavoriteStorage();
    $listItemFav.remove();
  },

  /**
   * Checks which posts are favorites and add them to local storage
   */
  buildFavoriteStorage: function() {
    let storage = [];
    $('.admin-quickbar-post.is-favorite').each(function(index, element) {
      if (storage.indexOf($(element).data('postid')) === -1) {
        storage.push($(element).data('postid'));
      }
    });
    localStorage.adminQuickbarFavorites = JSON.stringify(storage);
  },

  /**
   *
   * @param {Event} e
   * @param {int} postid
   */
  trashPost: function(e, postid) {
    let $listItem = $('.admin-quickbar-post[data-postid=' + postid + ']'),
      trashUrl = $listItem.data('trash-url'),
      unTrashUrl = $listItem.data('untrash-url');

    if ($listItem.hasClass('post-status-trash')) {
      $.ajax(unTrashUrl);
      $listItem.addClass('post-status-publish').removeClass('post-status-trash');
    } else {
      $.ajax(trashUrl);
      $listItem.addClass('post-status-trash').removeClass('post-status-publish');
    }
  },

  /**
   *
   * @param {Event} e
   * @param {int} postid
   */
  startRenamePost: function(e, postid) {
    let $contextMenu = $('.admin-quickbar-contextmenu'),
      $listItem = $contextMenu.data('listitem'),
      $titleItem = $listItem.find('.aqb-post-title'),
      $saveButton = $('<span class="save-rename" />');

    $('.admin-quickbar .save-rename').remove();
    $('.admin-quickbar .aqb-post-title').removeClass('is-renaming').prop('contenteditable', false);

    $saveButton.on('click', function(e) {
      AdminQuickbarActions.saveRenamePost(postid, $titleItem.text());
    });
    $titleItem.addClass('is-renaming');
    $titleItem.prop('contenteditable', true);
    $titleItem.parent().find('.save-rename').remove();
    $titleItem.after($saveButton);
    $titleItem.focus();
  },

  /**
   *
   * @param {int} postid
   * @param {string} title
   */
  saveRenamePost: function(postid, title) {
    let $postTitle = $('.aqb-post-title');
    $.post({
      url: aqbLocalize.ajaxUrl,
      data: {
        action: 'aqbRenamePost',
        postid: postid,
        title: title,
      },
      success: function() {
        let $listItems = $('.admin-quickbar-post[data-postid=' + postid + ']'),
          $listTitles = $listItems.find('.aqb-post-title');
        $listTitles.html(title);
      }
    });

    $('.save-rename').remove();
    $postTitle.prop('contenteditable', false);
    $postTitle.removeClass('is-renaming');
  },


  /**
   * Opens a connected css-window
   */
  openWindow: function() {
    let url = $(this).next('.dashicons-edit').attr('href') + '&noaqb';
    AdminQuickbarActions.registerCssWindow(url);
  },

  /**
   * Registers a connected css-window
   */
  registerCssWindow: function(url) {
    if (!window.globalCssWindow || window.globalCssWindow.closed) {
      window.globalCssWindow = window.open(url, "rto_wp_adminQuickbar", 'width=700,height=500,left=200,top=100');
    } else {
      if (window.globalCssWindow.location.href !== url) {
        window.globalCssWindow.location.assign(url);
      }
    }
    window.globalCssWindow.focus();
  },
};