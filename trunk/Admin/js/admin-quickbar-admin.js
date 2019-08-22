let AdminQuickbar = function () {
  let win = window,
    doc = win.document,
    self = this,
    init,
    domReady,
    refreshPostListStorage,
    closeContextMenu,
    openContextMenu,
    buildContextMenu,
    buildContextMenuCopy,
    buildContextMenuSwift,
    buildContextMenuFavorite,
    buildFavoriteStorage,
    initFavorites,
    removeFromFavorites,
    addToFavorites,
    addPageToSwiftCache,
    initDefaultConfig,
    addTitleToElement;

  if (typeof ($) === 'undefined') {
    var $ = jQuery;
  }

  init = function () {
    $(function ($) {
      domReady();
    });

    initDefaultConfig();

    $(doc).on('click', '.toggle-quickbar-button', self.toggleSidebar);
    $(doc).on('click', '.admin-quickbar-post-type', self.togglePostTypes);
    $(doc).on('click', '.aqb-tab-button', self.changeTab);

    /**
     * Keep open
     */
    $(doc).on('change', '.admin-quickbar-keepopen input', function (e) {
      localStorage.adminQuickbarKeepopen = $('.admin-quickbar-keepopen input').is(':checked');
    });

    /**
     * Darkmode
     */
    $(doc).on('change', '.admin-quickbar-darkmode input', self.checkDarkmode);

    /**
     * Hide on website
     */
    $(doc).on('change', '.admin-quickbar-hide-on-website input', function (e) {
      localStorage.adminQuickbarHideOnWebsite = $('.admin-quickbar-hide-on-website input').is(':checked');
    });

    /**
     * Overlapping
     */
    $(doc).on('change', '.admin-quickbar-overlap input', self.checkOverlap);

    /**
     * Load thumbs
     */
    $(doc).on('change', '.admin-quickbar-loadthumbs input', self.checkThumbs);

    $(doc).on('click', '.aqb-icon-swift', self.checkSwiftCache);
    $(doc).on('click', '.aqb-icon-external', self.openWindow);

    /**
     * Open default contextmenu on icons
     */
    $(doc).on('contextmenu', '.aqb-icon', function (e) {
      e.stopPropagation();
    });

    // contextmenu
    $(doc).on('contextmenu', '.admin-quickbar-post', openContextMenu);
    $(doc).on('click', closeContextMenu);


    $(doc).on('change', '.aqm-hide-posttypes', function () {
      self.updateHiddenPostTypes();
    });


    if (localStorage.adminQuickbarOverlap === 'true') {
      $('body').addClass('admin-quickbar-is-overlap');
    }

    if (localStorage.adminQuickbarDarkmode === 'true') {
      $('body').addClass('admin-quickbar-is-darkmode');
    }
  };

  /**
   * Inits default config-options
   */
  initDefaultConfig = function () {
    if (typeof (localStorage.adminQuickbarKeepopen) === 'undefined') {
      localStorage.adminQuickbarKeepopen = 'true';
    }
    if (typeof (localStorage.adminQuickbarToggle) === 'undefined') {
      localStorage.adminQuickbarToggle = 'true';
    }
  };

  /**
   * Open sidebar and postlists on dom-ready
   */
  domReady = function () {
    let postLists = self.getPostListStorage();
    initFavorites();

    // open postlists
    $('.admin-quickbar-postlist').each(function (index, element) {
      if (postLists[$(element).data('post-type')]) {
        $(element).addClass('show-list');
      }
    });

    // open quickbar
    if (localStorage.adminQuickbarToggle === 'true' && localStorage.adminQuickbarKeepopen === 'true') {
      $('.admin-quickbar').addClass('toggle');
      $('body').addClass('admin-quickbar-visible');
    }

    if (localStorage.adminQuickbarKeepopen === 'true') {
      $('.admin-quickbar-keepopen input').prop('checked', true);
    }

    if (localStorage.adminQuickbarHideOnWebsite === 'true') {
      $('.admin-quickbar-hide-on-website input').prop('checked', true);
    }

    if (localStorage.adminQuickbarLoadthumbs === 'true') {
      $('.admin-quickbar-loadthumbs input').prop('checked', true);
      self.loadThumbs();
    }

    if (localStorage.adminQuickbarOverlap === 'true') {
      $('.admin-quickbar-overlap input').prop('checked', true);
      $('body').addClass('admin-quickbar-is-overlap');
    }

    if (localStorage.adminQuickbarDarkmode === 'true') {
      $('.admin-quickbar-darkmode input').prop('checked', true);
      $('body').addClass('admin-quickbar-is-darkmode');
    }

    // init hidden post types
    self.initHiddenPostTypes();
  };

  /**
   * Read from localstorage, set select-field and hide post-types
   */
  self.initHiddenPostTypes = function () {
    if (typeof (localStorage.adminQuickbarHiddenPostTypes) === 'undefined') {
      localStorage.adminQuickbarHiddenPostTypes = '[]';
    }
    let hiddenTypes = JSON.parse(localStorage.adminQuickbarHiddenPostTypes);
    $('.aqm-hide-posttypes').val(hiddenTypes);
    self.hidePostTypes();
  };

  /**
   * Update localstorage for hidden post-types and hide the post-types
   */
  self.updateHiddenPostTypes = function () {
    localStorage.adminQuickbarHiddenPostTypes = JSON.stringify($('.aqm-hide-posttypes').val());
    self.hidePostTypes();
  };

  /**
   * Hides post-types
   */
  self.hidePostTypes = function () {
    let hiddenTypes = JSON.parse(localStorage.adminQuickbarHiddenPostTypes);

    $('.admin-quickbar-postlist').removeClass('hidden');

    for (let index in hiddenTypes) {
      $('.admin-quickbar-postlist[data-post-type="' + hiddenTypes[index] + '"]').addClass('hidden');
    }
  };

  /**
   *
   * @param e
   */
  self.changeTab = function (e) {
    let target = $(e.currentTarget),
      tabSlug = target.data('tab');

    $('.aqb-tab-button, .aqb-tab').removeClass('active');

    target.addClass('active');
    $('.aqb-tab-' + tabSlug).addClass('active');
  };

  /**
   *
   * @param e
   */
  openContextMenu = function (e) {
    e.preventDefault();

    let target = $(e.currentTarget),
      contextMenu = $('.admin-quickbar-contextmenu'),
      offsetTop = $('.admin-quickbar-inner').scrollTop() + target.offset().top - $('.admin-quickbar').offset().top + 35;

    contextMenu.data('postid', target.data('postid'));
    buildContextMenu(target.data('contextmenu'));

    contextMenu.css({
      top: offsetTop + 'px'
    });

    contextMenu.addClass('open');
  };

  /**
   * @param data
   */
  buildContextMenu = function (data) {
    let contextMenu = $('.admin-quickbar-contextmenu');

    contextMenu.html('');

    for (let index in data) {
      switch (index) {
        case 'favorite':
          contextMenu.append(buildContextMenuFavorite(data[index]));
          break;

        case 'copy':
          contextMenu.append(buildContextMenuCopy(data[index]));
          break;

        case 'swift':
          contextMenu.append(buildContextMenuSwift(data[index]));
          break;
      }
    }
  };

  /**
   * Build menu-item to add item to favorites
   *
   * @param data
   */
  buildContextMenuSwift = function (data) {
    let parent = $('<div class="item has-sub item-favorite" />'),
      contextMenu = $('.admin-quickbar-contextmenu'),
      item;

    parent.append('<span class="label">Swift</span>');

    item = $('<div class="item subitem" />');
    item.addClass('aqb-icon aqb-icon-swift');
    if (data.inCache) {
      item.addClass('is-in-cache');
    }

    item.prop('title', 'Refresh swift cache');
    item.data('url', data.permalink);
    parent.append(item);

    return parent;
  };

  /**
   * Build menu-item to add item to favorites
   *
   * @param data
   */
  buildContextMenuFavorite = function (data) {
    let parent = $('<div class="item has-sub item-favorite" />'),
      contextMenu = $('.admin-quickbar-contextmenu'),
      postid = contextMenu.data('postid'),
      listItem = $('.admin-quickbar-post[data-postid=' + postid + ']'),
      item;

    parent.append('<span class="label">Favorite</span>');

    item = $('<div class="item subitem aqb-icon" />');
    item.addClass('aqb-icon aqb-icon-favorite');
    if (!listItem.hasClass('is-favorite')) {
      item.addClass('aqb-icon-favorite');
      item.prop('title', 'Add to favorites');
      item.on('click', function (e) {
        addToFavorites(postid);
      });
    } else {
      item.addClass('aqb-icon-favorite-alt');
      item.prop('title', 'Remove from favorites');
      item.on('click', function (e) {
        removeFromFavorites(postid);
      });
    }
    parent.append(item);

    return parent;
  };

  /**
   * Checks which posts are favorites and add them to local storage
   */
  buildFavoriteStorage = function () {
    let storage = [];
    $('.admin-quickbar-post.is-favorite').each(function (index, element) {
      if (storage.indexOf($(element).data('postid')) === -1) {
        storage.push($(element).data('postid'));
      }
    });
    localStorage.adminQuickbarFavorites = JSON.stringify(storage);
  };

  /**
   * Read local storage and moves all posts in it to favorites
   */
  initFavorites = function () {
    let storage = [];
    if (typeof (localStorage.adminQuickbarFavorites) !== 'undefined') {
      storage = JSON.parse(localStorage.adminQuickbarFavorites);
    }

    for (let i in storage) {
      let listItem = $('.admin-quickbar-post[data-postid=' + storage[i] + ']');
      listItem.addClass('is-favorite');
      let listItemFav = listItem.first().clone();
      listItemFav.css({marginLeft: ''});
      $('.aqb-favorites .admin-quickbar-postlist-inner').append(listItemFav);
    }
  };

  /**
   * Removes a post from favorites
   * @param postid
   */
  removeFromFavorites = function (postid) {
    let listItem = $('.admin-quickbar-post[data-postid=' + postid + ']'),
      listItemFav = $('.aqb-favorites .admin-quickbar-post[data-postid=' + postid + ']');

    listItem.removeClass('is-favorite');
    buildFavoriteStorage();
    listItemFav.remove();
  };

  /**
   * Adds a post to favorites
   * @param postid
   */
  addToFavorites = function (postid) {
    let listItem = $('.admin-quickbar-post[data-postid=' + postid + ']'),
      listItemFav = $('.aqb-favorites .admin-quickbar-post[data-postid=' + postid + ']');

    listItem.addClass('is-favorite');
    buildFavoriteStorage();

    if (!listItemFav.length) {
      listItemFav = listItem.first().clone();
      listItemFav.css({marginLeft: ''});
      $('.aqb-favorites .admin-quickbar-postlist-inner').append(listItemFav);
    }
  };

  /**
   * Build menu-item with icons to copy id, permalink, shortcode, etc
   *
   * @param data
   * @returns {*|jQuery.fn.init|jQuery|HTMLElement}
   */
  buildContextMenuCopy = function (data) {
    let parent = $('<div class="item has-sub item-copy" />'),
      input,
      item;

    parent.append('<span class="label">Copy</span>');
    for (let index in data) {
      if (!data[index]) {
        continue;
      }

      item = $('<div class="item subitem" />');
      item.on('click', function (e) {
        e.stopPropagation();
        let input = $(e.currentTarget).find('input');
        input.focus();
        input.select();
        document.execCommand('copy');
      });
      item.addClass('item-' + index);
      item.addClass('aqb-icon aqb-icon-' + index);
      addTitleToElement(item, index);
      input = $('<input type="text" class="hidden-copy-input" />');
      input.val(data[index]);
      item.append(input);
      parent.append(item);
    }

    return parent;
  };

  closeContextMenu = function () {
    let contextMenu = $('.admin-quickbar-contextmenu');
    contextMenu.removeClass('open');
  };

  addTitleToElement = function (item, index) {
    let title;

    switch (index) {
      case 'id':
        title = 'ID';
        break;
      case 'wordpress':
        title = 'WP-Edit-URL';
        break;
      case 'elementor':
        title = 'Elementor-URL';
        break;
      case 'shortcode':
        title = 'Elementor Shortcode';
        break;
      case 'website':
        title = 'Website-URL';
        break;
      default:
        title = index.charAt(0).toUpperCase() + index.slice(1);
        break;
    }
    item.prop('title', title);
  };

  /**
   * Checks if overlapping is active
   * @param e
   */
  self.checkOverlap = function (e) {
    localStorage.adminQuickbarOverlap = $('.admin-quickbar-overlap input').is(':checked');

    if (localStorage.adminQuickbarOverlap === 'true') {
      $('body').addClass('admin-quickbar-is-overlap');
    } else {
      $('body').removeClass('admin-quickbar-is-overlap');
    }
  };

  /**
   * Checks if overlapping is active
   * @param e
   */
  self.checkDarkmode = function (e) {
    localStorage.adminQuickbarDarkmode = $('.admin-quickbar-darkmode input').is(':checked');

    if (localStorage.adminQuickbarDarkmode === 'true') {
      $('body').addClass('admin-quickbar-is-darkmode');
    } else {
      $('body').removeClass('admin-quickbar-is-darkmode');
    }
  };

  /**
   * Check if load-thumbs is active
   *
   * @param e
   */
  self.checkThumbs = function (e) {
    localStorage.adminQuickbarLoadthumbs = $('.admin-quickbar-loadthumbs input').is(':checked');

    if (localStorage.adminQuickbarLoadthumbs === 'true') {
      $('.admin-quickbar-loadthumbs input').prop('checked', true);
      self.loadThumbs();
    } else {
      $('.admin-quickbar .wp-post-image').prop('src', '');
    }
  };

  /**
   * Checks if page is cached and clear/add or only add it
   *
   * @param e
   */
  self.checkSwiftCache = function (e) {
    e.preventDefault();
    e.stopPropagation();
    let target = $(e.currentTarget);

    if (target.hasClass('is-in-cache')) {
      self.refreshSwiftCache(e);
    } else {
      addPageToSwiftCache(e);
    }
  };

  /**
   * Open/Close Sidebar
   */
  self.toggleSidebar = function (e) {
    $('.admin-quickbar').toggleClass('toggle');
    $('body').toggleClass('admin-quickbar-visible');
    localStorage.adminQuickbarToggle = $('.admin-quickbar').hasClass('toggle');
  };

  /**
   * Click on headlines
   */
  self.togglePostTypes = function (e) {
    let target = $(e.target),
      parent = target.parent();

    parent.toggleClass('show-list');

    refreshPostListStorage();
  };

  /**
   * Removes a page from swift-cache
   *
   * @param e
   */
  self.refreshSwiftCache = function (e) {
    e.preventDefault();
    let target = $(e.currentTarget),
      url = target.data('url');

    target.addClass('loading');

    jQuery.post(ajaxurl, {
      action: 'swift_performance_single_clear_cache',
      '_wpnonce': target.closest('.admin-quickbar, .admin-quickbar-jumpicons').data('swift-nonce'),
      'url': url,
    }, function (response) {
      target.removeClass('is-in-cache');
      addPageToSwiftCache(e);
    });
  };

  /**
   * Adds a page to swift-cache
   *
   * @param e
   */
  addPageToSwiftCache = function (e) {
    e.preventDefault();
    let target = $(e.currentTarget),
      url = target.data('url');

    target.addClass('loading');

    jQuery.post(ajaxurl, {
      action: 'swift_performance_single_prebuild',
      '_wpnonce': target.closest('.admin-quickbar').data('swift-nonce'),
      'url': url,
    }, function (response) {
      response = (typeof response === 'string' ? JSON.parse(response) : response);

      if (response.status === 'success') {
        target.addClass('is-in-cache');
      }
      target.removeClass('loading');
    });
  };

  /**
   * Set localStorage
   */
  refreshPostListStorage = function () {
    let postListStorage = {};
    $('.admin-quickbar-postlist').each(function (index, element) {
      postListStorage[$(element).data('post-type')] = $(element).hasClass('show-list');
    });

    localStorage.postList = JSON.stringify(postListStorage);
  };

  /**
   * Get localStorage
   */
  self.getPostListStorage = function () {
    if (!localStorage.postList || typeof (localStorage.postList) != 'string') {
      localStorage.postList = '{}';
    }
    return JSON.parse(localStorage.postList);
  };

  /**
   * Replace img src with data-src and loads images
   */
  self.loadThumbs = function () {
    $('.admin-quickbar .wp-post-image').each(function (index, element) {
      $(element).prop('src', $(element).data('src'));
    });
  };


  self.openWindow = function () {
    let url = $(this).next('.dashicons-edit').attr('href') + '&noaqb';
    window.open(url, '_blank', 'width=700,height=500,left=200,top=100');
  };

  init();
};


window.adminQuickbarInstance = new AdminQuickbar();