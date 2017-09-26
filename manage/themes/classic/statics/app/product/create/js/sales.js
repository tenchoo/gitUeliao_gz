define(function(require, exports, module) {
  var input = require('modules/form/js/input.js');
  input.priceOnly();
  input.intOnly();
  var template = require('libs/arttemplate/3.0.0/template.js');
  var $uploader;
  var uploader = require('modules/uploader/js/uploader.js').uploader({
    success: function(file, res) {
      $uploader = $('#rt_' + file.source.ruid).parents('li');
      $uploader.find('button').html('<img src="' + seajs.data.uploaderPath + '/../..' + res.data + '" alt="" width="32" height="32"/>');
      $colorsItem.filter('[data-relation="' + $uploader.data('relation') + '"]').find('.pic').val(res.data);
    }
  });

  require('libs/my97datepicker/4.8.0/WdatePicker.js');
  require('statics/app/product/create/js/batch.js');

  var $form = $('.content-wrap form');
  var $colors = $form.find('.colors');
  var $checkedAll = $colors.find('.all :checkbox');
  var $colorsItem = $colors.find('.color-items li');
  var $colorsChecked = $form.find('.colors-checked');
  var $publishTime = $form.find('[name="product[publishTime]"]');
  var $specData = $form.find('.spec-data');
  var specData = [];

  template.config('escape', false);


  function toggleGroup() {
    var $group = $colorsChecked.parents('.form-group');
    $colorsChecked.find('li:not(.hide)').length ? $group.removeClass('hide') : $group.addClass('hide');
  }


  $colorsItem.each(function() {
    var $t = $(this);
    var relation = $t.data('relation');
    specData.push({
      relation: relation,
      code: $t.data('code'),
      img: $t.data('picture') ? '<img src="' + seajs.data.uploaderPath + '/../..' + $t.data('picture') + '" alt="" width="32" height="32"/>' : '',
      color: $t.data('title'),
      num: $t.data('serialnumber'),
      //  total: specStock[relation]
    });
  });
  $form.find('.colors-checked').html(template('spec', {
    list: specData
  }));

  function toggleChecked() {
    var $checkeds = $colorsItem.filter(':visible').find(':checkbox');
    $checkedAll.prop('checked', $checkeds.length > 0 && $checkeds.length === $checkeds.filter(':checked').length);
  }

  toggleChecked();

  function createUpload($li) {
    if ($li.data('upload')) return;
    uploader.addButton({
      id: $li.find('.uploader'),
      multiple: false
    });
    $li.data('upload', true);
  }

  function batchRender() {
    var $t;
    var relation;
    var $li;
    var checked;
    $colorsItem.each(function() {
      $t = $(this);
      relation = $t.data('relation');
      checked = $t.find(':checkbox').prop('checked');
      $li = $colorsChecked.find('[data-relation="' + relation + '"]');
      if (checked) {
        $li.removeClass('hide');
        createUpload($li);
      } else {
        $li.addClass('hide');
      }
    });
    toggleGroup();
  }

  function createSpecData() {
    var data = [];
    $form.find('.webuploader-element-invisible').prop('disabled', true);
    $colorsChecked.find('li:visible').each(function() {
      var $li = $(this);
      data.push({
        relation: $li.data('relation'),
        total: $li.find('input:text').val()
      });
    });

    $specData.html(template('specData', {
      list: data
    }));
  }



  $colors.on('click', '.color-group li:not(.active)[data-group]', function(event) {
    var $t = $(this).addClass('active');
    var group = $t.data('group');
    $t.siblings('.active').removeClass('active');
    if (group === 'all') return $colorsItem.removeClass('hide');
    $colorsItem.addClass('hide').filter('[data-group="' + group + '"]').removeClass('hide');
    toggleChecked();
  }).on('change', '.all :checkbox', function(event) {
    var checked = this.checked;
    $colorsItem.filter(':not(.hide)').each(function() {
      var $t = $(this);
      $t.find(':checkbox').prop('checked', checked);
      $t.find(':hidden').prop('disabled', !checked);
    });
    batchRender();
  }).on('change', '.color-items :checkbox', function(event) {
    var checked = this.checked;
    var $t = $(this).parents('li:first');
    var relation = $t.data('relation');
    var $li = $colorsChecked.find('[data-relation="' + relation + '"]');
    toggleChecked();
    if (checked) {
      $li.removeClass('hide');
      $t.find(':hidden').prop('disabled', false);
      createUpload($li);
    } else {
      $li.addClass('hide');
      $t.find(':hidden').prop('disabled', true);
    }
    toggleGroup();
  });

  batchRender();

  $form.on('click', '[name="product[publishTime]"]', function(event) {
    WdatePicker({
      minDate: '%y-%M-{%d+1} %H:%m:%s',
      maxDate: '%y-%M-{%d+7} %H:%m:%s',
      dateFmt: 'yyyy-MM-dd HH:mm:ss'
    });
  }).on('change', '[name="product[timeType]"]', function(event) {
    $publishTime.prop('disabled', this.value !== '2');
  }).on('submit', createSpecData);

  input.clearZero($('.int-only,.price-only'));

});
