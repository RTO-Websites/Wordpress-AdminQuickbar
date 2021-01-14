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


    $doc.on('click', '.aqb-icon-swift:not(.clear-all)', AdminQuickbarActions.checkSwiftCache);
    $doc.on('click', '.aqb-icon-swift.clear-all', AdminQuickbarActions.clearAllSwiftCache);
    $doc.on('click', '.aqb-icon-external', AdminQuickbarActions.openWindow);

  };


  /**
   *
   * @param {Event} e
   */
  openContextMenu = function(e) {
    e.preventDefault();

    let $target = $(e.currentTarget),
      $contextMenu = $('.admin-quickbar-contextmenu'),
      offsetTop = $('.admin-quickbar-inner').scrollTop() + $target.offset().top - $('.admin-quickbar').offset().top + 35;

    $contextMenu.data('postid', $target.data('postid'));
    $contextMenu.data('listitem', $target);
    buildContextMenu($target.data('contextmenu'));

    $contextMenu.css({
      top: offsetTop + 'px'
    });

    $contextMenu.addClass('open');
  };

  /**
   * @param {object} data
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
   * @param {object} data
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
   * @param {object} data
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
   * @param {object} data
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
   * @param {object} data
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
   * @param {object} data
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

  /**
   *
   * @param $item
   * @param {string} index
   */
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