let AdminQuickbarActions = {

  /**
   * Adds a page to swift-cache
   *
   * @param e
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
   * @param e
   */
  refreshSwiftCache: function(e) {
    e.preventDefault();
    let $target = $(e.currentTarget),
      url = $target.data('url');

    $target.addClass('loading');

    jQuery.post(aqbLocalize.ajaxUrl, {
      action: 'swift_performance_single_clear_cache',
      '_wpnonce': $target.closest('.admin-quickbar, .admin-quickbar-jumpicons').data('swift-nonce'),
      'url': url,
    }, function(response) {
      $target.removeClass('is-in-cache');
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
    let $target = $(e.currentTarget);

    if ($target.hasClass('is-in-cache')) {
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
   * @param postid
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

  saveRenamePost: function(postid, title) {
    let $postTitle = $('.aqb-post-title');
    $.post({
      url: aqbLocalize.ajaxUrl,
      data: {
        action: 'aqbRenamePost',
        postid: postid,
        title: title,
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
let AdminQuickbar = function() {
  let win = window,
    doc = win.document,
    $doc = $(doc),
    self = this,
    init,
    domReady,
    contextMenu,
    refreshPostListStorage,
    initFavorites,
    initDefaultConfig,
    search,
    keyEvent;

  init = function() {
    let $body = $('body');

    $(function($) {
      domReady();
    });

    initDefaultConfig();

    $doc.on('click', '.toggle-quickbar-button', self.toggleSidebar);
    $doc.on('click', '.admin-quickbar-post-type', self.togglePostTypes);
    $doc.on('click', '.aqb-tab-button', self.changeTab);

    /**
     * Keep open
     */
    $doc.on('change', '.admin-quickbar-keepopen input', function(e) {
      localStorage.adminQuickbarKeepopen = $('.admin-quickbar-keepopen input').is(':checked');
    });

    /**
     * Theme
     */
    $doc.on('change', '.admin-quickbar-theme select', self.changeTheme);

    /**
     * Hide on website
     */
    $doc.on('change', '.admin-quickbar-hide-on-website input', function(e) {
      localStorage.adminQuickbarHideOnWebsite = $('.admin-quickbar-hide-on-website input').is(':checked');

      if (localStorage.adminQuickbarHideOnWebsite === 'true') {
        $body.addClass('aqb-hide-on-website');
      } else {
        $body.removeClass('aqb-hide-on-website');
      }
    });

    /**
     * Overlapping
     */
    $doc.on('change', '.admin-quickbar-overlap input', self.checkOverlap);

    /**
     * Show/Hide trashed posts
     */
    $doc.on('change', '.admin-quickbar-show-trash-option input', self.checkTrash);

    /**
     * Load thumbs
     */
    $doc.on('change', '.admin-quickbar-loadthumbs input', self.checkThumbs);

    $doc.on('click', '.language-switch .language-flag, .language-switch .language-all', self.changeLanguageFilter);

    $doc.on('change', '.aqm-hide-posttypes', function() {
      self.updateHiddenPostTypes();
    });

    $doc.on('keydown', function(e) {
      keyEvent(e);
    });

    if (localStorage.adminQuickbarOverlap === 'true') {
      $body.addClass('admin-quickbar-is-overlap');
    }

    self.checkTheme();

    contextMenu = new AdminQuickbarContextMenu();
    search = new AdminQuickbarSearch();
  };

  /**
   * Inits default config-options
   */
  initDefaultConfig = function() {
    if (typeof (localStorage.adminQuickbarKeepopen) === 'undefined') {
      localStorage.adminQuickbarKeepopen = 'true';
    }
    if (typeof (localStorage.adminQuickbarToggle) === 'undefined') {
      localStorage.adminQuickbarToggle = 'false';
    }
    if (typeof (localStorage.adminQuickbarLanguageFilter) === 'undefined') {
      localStorage.adminQuickbarLanguageFilter = 'all';
    }
  };

  /**
   * Open sidebar and postlists on dom-ready
   */
  domReady = function() {
    let $body = $('body');
    initFavorites();

    // open quickbar
    if (localStorage.adminQuickbarToggle === 'true' && localStorage.adminQuickbarKeepopen === 'true') {
      $('.admin-quickbar').addClass('toggle');
      $body.addClass('admin-quickbar-visible');
    }

    if (localStorage.adminQuickbarKeepopen === 'true') {
      $('.admin-quickbar-keepopen input').prop('checked', true);
    }

    if (localStorage.adminQuickbarHideOnWebsite === 'true') {
      $('.admin-quickbar-hide-on-website input').prop('checked', true);
      $body.addClass('aqb-hide-on-website');
    }

    if (localStorage.adminQuickbarLoadthumbs === 'true') {
      $('.admin-quickbar-loadthumbs input').prop('checked', true);
      self.loadThumbs();
    }

    if (localStorage.adminQuickbarOverlap === 'true') {
      $('.admin-quickbar-overlap input').prop('checked', true);
      $body.addClass('admin-quickbar-is-overlap');
    }

    if (localStorage.adminQuickbarShowTrash === 'true') {
      $('.admin-quickbar-show-trash-option input').prop('checked', true);
      $body.addClass('admin-quickbar-show-trash');
    }

    self.checkTheme();

    // init hidden post types
    self.initHiddenPostTypes();

    self.setLanguageSwitchActiveClass();
    self.hideByLanguage();

    let $previewIframe = $('#elementor-preview-iframe');
    if ($previewIframe.length) {
      $previewIframe.on('load', function() {
        $($previewIframe.get(0).contentDocument).on('keydown', function(e) {
          keyEvent(e);
        });
      });
    }
  };

  self.checkTheme = function() {
    let $body = $('body'),
      $themeSelect = $('.admin-quickbar-theme select');

    switch (localStorage.adminQuickbarTheme) {
      case 'light':
        $themeSelect.val('light');
        $body.removeClass('admin-quickbar-is-darkmode');
        break;
      case 'dark':
        $themeSelect.val('dark');
        $body.addClass('admin-quickbar-is-darkmode');
        break;
      case 'auto':
      default:
        $themeSelect.val('auto');
        let isSystemDarkMode = window.matchMedia("(prefers-color-scheme: dark)").matches,
          isSystemLightMode = window.matchMedia("(prefers-color-scheme: light)").matches,
          isNotSpecified = window.matchMedia("(prefers-color-scheme: no-preference)").matches,
          hasNoSupport = !isSystemDarkMode && !isSystemLightMode && !isNotSpecified;

        if (isSystemDarkMode || hasNoSupport || isNotSpecified) {
          $body.addClass('admin-quickbar-is-darkmode');
        } else {
          $body.removeClass('admin-quickbar-is-darkmode');
        }
        break;
    }

    // compatibility
    if (!localStorage.adminQuickbarTheme && localStorage.adminQuickbarDarkmode === 'true') {
      $body.addClass('admin-quickbar-is-darkmode');
    }
  };

  /**
   * Read from localstorage, set select-field and hide post-types
   */
  self.initHiddenPostTypes = function() {
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
  self.updateHiddenPostTypes = function() {
    localStorage.adminQuickbarHiddenPostTypes = JSON.stringify($('.aqm-hide-posttypes').val());
    self.hidePostTypes();
  };

  /**
   * Hides post-types
   */
  self.hidePostTypes = function() {
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
  self.changeLanguageFilter = function(e) {
    let $target = $(e.currentTarget),
      language = $target.data('language-code');

    localStorage.adminQuickbarLanguageFilter = language;
    self.setLanguageSwitchActiveClass();
    self.hideByLanguage();
  };

  /**
   * Hides all post which dont match selected language
   */
  self.hideByLanguage = function() {
    let language = localStorage.adminQuickbarLanguageFilter;

    $('.admin-quickbar-post').removeClass('hidden-by-language');

    if (language == 'all') {
      return;
    }

    $('.admin-quickbar-post .language-flag').each(function(index, flagElement) {
      let $flagElement = $(flagElement);
      if ($flagElement.data('language-code') !== language) {
        $flagElement.closest('.admin-quickbar-post').addClass('hidden-by-language');
      }
    });
  };

  self.setLanguageSwitchActiveClass = function() {
    let language = localStorage.adminQuickbarLanguageFilter;
    $('.admin-quickbar .language-switch .language-all,' +
      '.admin-quickbar .language-switch .language-flag').removeClass('active');

    $('.admin-quickbar .language-switch [data-language-code="' + language + '"]').addClass('active');
  };

  /**
   *
   * @param e
   */
  self.changeTab = function(e) {
    let $target = $(e.currentTarget),
      tabSlug = $target.data('tab');

    $('.aqb-tab-button, .aqb-tab').removeClass('active');

    $target.addClass('active');
    $('.aqb-tab-' + tabSlug).addClass('active');
  };

  keyEvent = function(e) {
    if ((!e.ctrlKey && !e.metaKey) || !e.shiftKey) {
      return;
    }

    let key = e.key ? e.key.toLowerCase() : '';

    if (!key && e.keyCode) {
      // fallback
      switch (e.keyCode) {
        case 60:
        case 220:
          key = '<';
          break;
        case 70:
          key = 'f';
          break;
      }
    }

    switch (key) {
      case 'f':
        if (!$('body').hasClass('admin-quickbar-visible')) {
          self.toggleSidebar();
        }
        $('#aqb-search').focus();
        break;

      case '>':
      case '<':
      case '|':
        e.preventDefault();
        self.toggleSidebar();
        break;
    }
  };

  /**
   * Read local storage and moves all posts in it to favorites
   */
  initFavorites = function() {
    let storage = [];
    if (typeof (localStorage.adminQuickbarFavorites) !== 'undefined') {
      storage = JSON.parse(localStorage.adminQuickbarFavorites);
    }

    for (let i in storage) {
      let $listItem = $('.admin-quickbar-post[data-postid=' + storage[i] + ']');
      $listItem.addClass('is-favorite');
      let $listItemFav = $listItem.first().clone();
      $listItemFav.css({marginLeft: ''});
      $('.aqb-favorites .admin-quickbar-postlist-inner').append($listItemFav);
    }
  };

  /**
   * Checks if overlapping is active
   * @param e
   */
  self.checkOverlap = function(e) {
    let $body = $('body');
    localStorage.adminQuickbarOverlap = $('.admin-quickbar-overlap input').is(':checked');

    if (localStorage.adminQuickbarOverlap === 'true') {
      $body.addClass('admin-quickbar-is-overlap');
    } else {
      $body.removeClass('admin-quickbar-is-overlap');
    }
  };

  /**
   * Checks if show trashed is active
   * @param e
   */
  self.checkTrash = function(e) {
    let $body = $('body');
    localStorage.adminQuickbarShowTrash = $('.admin-quickbar-show-trash-option input').is(':checked');

    if (localStorage.adminQuickbarShowTrash === 'true') {
      $body.addClass('admin-quickbar-show-trash');
    } else {
      $body.removeClass('admin-quickbar-show-trash');
    }
  };

  /**
   * Checks if overlapping is active
   * @param e
   */
  self.changeTheme = function(e) {
    localStorage.adminQuickbarTheme = $('.admin-quickbar-theme select').val();

    self.checkTheme();
  };

  /**
   * Check if load-thumbs is active
   *
   * @param e
   */
  self.checkThumbs = function(e) {
    localStorage.adminQuickbarLoadthumbs = $('.admin-quickbar-loadthumbs input').is(':checked');

    if (localStorage.adminQuickbarLoadthumbs === 'true') {
      $('.admin-quickbar-loadthumbs input').prop('checked', true);
      self.loadThumbs();
    } else {
      $('.admin-quickbar .wp-post-image').prop('src', '');
    }
  };

  /**
   * Open/Close Sidebar
   */
  self.toggleSidebar = function() {
    let $adminQuickbar = $('.admin-quickbar');
    $adminQuickbar.toggleClass('toggle');
    $('body').toggleClass('admin-quickbar-visible');
    localStorage.adminQuickbarToggle = $adminQuickbar.hasClass('toggle');
  };

  /**
   * Click on headlines
   */
  self.togglePostTypes = function(e) {
    let $target = $(e.target),
      $parent = $target.parent();

    $parent.toggleClass('show-list');

    refreshPostListStorage();
  };


  /**
   * Set localStorage
   */
  refreshPostListStorage = function() {
    let postListStorage = {};
    $('.admin-quickbar-postlist').each(function(index, element) {
      let $element = $(element);
      postListStorage[$element.data('post-type')] = $element.hasClass('show-list');
    });

    localStorage.postList = JSON.stringify(postListStorage);
  };


  /**
   * Get localStorage
   */
  self.getPostListStorage = function() {
    if (!localStorage.postList || typeof (localStorage.postList) != 'string') {
      localStorage.postList = '{}';
    }
    return JSON.parse(localStorage.postList);
  };

  /**
   * Replace img src with data-src and loads images
   */
  self.loadThumbs = function() {
    $('.admin-quickbar .wp-post-image').each(function(index, element) {
      let $element = $(element);
      $element.prop('src', $element.data('src'));
    });
  };

  init();
};


let AdminQuickbarContextMenu = function() {
  let win = window,
    doc = win.document,
    $doc = $(doc),
    self = this,
    closeContextMenu,
    openContextMenu,
    buildContextMenu,
    buildContextMenuCopy,
    buildContextMenuSwift,
    buildContextMenuFavorite,
    buildContextMenuTrash,
    buildContextMenuRename,
    addTitleToElement,
    init;

  init = function() {
    /**
     * Open default contextmenu on icons
     */
    $doc.on('contextmenu', '.aqb-icon', function(e) {
      e.stopPropagation();
    });

    // contextmenu
    $doc.on('contextmenu', '.admin-quickbar-post', openContextMenu);
    $doc.on('click', closeContextMenu);


    $doc.on('click', '.aqb-icon-swift', AdminQuickbarActions.checkSwiftCache);
    $doc.on('click', '.aqb-icon-external', AdminQuickbarActions.openWindow);

  };


  /**
   *
   * @param e
   */
  openContextMenu = function(e) {
    e.preventDefault();

    let $target = $(e.currentTarget),
      $contextMenu = $('.admin-quickbar-contextmenu'),
      offsetTop = $('.admin-quickbar-inner').scrollTop() + $target.offset().top - $('.admin-quickbar').offset().top + 35;

    $contextMenu.data('postid', $target.data('postid'));
    buildContextMenu($target.data('contextmenu'));

    $contextMenu.css({
      top: offsetTop + 'px'
    });

    $contextMenu.addClass('open');
  };

  /**
   * @param data
   */
  buildContextMenu = function(data) {
    let $contextMenu = $('.admin-quickbar-contextmenu');

    $contextMenu.html('');

    for (let index in data) {
      switch (index) {
        case 'favorite':
          $contextMenu.append(buildContextMenuFavorite(data[index]));
          break;

        case 'copy':
          $contextMenu.append(buildContextMenuCopy(data[index]));
          break;

        case 'swift':
          $contextMenu.append(buildContextMenuSwift(data[index]));
          break;

        case 'trash':
          $contextMenu.append(buildContextMenuTrash(data[index]));
          break;

        case 'rename':
          $contextMenu.append(buildContextMenuRename(data[index]));
          break;
      }
    }
  };

  /**
   * Build menu-item to add item to favorites
   *
   * @param data
   */
  buildContextMenuSwift = function(data) {
    let $parent = $('<div class="item has-sub item-swift" />'),
      $contextMenu = $('.admin-quickbar-contextmenu'),
      $item;

    $parent.append('<span class="label">Swift</span>');

    $item = $('<div class="item subitem" />');
    $item.addClass('aqb-icon aqb-icon-swift');
    if (data.inCache) {
      $item.addClass('is-in-cache');
    }

    $item.prop('title', 'Refresh swift cache');
    $item.data('url', data.permalink);
    $parent.append($item);

    return $parent;
  };

  /**
   * Build menu-item to delete item
   *
   * @param data
   */
  buildContextMenuTrash = function(data) {
    let $parent = $('<div class="item has-sub item-trash" />'),
      $contextMenu = $('.admin-quickbar-contextmenu'),
      postid = $contextMenu.data('postid'),
      $item;

    $parent.append('<span class="label">(Un)Trash</span>');

    $item = $('<div class="item subitem" />');
    $item.addClass('aqb-icon aqb-icon-trash');
    $item.prop('title', '(Un)Trash');
    $parent.on('click', function(e) {
      AdminQuickbarActions.trashPost(e, postid);
    });
    $parent.append($item);

    return $parent;
  };

  /**
   * Build menu-item to rename item
   *
   * @param data
   */
  buildContextMenuRename = function(data) {
    let $parent = $('<div class="item has-sub item-rename" />'),
      $contextMenu = $('.admin-quickbar-contextmenu'),
      postid = $contextMenu.data('postid'),
      $item;

    $parent.append('<span class="label">Rename</span>');

    $item = $('<div class="item subitem" />');
    $item.addClass('aqb-icon aqb-icon-rename');
    $item.prop('title', 'Rename');
    $parent.on('click', function(e) {
      AdminQuickbarActions.startRenamePost(e, postid);
    });
    $parent.append($item);

    return $parent;
  };

  /**
   * Build menu-item to add item to favorites
   *
   * @param data
   */
  buildContextMenuFavorite = function(data) {
    let $parent = $('<div class="item has-sub item-favorite" />'),
      $contextMenu = $('.admin-quickbar-contextmenu'),
      postid = $contextMenu.data('postid'),
      $listItem = $('.admin-quickbar-post[data-postid=' + postid + ']'),
      $item;

    $parent.append('<span class="label">Favorite</span>');

    $item = $('<div class="item subitem aqb-icon" />');
    $item.addClass('aqb-icon aqb-icon-favorite');
    if (!$listItem.hasClass('is-favorite')) {
      $item.addClass('aqb-icon-favorite');
      $item.prop('title', 'Add to favorites');
      $parent.on('click', function(e) {
        AdminQuickbarActions.addToFavorites(postid);
      });
    } else {
      $item.addClass('aqb-icon-favorite-alt');
      $item.prop('title', 'Remove from favorites');
      $parent.on('click', function(e) {
        AdminQuickbarActions.removeFromFavorites(postid);
      });
    }
    $parent.append($item);

    return $parent;
  };

  /**
   * Build menu-item with icons to copy id, permalink, shortcode, etc
   *
   * @param data
   * @returns {*|jQuery.fn.init|jQuery|HTMLElement}
   */
  buildContextMenuCopy = function(data) {
    let $parent = $('<div class="item has-sub item-copy" />'),
      $input,
      $item;

    $parent.append('<span class="label">Copy</span>');
    for (let index in data) {
      if (!data[index]) {
        continue;
      }

      $item = $('<div class="item subitem" />');
      $item.on('click', function(e) {
        e.stopPropagation();
        let $input = $(e.currentTarget).find('input');
        $input.focus();
        $input.select();
        document.execCommand('copy');
      });
      $item.addClass('item-' + index);
      $item.addClass('aqb-icon aqb-icon-' + index);
      addTitleToElement($item, index);
      $input = $('<input type="text" class="hidden-copy-input" />');
      $input.val(data[index]);
      $item.append($input);
      $parent.append($item);
    }

    return $parent;
  };

  closeContextMenu = function() {
    let $contextMenu = $('.admin-quickbar-contextmenu');
    $contextMenu.removeClass('open');
  };


  addTitleToElement = function($item, index) {
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
    $item.prop('title', title);
  };

  init();
};

let AdminQuickbarSearch = function() {
  let win = window,
    doc = win.document,
    self = this,
    searchPosts,
    hideEmptyPostTypes,
    restorePostlistState,
    domReady,
    init;

  init = function() {
    $(function($) {
      domReady();
    });

    $(doc).on('keyup input change', '#aqb-search', searchPosts);
  };

  domReady = function() {
    restorePostlistState();
    searchPosts();
  };

  /**
   * Check search input and hide not found posts
   */
  searchPosts = function(e) {
    let $searchInput = $('#aqb-search'),
      searchVal = $searchInput.val().toLowerCase(),
      $posts = $('.admin-quickbar-post');

    $posts.removeClass('aqb-search-hidden');

    $posts.each(function(index, post) {
      let $post = $(post),
        postName = $post.find('.label').text().toLowerCase(),
        postId = $post.data('postid');

      if (postName.indexOf(searchVal) !== -1) {
        return;
      }

      if (postId === parseInt(searchVal)) {
        return;
      }
      $post.addClass('aqb-search-hidden');
    });

    hideEmptyPostTypes();

    if (!searchVal.length) {
      restorePostlistState();
    }
  };


  /**
   * Hides posttypes without visible posts
   */
  hideEmptyPostTypes = function() {
    let $postTypes = $('.admin-quickbar-postlist');
    $postTypes.removeClass('aqb-search-hidden');

    $postTypes.each(function(index, postType) {
      let $postType = $(postType);
      if (!$postType.find('.admin-quickbar-post:not(.aqb-search-hidden)').length) {
        $postType.addClass('aqb-search-hidden');
      } else {
        $postType.addClass('show-list');
      }
    })
  };

  /**
   * Restore opened/closed postlists to state before search was startet.
   */
  restorePostlistState = function() {
    let postLists = win.adminQuickbarInstance.getPostListStorage(),
      $postListElements = $('.admin-quickbar-postlist');

    $postListElements.removeClass('show-list');
    // open postlists
    $postListElements.each(function(index, element) {
      let $element = $(element);
      if (postLists[$element.data('post-type')]) {
        $element.addClass('show-list');
      }
    });
  };

  init();
};

(function () {
  if (typeof ($) === 'undefined') {
    window.$ = jQuery;
  }

  window.adminQuickbarInstance = new AdminQuickbar();
})();