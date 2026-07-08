(function ($) {
  if (wp.data && wp.data.dispatch) {
    wp.domReady(function () {
      var panel = wp.data.select('core/edit-post').getEditorPanel('taxonomy-panel-bc_chapter');
      if (panel) {
        wp.data.dispatch('core/edit-post').removeEditorPanel('taxonomy-panel-bc_chapter');
      }
    });
  }

  var $search = $('#bc-chapter-search');
  var $selected = $('#bc-chapter-selected');
  var $input = $('#bc-chapter-input');

  if (!$search.length) return;

  function getIds() {
    var ids = [];
    $selected.find('.bc-chapter-chip').each(function () {
      ids.push($(this).data('id'));
    });
    return ids;
  }

  function updateInput() {
    $input.val(getIds().join(','));
  }

  function addChip(id, label) {
    if (getIds().indexOf(id) !== -1) return;
    var chip = $('<span class="bc-chapter-chip" data-id="' + id + '">' + label + '<button type="button" class="bc-chapter-remove">&times;</button></span>');
    chip.find('.bc-chapter-remove').on('click', function () {
      chip.remove();
      updateInput();
    });
    $selected.append(chip);
    updateInput();
  }

  $selected.on('click', '.bc-chapter-remove', function () {
    $(this).closest('.bc-chapter-chip').remove();
    updateInput();
  });

  $search.autocomplete({
    source: function (req, res) {
      $.getJSON(bcChapter.ajaxUrl, {
        action: 'bc_chapter_search',
        q: req.term
      }, res);
    },
    minLength: 2,
    select: function (e, ui) {
      addChip(ui.item.id, ui.item.label);
      $search.val('');
      return false;
    },
    focus: function () {
      return false;
    }
  }).autocomplete('instance')._renderItem = function (ul, item) {
    return $('<li>').append('<div>' + item.label + '</div>').appendTo(ul);
  };

  $search.on('keydown', function (e) {
    if (e.key === 'Enter' && $search.val() === '') {
      e.preventDefault();
    }
  });
})(jQuery);
