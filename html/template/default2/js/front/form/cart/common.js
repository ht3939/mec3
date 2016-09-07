/*
# カート共通のJavaScript
*/
;(function($){
	'use strict';

	/*
	## ラジオボタンのチェック
	*/
	$(function(){
		var $doc = $(document);

		//ページが読み込まれた時に、チェックされたラジオボタンを元に選択状態を設定する。
		$doc.on('ready', function(){
			$('.js-radio-block-input:checked').closest('.js-radio-block-active').addClass('active');
		});

		//ラジオボタンがチェックされた時に、対応したボックスを選択状態にする。
		$doc.on('click', '.js-radio-block-input', function(){
			var $this = $(this);
			$this.closest('.js-radio-block-group').find('.js-radio-block-active').removeClass('active');
			$this.closest('.js-radio-block-active').addClass('active');
		});

	});



	/*
	## チェックボックスのチェック
	*/
	$(function(){
		var $doc = $(document);

		//ページが読み込まれた時に、チェックされたチェックボックスを元に選択状態を設定する。
		$doc.on('ready', function(){
			$('.js-check-block-input:checked').closest('.js-check-block-active').addClass('active');
		});

		//チェックボックスがチェックされた時に、対応したボックスを選択状態にする。
		$doc.on('click', '.js-check-block-input', function(){
			var $this = $(this);
			var $active = $this.closest('.js-check-block-active');
			if($this.prop('checked') === true){
				$active.addClass('active');
			}
			else{
				$active.removeClass('active');
			}
		});

	});



})(jQuery);