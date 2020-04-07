let AdminQuickbarActions = {

  /**
   * Adds a page to swift-cache
   *
   * @param e
   */
  addPageToSwiftCache: function(e) {
    e.preventDefault();
    let target = $(e.currentTarget),
      url = target.data('url');

    target.addClass('loading');

    jQuery.post(ajaxurl, {
      action: 'swift_performance_single_prebuild',
      '_wpnonce': target.closest('.admin-quickbar').data('swift-nonce'),
      'url': url,
    }, function(response) {
      response = (typeof response === 'string' ? JSON.parse(response) : response);

      if (response.status === 'success') {
        target.addClass('is-in-cache');
      }
      target.removeClass('loading');
    });
  },


  /**
   * Removes a page from swift-cache
   *
   * @param e
   */
  refreshSwiftCache: function(e) {
    e.preventDefault();
    let target = $(e.currentTarget),
      url = target.data('url');

    target.addClass('loading');

    jQuery.post(ajaxurl, {
      action: 'swift_performance_single_clear_cache',
      '_wpnonce': target.closest('.admin-quickbar, .admin-quickbar-jumpicons').data('swift-nonce'),
      'url': url,
    }, function(response) {
      target.removeClass('is-in-cache');
      AdminQuickbarActions.addPageToSwiftCache(e);
    });
  },

  /**
   * Checks if page is cached and clear/add or only add it
   *
   * @param e
   */
  checkSwiftCache: function(e) {
    e.preventDefault();
    e.stopPropagation();
    let target = $(e.currentTarget);

    if (target.hasClass('is-in-cache')) {
      AdminQuickbarActions.refreshSwiftCache(e);
    } else {
      AdminQuickbarActions.addPageToSwiftCache(e);
    }
  },

  /**
   * Adds a post to favorites
   * @param postid
   */
  addToFavorites: function(postid) {
    let listItem = $('.admin-quickbar-post[data-postid=' + postid + ']'),
      listItemFav = $('.aqb-favorites .admin-quickbar-post[data-postid=' + postid + ']');

    listItem.addClass('is-favorite');
    AdminQuickbarActions.buildFavoriteStorage();

    if (!listItemFav.length) {
      listItemFav = listItem.first().clone();
      listItemFav.css({marginLeft: ''});
      $('.aqb-favorites .admin-quickbar-postlist-inner').append(listItemFav);
    }
  },
  /**
   * Removes a post from favorites
   * @param postid
   */
  removeFromFavorites: function(postid) {
    let listItem = $('.admin-quickbar-post[data-postid=' + postid + ']'),
      listItemFav = $('.aqb-favorites .admin-quickbar-post[data-postid=' + postid + ']');

    listItem.removeClass('is-favorite');
    AdminQuickbarActions.buildFavoriteStorage();
    listItemFav.remove();
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

  startRenamePost: function(e, postid) {
    let $listItem = $('.admin-quickbar-post[data-postid=' + postid + ']'),
      $titleItem = $listItem.find('.aqb-post-title'),
      $saveButton = $('<span class="save-rename" />');

    $saveButton.on('click', function(e) {
      AdminQuickbarActions.saveRenamePost(postid, $titleItem.text());
    });
    $titleItem.addClass('is-renaming');
    $titleItem.prop('contenteditable', true);
    $titleItem.parent().find('.save-rename').remove();
    $titleItem.after($saveButton);

  },

  saveRenamePost: function(postid, title) {
    $.post({
      url: ajaxurl,
      data: {
        action: 'aqbRenamePost',
        postid: postid,
        title: title,
      }
    });
    $('.save-rename').remove();
    $('.aqb-post-title').prop('contenteditable', false);
    $('.aqb-post-title').removeClass('is-renaming');
  },


  openWindow: function() {
    let url = $(this).next('.dashicons-edit').attr('href') + '&noaqb';
    AdminQuickbarActions.registerCssWindow(url);
  },

  registerCssWindow: function(url) {

    if (!globalCssWindow || globalCssWindow.closed) {
      globalCssWindow = window.open(url, "rto_wp_adminQuickbar", 'width=700,height=500,left=200,top=100');
    } else {
      if (globalCssWindow.location.href !== url) {
        globalCssWindow.location.assign(url);
      }
    }
    globalCssWindow.focus();
  },
};