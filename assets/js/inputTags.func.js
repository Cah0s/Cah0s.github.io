jQuery(document).ready(function($) {
  var tags = $('#tags').inputTags({
    // tags: ['jQuery', 'JavaScript'],
    autocomplete: {
      values: ['ISP', 'Immobiliária', 'Mercado', 'Informática', 'Tecnologia', 'Hotel']
    },
    // init: function(elem) {
    //   $('span', '#events').text('init');
    //   $('<p class="results">').html('<strong>Tags:</strong> ' + elem.tags.join(' - ')).insertAfter(elem.$list);
    // },
    create: function() {
      $('span', '#events').text('create');
    },
    update: function() {
      $('span', '#events').text('update');
    },
    destroy: function() {
      $('span', '#events').text('destroy');
    },
    selected: function() {
      $('span', '#events').text('selected');
    },
    unselected: function() {
      $('span', '#events').text('unselected');
    },
    change: function(elem) {
      $('.results').empty().html('<strong>Tags:</strong> ' + elem.tags.join(' - '));
    }
  });

  // $('#tags').inputTags('tags', 'flat', function(tags) {
  //   $('.results').empty().html('<strong>Tags:</strong> ' + tags.join(' - '));
  // });

  // var autocomplete = $('#tags').inputTags('options', 'autocomplete');
  // $('span', '#autocomplete').text(autocomplete.values.join(', '));
});