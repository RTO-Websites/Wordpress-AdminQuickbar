let AdminQuickbar = function () {
  let win = window,
    doc = win.document,
    self = this,
    init,
    domReady,
    refreshPostListStorage,
    addPageToSwiftCache;

  if (typeof ($) === 'undefined') {
    var $ = jQuery;
  }

  init = function () {
    $(function ($) {
      domReady();
    });

    $(document).on('click', '.toggle-quickbar-button', self.toggleSidebar);
    $(document).on('click', '.admin-quickbar-post-type', self.togglePostTypes);

    /**
     * Keep open
     */
    $(document).on('change', '.admin-quickbar-keepopen input', function (e) {
      localStorage.adminQuickbarKeepopen = $('.admin-quickbar-keepopen input').is(':checked');
    });

    /**
     * Overlapping
     */
    $(document).on('change', '.admin-quickbar-overlap input', self.checkOverlap);

    /**
     * Load thumbs
     */
    $(document).on('change', '.admin-quickbar-loadthumbs input', self.checkThumbs);

    $(document).on('click', '.admin-quickbar-control-cache', self.checkSwiftCache);
  };

  /**
   * Open sidebar and postlists on dom-ready
   */
  domReady = function () {
    let postLists = self.getPostListStorage();

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

    if (localStorage.adminQuickbarLoadthumbs === 'true') {
      $('.admin-quickbar-loadthumbs input').prop('checked', true);
      loadThumbs();
    }

    if (localStorage.adminQuickbarOverlap === 'true') {
      $('.admin-quickbar-overlap input').prop('checked', true);
      $('body').addClass('admin-quickbar-is-overlap');
    }
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

      if (response.status !== false) {
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

  init();
};


window.adminQuickbarInstance = new AdminQuickbar();