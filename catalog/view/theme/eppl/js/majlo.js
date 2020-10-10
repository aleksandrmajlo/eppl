jQuery(document).ready(function($) {
    $('#ButtonLogout').click(function(event) {
        event.preventDefault();
        var href = $(this).data('href');
        location = href;
    });
    var $form = $('#OneClick');
    $('#OneClick').submit(function(e) {
        e.preventDefault();
        var tel = $('[name="tel"]', $form).val();
        if (tel == "") {
            $('[name="tel"]', $form).addClass('is-invalid');
        } else {
            $.ajax({
                url: '/index.php?route=common/ajax/oneclick',
                type: 'POST',
                datatype: "json",
                data: $form.serialize(),
            }).done(function(res) {
                $.fancybox.close()
                $.fancybox.open({
                    src: '#suc-click',
                    type: 'inline',
                });
            }).fail(function() {
                alert('Спробуй пізніше')
            }).always(function() {});
        }
    });
});
jQuery(document).ready(function($) {
    $('.sBasket__minus').click(function(event) {
        event.preventDefault();
        var $tr = $(this).parents('.BasketTR');
        var $inp = $tr.find('.sBasket__input');
        var val = parseInt($inp.val());

        if (val > 1) {
            var updateButton=$tr.find('.updateButton');
            updateButton.prop( "disabled", false );
            $inp.val(val - 1)
        }
    });
    $('.sBasket__plus').click(function(event) {
        event.preventDefault();
        var $tr = $(this).parents('.BasketTR');
        var $inp = $tr.find('.sBasket__input');
        var val = parseInt($inp.val());
        $inp.val(val + 1);
        var updateButton=$tr.find('.updateButton');
        updateButton.prop( "disabled", false );
    });
});