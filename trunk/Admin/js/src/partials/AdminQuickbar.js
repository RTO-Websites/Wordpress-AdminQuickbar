let AdminQuickbar = function() {
  let win = window,
    doc = win.document,
    $doc = $(doc),
    self = this,
    $body = $('body'),
    init,
    domReady,
    contextMenu,
    refreshPostListStorage,
    initFavorites,
    initRecent,
    initDefaultConfig,
    search,
    keyEvent;

  init = function() {

    $(function($) {
      domReady();
    });

    initDefaultConfig();

    $doc.on('click', '.toggle-quickbar-button', self.toggleSidebar);
    $doc.on('click', '.admin-quickbar-post-type', self.togglePostTypes);
    $doc.on('click', '.aqb-tab-button', self.changeTab);

    $doc.on('mouseenter', '.aqb-toolbar-item', self.showIndicator);
    $doc.on('mouseleave', '.aqb-toolbar-item', self.hideIndicator);

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
      if ($('.admin-quickbar-hide-on-website input').is(':checked')) {
        $body.addClass('aqb-hide-on-website');
      } else {
        $body.removeClass('aqb-hide-on-website');
      }
      self.saveSettings();
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
     * Show/Hide post-ids
     */
    $doc.on('change', '.admin-quickbar-show-postids input', self.checkShowPostIds);

    /**
     * Load thumbs
     */
    $doc.on('change', '.admin-quickbar-loadthumbs input', self.checkThumbs);

    $doc.on('click', '.language-switch .language-flag, .language-switch .language-all', self.changeLanguageFilter);

    $doc.on('change', '.aqb-input-hide-posttypes', function() {
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
    initFavorites();
    initRecent();

    // open quickbar
    if (localStorage.adminQuickbarToggle === 'true' && localStorage.adminQuickbarKeepopen === 'true') {
      $('.admin-quickbar').addClass('toggle');
      $body.addClass('admin-quickbar-visible');
      self.checkElementorNavigator();
    }

    if (localStorage.adminQuickbarKeepopen === 'true') {
      $('.admin-quickbar-keepopen input').prop('checked', true);
    }

    if ($('.admin-quickbar-loadthumbs input').is(':checked')) {
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
    if (localStorage.adminQuickbarShowPostIds === 'true') {
      $('.admin-quickbar-show-postids input').prop('checked', true);
      $body.addClass('aqb-show-postids');
    }

    self.checkTheme();

    self.setLanguageSwitchActiveClass();
    self.hideByLanguage();

    self.checkElementorNavigator();
    let $previewIframe = $('#elementor-preview-iframe');
    if ($previewIframe.length) {
      $previewIframe.on('load', function() {
        $($previewIframe.get(0).contentDocument).on('keydown', function(e) {
          keyEvent(e);
        });

        setTimeout(function() {
          self.checkElementorNavigator();
        }, 2000);
      });
    }
  };

  self.saveSettings = function() {
    let aqbSettings = {
      hiddenPostTypes: $('.aqb-input-hide-posttypes').val() ?? [],
      loadThumbs: $('.admin-quickbar-loadthumbs input').is(':checked'),
      hideOnWebsite: $('.admin-quickbar-hide-on-website input').is(':checked')
    };

    $.post(aqbLocalize.ajaxUrl, {
      action: 'aqb_save_settings',
      aqbSettings: aqbSettings,
    }, function(result) {
      console.log('aqb_save_settings', result);
    }, 'json');

  }

  /**
   * Checks and set theme dark/light
   */
  self.checkTheme = function() {
    let $themeSelect = $('.admin-quickbar-theme select');

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
   * Update settings for hidden post-types and hide the post-types
   */
  self.updateHiddenPostTypes = function() {
    self.saveSettings();
    self.hidePostTypes();
  };

  /**
   * Hides post-types
   */
  self.hidePostTypes = function() {
    let hiddenTypes = $('.aqb-input-hide-posttypes').val();

    $('.admin-quickbar-postlist').removeClass('hidden-posttype');

    for (let index in hiddenTypes) {
      $('.admin-quickbar-postlist[data-post-type="' + hiddenTypes[index] + '"]').addClass('hidden-posttype');
    }
  };

  /**
   *
   * @param {Event} e
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
   * @param {Event} e
   */
  self.changeTab = function(e) {
    let $target = $(e.currentTarget),
      tabSlug = $target.data('tab');

    $('.aqb-tab-button, .aqb-tab').removeClass('active');

    $target.addClass('active');
    $('.aqb-tab-' + tabSlug).addClass('active');
  };

  /**
   * Event handler for keypress
   *
   * @param {Event} e
   */
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
        if (!$body.hasClass('admin-quickbar-visible')) {
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
  initRecent = function() {
    if (!localStorage.adminQuickbarMaxRecent) {
      localStorage.adminQuickbarMaxRecent = 4;
    }
    $('.admin-quickbar-max-recent input').val(localStorage.adminQuickbarMaxRecent);
    $doc.on('change', '.admin-quickbar-max-recent input', function(e) {
      localStorage.adminQuickbarMaxRecent = $('.admin-quickbar-max-recent input').val();
    });

    let storage = [],
      newStorage = [],
      max = parseInt(localStorage.adminQuickbarMaxRecent),
      count = 0,
      currentPost = $('.admin-quickbar').data('current-post');

    if (typeof (localStorage.adminQuickbarRecent) !== 'undefined') {
      storage = JSON.parse(localStorage.adminQuickbarRecent);
    }

    if (currentPost) {
      storage.unshift(currentPost);
    }

    for (let i in storage) {
      if (newStorage.indexOf(storage[i]) !== -1) {
        continue;
      }
      newStorage.push(storage[i]);
      count += 1;

      let $listItem = $('.admin-quickbar-post[data-postid=' + storage[i] + ']'),
        $listItemFav = $listItem.first().clone();
      $listItemFav.css({marginLeft: ''});
      $('.aqb-recent .admin-quickbar-postlist-inner').append($listItemFav);

      if (count >= max) {
        break;
      }
    }

    localStorage.adminQuickbarRecent = JSON.stringify(newStorage);
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
   * @param {Event} e
   */
  self.checkOverlap = function(e) {
    localStorage.adminQuickbarOverlap = $('.admin-quickbar-overlap input').is(':checked');

    if (localStorage.adminQuickbarOverlap === 'true') {
      $body.addClass('admin-quickbar-is-overlap');
    } else {
      $body.removeClass('admin-quickbar-is-overlap');
    }
  };

  /**
   * Checks if show trashed is active
   * @param {Event} e
   */
  self.checkTrash = function(e) {
    localStorage.adminQuickbarShowTrash = $('.admin-quickbar-show-trash-option input').is(':checked');

    if (localStorage.adminQuickbarShowTrash === 'true') {
      $body.addClass('admin-quickbar-show-trash');
    } else {
      $body.removeClass('admin-quickbar-show-trash');
    }
  };

  /**
   * Checks if show trashed is active
   * @param {Event} e
   */
  self.checkShowPostIds = function(e) {
    localStorage.adminQuickbarShowPostIds = $('.admin-quickbar-show-postids input').is(':checked');

    if (localStorage.adminQuickbarShowPostIds === 'true') {
      $body.addClass('aqb-show-postids');
    } else {
      $body.removeClass('aqb-show-postids');
    }
  };

  /**
   * Checks if overlapping is active
   * @param {Event} e
   */
  self.changeTheme = function(e) {
    localStorage.adminQuickbarTheme = $('.admin-quickbar-theme select').val();

    self.checkTheme();
  };

  /**
   * Check if load-thumbs is active
   *
   * @param {Event} e
   */
  self.checkThumbs = function(e) {
    self.saveSettings();

    if ($('.admin-quickbar-loadthumbs input').is(':checked')) {
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
    $body.toggleClass('admin-quickbar-visible');
    localStorage.adminQuickbarToggle = $adminQuickbar.hasClass('toggle');

    self.checkElementorNavigator();
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

  /**
   * Change and show the toolbar indicator
   *
   * @param {Event} e
   */
  self.showIndicator = function(e) {
    let $target = $(e.currentTarget),
      $toolbar = $('.admin-quickbar-toolbar'),
      $indicator = $('.aqb-toolbar-indicator');

    $indicator.html($target.data('title'));
    $toolbar.addClass('show-indicator');
  };

  /**
   * Hides the toolbar indicator
   *
   * @param {Event} e
   */
  self.hideIndicator = function(e) {
    let $target = $(e.currentTarget),
      $toolbar = $('.admin-quickbar-toolbar'),
      $indicator = $('.aqb-toolbar-indicator');

    $toolbar.removeClass('show-indicator');
  };


  /**
   * Moves elementor navigator in viewport if hidden by sidebar
   */
  self.checkElementorNavigator = function() {
    let $navigator = $('#elementor-navigator');

    if (!$navigator.length ||
      !$body.hasClass('admin-quickbar-visible')
      || $body.hasClass('elementor-navigator-docked')
    ) {
      return;
    }

    let offsetRight = window.innerWidth - ($navigator.offset().left + $navigator.width());

    if (offsetRight >= 320) {
      return;
    }

    $navigator.css({'left': window.innerWidth - $navigator.width() - 320});
  };

  init();
};

