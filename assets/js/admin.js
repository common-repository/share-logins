jQuery(function($){
	// enable/disable
	var chk_all = $(".check-all");
	var chk_def = $(".check-all-default");
	var chk_cst = $(".check-all-custom");

	chk_all.change(function () {
	    $('.check-all-default,.check-all-custom').prop('checked',this.checked).change();
	});

	chk_def.change(function () {
	    $('.check-default').prop('checked',this.checked);
	    $('.check-this').change();
	});

	chk_cst.change(function () {
	    $('.check-custom').prop('checked',this.checked);
	    $('.check-this').change();
	});

	// counter
	$('.check-this').change(function(e){
		var total = $('.check-this').length;
		var enabled = $('.check-this:not(:checked)').length;
		var disabled = $('.check-this:checked').length;

		$('#disabled-counter .counter').text(disabled);
		$('#enabled-counter .counter').text(enabled);
	}).change();

	// cx-regen-thumbs
	$('#cxis-regen-thumbs').click(function(e){
		$('#cxis-regen-thumbs').text(CXIS.regening).attr('disabled', true);
		$('#cxis-message').html('').hide();
		$.ajax({
			url: CXIS.ajaxurl,
			type: 'GET',
			data: { action : 'cxis-regen-thumbs', '_nonce' : CXIS.nonce },
			success: function(res) {
				$('#cxis-regen-thumbs').text(CXIS.regen).attr('disabled', false);
				$('#cxis-message').html(res.message).show();
				console.log(res);
			},
			error: function(err){
				$('#cxis-regen-thumbs').text(CXIS.regen).attr('disabled', false);
				console.log(err);
			}
		})
	});

	// dismiss
	$('.cxis-dismiss').click(function(e){
		var $this = $(this);
		$this.parent().slideToggle();
		$.ajax({
			url: CXIS.ajaxurl,
			data: { action : 'cxis-dismiss', meta_key : $this.data('meta_key'), '_nonce' : CXIS.nonce  },
			type: 'GET',
			success: function(res) {console.log(res);},
			error: function(err){console.log(err);}
		})
	})
})