
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
   * @param {Event} e
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

    if (searchVal.length) {
      hideEmptyPostTypes();
    }

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