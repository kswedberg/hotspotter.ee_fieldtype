
(function($, doc, $doc) {

var rowNum = 0;
$doc.ready(function() {
  var uiStyles = $('<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.3/themes/base/jquery-ui.css" rel="stylesheet" type="text/css">')[0];
  doc.getElementsByTagName('head')[0].appendChild(uiStyles);

  $('div.hotspot-wrapper').each(function() {

    $(this).find('tr.hotspot').drawSpot(this);
    rowNum = $(this).find('tr.hotspot').length;
  });


  $('button.hs_add').live('click', function(event) {
    event.preventDefault();
    $(this).addRow();
  });

  $('a.hs_del').live('click', function(event) {
    event.preventDefault();
    $(this).removeRow();
  });

  $('img.hotspot-img').shrinkWrap();
});


var styleProps = ['top', 'left', 'height', 'width'];

$.extend($.fn, {
  drawSpot: function(wrap) {
    if (!wrap) {
      wrap = $(this).closest('.hotspot-wrapper')[0];
    }
    var wrapId = wrap.id,
        $imgWrap = $('#' + wrapId).find('.hotspot-img-wrap');

    this.each(function(index) {

      var $row = $(this),
          idx = parseInt($row.find('.hs_id').val(), 10),
          hsId = wrapId + '_hs_' + idx,
          css = {},
          link = $row.find('.hs_link').val(),
          $hotspot;

      $.each(styleProps, function(i, val) {
        var prop =  $row.find('.hs_' + val).val();
        if (/\d$/.test(prop)) {
          prop += 'px';
        }
        css[val] = prop;
      });


      if ( $('#' + hsId).length ) {
        $hotspot = $('#' + hsId);
      } else {
        $hotspot = $('<div></div>', {
          id: hsId,
          className: 'hs',
          html: '<div class="hs-inner"><div class="hs-info">' + idx + '</div></div>'
        })
        .appendTo($imgWrap);

        $hotspot.data('rowid', 'hotspot-' + idx);

        var justdrag = $.extend({}, dragsize, {cursor: 'move'});
        $hotspot.draggable(justdrag);
        if (HOTSPOTTER.resizable == 'yes') {
          $hotspot.resizable(dragsize);
        }

      }

      $hotspot.css(css).fadeTo(800, 0.5);

    });
    return this;

  },
  addRow: function() {
    var $wrapper = this.closest('.hotspot-wrapper');
    var rowData = {
      fieldId: $wrapper.attr('id').split('-').pop(),
      height: '30',
      width: '30',
      index: function() {
        return rowNum;
      }
    };

    rowNum++;
    var newRow = $.mustache(rowTemplate, rowData);
    $wrapper
      .find('tr').last().after(newRow)
        .next().drawSpot($wrapper[0]);




    return this;
  },
  removeRow: function() {

    return this.each(function() {
      var $row = $(this).closest('tr.hotspot'),
          rowid = $row.attr('id');

      $row.closest('.hotspot-wrapper').find('.hs').filter(function() {
        return $(this).data('rowid') == rowid;
      }).fadeOut(200, function() {
        $(this).remove();
      });

      $row.remove();
    });
  },
  shrinkWrap: function() {

    return this.each(function() {
      if (this.complete) {
        dims(this);
      } else {
        $(this).bind('load', function() {
          dims(this);
        });
      }
    });
  }
});

var dims = function(el) {
  var dims = {
    width: $(el).css('width'),
    height: $(el).css('height')
  };
  $(el).parent().css(dims);
};

var dragsize = {
  containment: 'parent',
  stop: function(event, ui) {
    var rowid = ui.helper.data('rowid'),
        $row = $('#' + rowid),
        css = $.extend({}, ui.size, ui.position);

    $.each(css, function(key, val) {
      $row.find('.hs_' + key).val(val);
    });
  }
};

var rowTemplate = HOTSPOTTER.tmpl;
// var rowTemplate = [
//   '<tr class="hotspot" id="hotspot-{{index}}">',
//     '<td class="tableCellOne">',
//       '<div class="defaultSmall">{{index}}</div>',
//       '<input type="hidden" value="{{index}}" class="hs_id">',
//       '<input type="hidden" value="0" name="{{fieldId}}[hotspots][{{index}}][top]" class="hs_top">',
//       '<input type="hidden" value="0" name="{{fieldId}}[hotspots][{{index}}][left]" class="hs_left">',
//       '<input type="hidden" value="{{height}}" name="{{fieldId}}[hotspots][{{index}}][height]" class="hs_height">',
//       '<input type="hidden" value="{{width}}" name="{{fieldId}}[hotspots][{{index}}][width]" class="hs_width">',
//     '</td>',
//     '<td style="width: 50%;" class="tableCellOne">',
//       '<input style="width: 100%" type="text" class="hs_link" value="" id="{{fieldId}}hotspots{{index}}link" name="{{fieldId}}[hotspots][{{index}}][link]">',
//     '</td>',
//     '<td class="tableCellOne">',
//       '<a href="#" class="hs_del">Remove</a>',
//     '</td>',
//   '</tr>'
// ].join('');


})( jQuery, document, jQuery(document) );
