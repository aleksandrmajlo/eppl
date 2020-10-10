var ocfilter = {
	/**
	   	URL GET Variables
	**/
	url: {},
	/**
	   	PHP Data (languages, filter params..)
	**/
	php: {},
	/**
	   	Initialization
	**/
	init: function() {
		this.helper.setURLVars();

	  /* OCFilter option values switcher */
	  $(document).on('click', '.switcher .selected', function(e) {
			var $this = $(this).parent('.switcher');

	    if (!$this.hasClass('active')) {
	      $('.switcher').removeClass('active');
	      $this.addClass('active');
	    } else {
	      $this.removeClass('active');
	    }
	  });

	  $(document).on('change', '.switcher input[type=\'checkbox\']', function() {
	    var $this = $(this), selected = $this.parents('.switcher').find('.selected'), text = $this.parent('label').text(), length = selected.find('span').length, counter = selected.find('strong').length;

			if (counter) {
	      selected.find('strong').text($this.parents('.switcher').find('.values input[name*=\'[options_id][]\']:checked').length);
			} else {
		    if ($this.prop('checked')) {
	        selected.append('<span id="v-' + this.value + '">' + text + '</span>').find('b').remove();
		    } else {
		      if (length === 1) {
		        $('#v-' + this.value).replaceWith('<b>' + ocfilter.php.text_select + '</b>');
		      } else {
		        $('#v-' + this.value).remove();
		      }
		    }
			}
	  });

	  $(document).click(function(e){
	    if (!$(e.target).parents('.switcher').length) $('.switcher.active').removeClass('active');
	    if (!$(e.target).parents('#colorbox').length) {
				$('#colorbox').remove();
	      $('a.color-handler').removeClass('active');
			}
	  });

		/* Floating Actions list */
		var actions = $('#list-actions'), scrolled = false, timeout = null;

		$(window).on('scroll', function() {
			if (window.pageYOffset > 35 && !scrolled) {
				scrolled = true;

				actions.addClass('scrolled');
			} else if (window.pageYOffset < 35 && scrolled) {
				scrolled = false;

				actions.removeClass('scrolled');
			}
		});

		$('.list input[name*=\'selected\'], .list thead input:first').on('change', function() {
			clearTimeout(timeout);

			actions.addClass('change');

			timeout = setTimeout(function(){ actions.removeClass('change'); }, 230);

		  $('strong > span', actions).text($('input[name*=\'selected\']:checked').length);
		});

		/* Subfields */

		$('.with-subfield').on('change', function(e){
			var $this = $(this), tag = e.target.nodeName.toLowerCase(), subfield = $this.attr('data-subfield');

			if (tag == 'input') {
				if (this.checked) {
	        $('.sf-' + subfield).addClass('visible');
				} else {
	        $('.sf-' + subfield).removeClass('visible');
				}
			} else if (tag == 'select') {
				$('.' + subfield).removeClass('visible');

				if (this.value) {
	        $('.sf-' + this.value.replace(/\_/g, '-')).addClass('visible');
				}
			}
		});
	},
	/**
	   	OCFilter List
	**/
	list: {
		init: function () {
			$('tr.filter input').keydown(function(e) {
				if (e.keyCode == 13) ocfilter.list.filter();
			});

			$('table input.edit, table select.edit').on('change', function(){
				var e = $(this), type = e.attr('type'), post = {
					field: encodeURIComponent(e.attr('name')),
					value: (type == 'checkbox' ? (this.checked ? 1 : 0) : encodeURIComponent(this.value)),
					option_id: e.attr('for')
				};

				e.fadeTo(250, .3);

				$.post('index.php?route=extension/module/ocfilter/editImmediately&user_token=' + ocfilter.url['user_token'], post, function(json){
					if (json['status'] === true) {
						e.fadeTo(250, 1).css('border', '1px solid #4BB349');

						if (type == 'checkbox') {
							var span = e.next('span');

							span.text(span.text() == ocfilter.php.text_enabled ? ocfilter.php.text_disabled : ocfilter.php.text_enabled);
						}
					} else {
						e.fadeTo(250, 1).css('border', '1px solid #E2302F');
					}
			  }, 'json');
			});
		},
		filter: function () {
			var $this = ocfilter, url = 'index.php?route=extension/module/ocfilter/filter&user_token=' + $this.url['user_token'], params = {};

			for (var i = 0; i < $this.php.filter_get.length; i++) {
				var key = $this.php.filter_get[i];

				params[key] = $('[name="' + key + '"]').val();

			  if (params[key] != '') {
					url += '&' + key + '=' + encodeURIComponent(params[key]);
				}
			}

			window.location = url;
		}
	},
	/**
	   	OCFilter Form
	**/
	form: {
		init: function() {
			/* Color picker by SooR. 12-07-2013 */

			var colorbox = [], colors = ['f00', 'ff0', '0f0', '0ff', '00f', 'f0f', 'fff', 'ebebeb', 'e1e1e1', 'd7d7d7', 'cccccc', 'c2c2c2', 'b7b7b7', 'acacac', 'a0a0a0', '959595', 'ee1d24', 'fff100', '00a650', '00aeef', '2f3192', 'ed008c', '898989', '7d7d7d', '707070', '626262', '555', '464646', '363636', '262626', '111', '000', 'f7977a', 'fbad82', 'fdc68c', 'fff799', 'c6df9c', 'a4d49d', '81ca9d', '7bcdc9', '6ccff7', '7ca6d8', '8293ca', '8881be', 'a286bd', 'bc8cbf', 'f49bc1', 'f5999d', 'f16c4d', 'f68e54', 'fbaf5a', 'fff467', 'acd372', '7dc473', '39b778', '16bcb4', '00bff3', '438ccb', '5573b7', '5e5ca7', '855fa8', 'a763a9', 'ef6ea8', 'f16d7e', 'ee1d24', 'f16522', 'f7941d', 'fff100', '8fc63d', '37b44a', '00a650', '00a99e', '00aeef', '0072bc', '0054a5', '2f3192', '652c91', '91278f', 'ed008c', 'ee105a', '9d0a0f', 'a1410d', 'a36209', 'aba000', '588528', '197b30', '007236', '00736a', '0076a4', '004a80', '003370', '1d1363', '450e61', '62055f', '9e005c', '9d0039', '790000', '7b3000', '7c4900', '827a00', '3e6617', '045f20', '005824', '005951', '005b7e', '003562', '002056', '0c004b', '30004a', '4b0048', '7a0045', '7a0026'];

	    colorbox.push('<div id="colorbox">');

			for (var i = 0; i < colors.length; i++) {
	      colorbox.push('<a href="#' + colors[i] + '" style="background-color: #' + colors[i] + ';"><i style="background-color: #' + colors[i] + ';"></i></a>');
			}

			colorbox.push('</div>');

	    colorbox = colorbox.join('');

			$(document).on('click', 'a.color-handler', function(){
				$('#colorbox').remove();
	      $('a.color-handler').not(this).removeClass('active');

				var $this = $(this);

				$this.toggleClass('active');

				if ($this.hasClass('active')) {
					$this.after(colorbox);
		 		}

				return false;
			});

	    $(document).on('click', '#colorbox a', function(){
				var $this = $(this), color = $this.attr('href').substr(1), value = $this.parents('li');

				value.find('input[name*=\'[color]\']').val(color);
	   		value.find('.color-handler').css('background', '#' + color);

				return false;
			});

			$(document).on('click', 'a.image-handler', function(e) {
        e.preventDefault();

    		$('.popover').popover('hide', function() {
    			$('.popover').remove();
    		});

    		var element = this;

    		$(element).popover({
    			html: true,
    			placement: 'left',
    			trigger: 'manual',
    			content: function() {
    				return '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
    			}
    		});

    		$(element).popover('show');

    		$('#button-image').on('click', function() {
    			$('#modal-image').remove();

    			$.ajax({
    				url: 'index.php?route=common/filemanager&user_token=' + getURLVar('user_token') + '&target=' + $(element).closest('li').find('input[name$=\'[image]\']').attr('id') + '&thumb=' + $(element).attr('id'),
    				dataType: 'html',
    				beforeSend: function() {
    					$('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
    					$('#button-image').prop('disabled', true);
    				},
    				complete: function() {
    					$('#button-image i').replaceWith('<i class="fa fa-pencil"></i>');
    					$('#button-image').prop('disabled', false);
    				},
    				success: function(html) {
    					$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

    					$('#modal-image').modal('show');
    				}
    			});

    			$(element).popover('hide', function() {
    				$('.popover').remove();
    			});
    		});

    		$('#button-clear').on('click', function() {
          $(element).find('img').attr('src', '');

    			$(element).closest('li').find('input[name$=\'[image]\']').attr('value', '');

    			$(element).popover('hide', function() {
    				$('.popover').remove();
    			});
    		});
			});

			/* Numeric values clone for another name fields */
		 	if (ocfilter.php.languages.length > 1) {
				$(document).on('keyup', '.value-name', function() {
					var $this = $(this), fields = $this.parents('.fields').find('input[name*=\'[name]\']').not(this), text = this.value, numeric = /^\d/g.test(text);

					if (numeric) fields.val(text);
				});
			}

			$(document).on('change', 'input[name=\'color\']', function(){
				if (this.checked) {
					$('a.color-handler').addClass('visible');
				} else {
          $('a.color-handler.visible').removeClass('visible');
				}

        ocfilter.php.color = this.checked;
      });

      $(document).on('change', 'input[name=\'image\']', function(){
    		if (this.checked) {
					$('a.image-handler').addClass('visible');
				} else {
          $('a.image-handler.visible').removeClass('visible');
				}

        ocfilter.php.image = this.checked;
			});
		},

		valueRow: $('#sortable li').length,

	  deleteValue: function(value) {
	    value.parent('li').remove();

			this.valueRow--;
	  },

	  addValue: function() {
			var html = [];

			html.push('<li>');
			html.push('	<a class="delete" onclick="ocfilter.form.deleteValue($(this));">Delete</a>');
			html.push('	<div class="fields">');
			html.push('		<input type="hidden" name="ocfilter_option_value[insert][' + this.valueRow + '][color]" value="" />');
			html.push('		<input type="hidden" name="ocfilter_option_value[insert][' + this.valueRow + '][image]" value="" id="value-image-' + this.valueRow + '" />');
			html.push('		<input type="number" name="ocfilter_option_value[insert][' + this.valueRow + '][sort_order]" value="' + this.valueRow + '" />');

			for (var i = 0; i < ocfilter.php.languages.length; i++) {
				var language = ocfilter.php.languages[i];

				html.push('	<label><input type="text" class="value-name" name="ocfilter_option_value[insert][' + this.valueRow + '][language][' + language.language_id + '][name]" value="" size="80" placeholder="Значение опции #' + this.valueRow + '" />&nbsp;<img src="language/' + language.code + '/' + language.code + '.png" title="' + language.name + '" /></label>');
			}

			html.push('	</div>');
			html.push('	<a href="#" class="color-handler' + (ocfilter.php.color ? ' visible' : '') + '" title="' + ocfilter.php.text_select_color + '"></a>');
			html.push('	<a href="#" class="image-handler' + (ocfilter.php.image ? ' visible' : '') + '" title="' + ocfilter.php.text_browse_image + '" id="value-image-thumb-' + this.valueRow + '"><img src="" alt="" /><i class="fa fa-fw fa-picture-o"></i></a>');
		  html.push('</li>');

      $('#sortable').append(html.join(''));

    	this.valueRow++;
	  }
	},
	/**
	   	OCFilter Product Form
	**/
	productForm: {
		category_id: null,
		product_id: null,
		length: null,
		init: function() {
      $('a[href=\'#tab-links\']').parent('li').after('<li><a href="#tab-ocfilter" data-toggle="tab">Опции фиьтра</a></li>');
			$('#tab-links').after('<div class="tab-pane" id="tab-ocfilter"><h2>' + ocfilter.php.ocfilter_select_category + '</h2></div>');

      $('a[href=\'#tab-general\']').tab('show');

			if (undefined !== ocfilter.url['product_id']) {
				this.product_id = ocfilter.url['product_id'];
			}

      this.length = $('#product-category div, input[type=\'checkbox\'][name=\'product_category[]\']:checked').length;

	  	setInterval(function() {
				var length = $('#product-category div, input[type=\'checkbox\'][name=\'product_category[]\']:checked').length;

				if (ocfilter.productForm.length != length) {
					ocfilter.productForm.length = length;

					ocfilter.productForm.update();
				}
			}, 500);

      this.update();
		},
		update: function() {
      if ($('input[type=\'checkbox\'][name=\'product_category[]\']:checked:last').length > 0) {
			  this.category_id = $('input[name=\'product_category[]\']:checked:last').val();
      } else if ($('input[type=\'hidden\'][name=\'product_category[]\']:last').length > 0) {
			  this.category_id = $('input[name=\'product_category[]\']:last').val();
      }

			var html = [], get = {
				user_token: ocfilter.url['user_token'],
				category_id: this.category_id
			};

			if (this.product_id) {
				get.product_id = this.product_id;
			}

      if (!get.category_id) {
      	$('#tab-ocfilter').html('<h2>' + ocfilter.php.ocfilter_select_category + '</h2>');

				return;
			}

      $.get('index.php?route=extension/module/ocfilter/callback', get, function(json) {
        if (json.message) {
          $('#tab-ocfilter').html('<h2>' + json.message + '</h2>');

					return;
				}

				html.push('<table class="form product-ocfilter-values">');

				for (var i = 0; i < json.options.length; i++) {
          var option = json.options[i], values = [], selecteds = [];

          html.push('<tr' + (!option.status ? ' class="disabled"' : '') + '>');
          html.push('<td width="20%">' + option.name + '</td><td width="80%">');

          if (option.type == 'slide' || option.type == 'slide_dual') {
						html.push('<input type="hidden" name="ocfilter_product_option[' + option.option_id + '][values][0][selected]" value="1" />');
						html.push('<input type="text" name="ocfilter_product_option[' + option.option_id + '][values][0][slide_value_min]" value="' + option.slide_value_min + '" size="5" class="slide-value-min" />&nbsp;&mdash;&nbsp;<input type="text" name="ocfilter_product_option[' + option.option_id + '][values][0][slide_value_max]" value="' + option.slide_value_max + '" size="5" class="slide-value-max" />' + option.postfix + '');
					} else {
						if (option.values) {
							for (var j in option.values) {
	              var value = option.values[j];

	              if (value.selected) {
	                selecteds.push('<span id="v-' + value.value_id + '">' + value.name + option.postfix + '</span>');
	              }

	              values.push('<div>');

                values.push(' <label><input type="checkbox" name="ocfilter_product_option[' + option.option_id + '][values][' + value.value_id + '][selected]" value="' + value.value_id + '"' + (value.selected ? ' checked="checked"' : '') + ' />' + value.name + option.postfix + '</label>');

                values.push('</div>');
							}

	            if (!selecteds.length) selecteds = ['<b>' + ocfilter.php.text_select + '</b>'];

	            html.push('<div class="switcher"><div class="selected">' + selecteds.join('') + '</div><div class="values">' + values.join('') + '</div>');
	          }
					}

          html.push('</td></tr>');
        }

        html.push('</table>');

      	$('#tab-ocfilter').html(html.join(''));
      }, 'json');
    }
	},
	/**
	   	Helpers
	**/
	helper: {
		setURLVars: function() {
			var vars = window.location.href.replace(window.location.hash, '').split('?')[1].split('&'), $this = ocfilter;

			for (var i = 0; i < vars.length; i++) {
				var parts = vars[i].split('=');

				ocfilter.url[parts[0]] = parts[1];
			}
		}
	}
};

$(function(){
  ocfilter.init();

	/**
	   	OCFilter List
	**/
  if (ocfilter.url['route'] == 'extension/module/ocfilter/filter') {
		ocfilter.list.init();
	}

	/**
	   	OCFilter Form
	**/
	if (ocfilter.url['route'] == 'extension/module/ocfilter/addFilter' || ocfilter.url['route'] == 'extension/module/ocfilter/editFilter') {
		ocfilter.form.init();
	}

	/**
			OCFilter Product Form
	**/
  if (ocfilter.url['route'] == 'catalog/product/add' || ocfilter.url['route'] == 'catalog/product/edit') {
    ocfilter.productForm.init();
	}
});