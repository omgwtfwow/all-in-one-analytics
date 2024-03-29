;(function ($, window, document, undefined) {

	$.fn.exopiteSofFieldACEEditor = function () {
		return this.each(function (index) {

			if (typeof ace !== 'undefined') {

				var $this = $(this),
					$textarea = $this.find('.exopite-sof-ace-editor-textarea'),
					options = JSON.parse($this.find('.exopite-sof-ace-editor-options').val()),
					plugin = this,
					editor = ace.edit($this.find('.exopite-sof-ace-editor').attr('id'));

				// global settings of ace editor
				editor.getSession().setValue($textarea.val());

				editor.setOptions(options);

				editor.on('change', function (e) {
					$textarea.val(editor.getSession().getValue()).trigger('change');
				});

				$('.exopite-sof-group').on('exopite-sof-field-group-item-added-before', function (event, $cloned, $group) {

					if ($cloned.find('.exopite-sof-ace-editor').length !== 0) {

						plugin.musterID = $group.find('.exopite-sof-cloneable__muster .exopite-sof-ace-editor').first().attr('id') + '-';

						var count = parseInt($group.find('.exopite-sof-ace-editor').filter(function () {
							return ($(this).parents().not('.exopite-sof-cloneable__muster'));
						}).length);

						$cloned.find('.exopite-sof-ace-editor').each(function (index, el) {
							$(el).attr('id', plugin.musterID + (count + index));
						});

					}

				});

				$('.exopite-sof-group').on('exopite-sof-field-group-item-added-after', function (event, $cloned) {

					$cloned.find('.exopite-sof-field-ace_editor').exopiteSofFieldACEEditor();

				});

			}
		});
	};

	$(document).ready(function () {

		if (typeof ace !== 'undefined') {

			var musterID = '';

			$('.exopite-sof-field-group').find('.exopite-sof-field-ace_editor').each(function (index, el) {

				if (!$(this).parents('.exopite-sof-cloneable__muster').length) {

					var $thisEditor = $(this).find('.exopite-sof-ace-editor');
					var thisId = $thisEditor.attr('id');
					$thisEditor.attr('id', thisId + '-' + index);

				}

			});
		}

		$('.exopite-sof-field-ace_editor').exopiteSofFieldACEEditor();


	});

})(jQuery, window, document);
