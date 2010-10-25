
(function($, doc, $doc, hs) {

// LIMITING TO ONE HOTSPOTTER PER ENTRY FOR NOW
hs = hs[0];
var styleProps = ['top', 'left', 'height', 'width'],
    cols = {index: 0};

$doc.bind('resetMatrix', function(event, tid) {

  var $matrix = $(event.target);
  $matrix.updateMatrix();

});


$doc.ready(function() {
  var uiStyles = $('<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.3/themes/base/jquery-ui.css" rel="stylesheet" type="text/css">')[0];
  doc.getElementsByTagName('head')[0].appendChild(uiStyles);
  var matrixId = 'field_id_' + hs.matrix_field,
      $matrixTable = $('#' + matrixId).find('table').eq(0);

  // set up matrix cols
  $matrixTable.find('thead th').each(function(index) {
    if (index !== 0) {
      cols[$.trim($(this).text())] = index;
    }
  });

  // set up matrix
  $matrixTable.updateMatrix();

  // make sure image's wrapper div is same dimensions as image
  $('img.hotspot-img').shrinkWrap();


}); // ready



$.extend($.fn, {
  updateDimensions: function(source) {
    this.each(function() {
      var $rowCells = $(this).children();

      $.each(styleProps, function(index, val) {
        // cols[val] is the index of the cell's column for each style property
        var propIndex = cols[val];

        var $valInput = $rowCells.eq(propIndex).find('textarea');
        $valInput.val(function(i, v) {
          // current value is v. if no source, use current value if it's there or default value if it's not;
          if (!source) {
            v = parseFloat(v) || hs[val];
          } else {
            v = source[val] || hs[val];
          }

          return parseFloat(v) + 'px';
        });
      });
    });

    return this;
  },
  getDimensions: function(props) {
    // props is an array
    props = props || styleProps;
    // only works on one row at a time!!!
    var dimensions = {};
    this.each(function() {
      var $rowCells = $(this).children();
      $.each(props, function(index, val) {
        // cols[val] is the index of the cell's column for each style property
        var propIndex = cols[val];
        dimensions[val] = $rowCells.eq(propIndex).find('textarea').val();
      });
    });
    return dimensions;
  },
  updateMatrix: function(source) {
    // source is typically going to be entered if this is coming from a draggable/resizable
    // each matrix
    this.each(function() {
      // each tbody tr in that matrix
      $(this).find('tbody tr').each(function(index) {
        this.id = $(this).find('th input:hidden').val() + '-row';
        $(this)
        .updateDimensions(source)
        .drawSpot();

      });

    });

    return this;
  },
  drawSpot: function() {
    var wrapId = 'hotspot-' + hs.id,
        $imgWrap = $('#' + wrapId).find('.hotspot-img-wrap');
    this.each(function(index) {

      var $row = $(this),
          rowId = this.id,
          rootId = rowId ? rowId.split('-')[0] : 'row-' + index,
          hsId = rootId + '-hs',
          idx = $row.find('th span:first').text(),
          css = $row.getDimensions(),
          $hotspot;

      if ( $('#' + hsId).length ) {
        $hotspot = $('#' + hsId);
        $hotspot.find('.hs-info').text(idx);
      } else {
        $hotspot = $('<div></div>', {
          id: hsId,
          className: 'hs',
          html: '<div class="hs-inner"><div class="hs-info">' + idx + '</div></div>'
        })
        .appendTo($imgWrap);

        $hotspot.data('rowid', rowId);

        var justdrag = $.extend({}, dragsize, {cursor: 'move'});
        $hotspot.draggable(justdrag);
        if (hs.resizable == 'yes') {
          $hotspot.resizable(dragsize);
        }

      }

      $hotspot.css(css).fadeTo(800, 0.5);

    });
    return this;

  },
  shrinkWrap: function() {

    return this.each(function() {
      if (this.complete) {
        setParentDims(this);
      } else {
        $(this).bind('load', function() {
          setParentDims(this);
        });
      }
    });
  }
});

var setParentDims = function(el) {
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

    $row.updateDimensions(css);
  }
};


})( jQuery, document, jQuery(document), HOTSPOTTER );
