(function($) {
    var cart;
    $.jCart = {
        init : function(){ //settings
            // инициализация модуля
            var btn = $('#product .cart-add');
            var guid = false;
            if (btn.length > 0){
                guid = btn.attr('data-key');
            }
            var getCart = $.localStorage.getItem('cart');
            if (getCart != null && getCart.length > 0 && getCart != 'null')
            {
                cart = JSON.parse(getCart);
                for(var i=0; i<cart.length;i++) {
                    $("tr[data-key="+cart[i][0]+"] img").attr('src', '/plugins/product/img/del.png');
                    if (cart[i][0] == guid)
                    {
                        guid = true;
                    }
                }
            }
            if ($('#order').length > 0)
            {
                $('#basket').hide();
                this.get(cart);
            }
            else
            {
                this.cart();
            }

            if (guid > 0 || guid == false){
                btn.val(langCart.add);
            }
            else
            {
                btn.val(langCart.del);
                btn.addClass('cart-del');
            }
        },
        add : function(item){
            // добавление одного наименования товара в корзину.
            var getCart = $.localStorage.getItem('cart');
            if (getCart == null || getCart.length == 0 || getCart == 'null')
            {
                cart = new Array();
                cart.push(item);
                $.localStorage.setItem('cart', JSON.stringify(cart));
            }
            else
            {
                cart = JSON.parse(getCart);
                cart.push(item);
                $.localStorage.setItem('cart', JSON.stringify(cart));
            }
            this.cart();
        },
        get : function(cart){
            // обновление информации о количестве товаров в корзине и общей сумме
            var getCart = $.localStorage.getItem('cart');
            if (getCart != null && getCart.length > 0 && getCart != 'null')
            {
                cart = JSON.parse(getCart);

                for(var i=0; i<cart.length;i++) {
                    $("#order table").append('<tr><td>'+cart[i][1]+'</td><td><input type="text" data-key="'+cart[i][0]+'" value="'+cart[i][2]+'" class="amount" /></td><td class="alignright">'+cart[i][3]+'</td><td class="button"><a href="javascript:void()" onclick="$.jCart.del('+cart[i][0]+');$(this).parent().parent().empty();"><img src="/plugins/product/img/del.png" alt="'+langCart.del+'" /></a></td></tr>');
                }

                var comp = $.localStorage.getItem('comp');
                $("#order_comp").val(comp);
                var phone = $.localStorage.getItem('phone');
                $("#order_phone").val(phone);
                $.localStorage.getItem('phone');
                var email = $.localStorage.getItem('email');
                $("#order_email").val(email);
                var fio = $.localStorage.getItem('comp');
                $("#order_fio").val(fio);
            }
        },
        count : function(context) {
            // изменение количества товаров одного и того же наименования в корзине
        },
        del : function(id) {
            // удаление одного наименования из корзины
            var getCart = $.localStorage.getItem('cart');
            if (getCart != null && getCart.length > 0 && getCart != 'null')
            {
                cart = JSON.parse(getCart);
                for(var i=0; i<cart.length;i++) {
                    if (cart[i][0] == id)
                    {
                        cart.splice(i,1);
                    }
                }
                $.localStorage.setItem('cart', JSON.stringify(cart));
            }
            this.cart();
        },
        cart: function() {
            // корзина
            if ($('#basket').length > 0)
            {
                if (cart != null && cart.length > 0)
                {
                    $('#basket p').html('<img src="/plugins/product/img/full-basket.png" alt="" /><a href="/product/order"> '+langCart.checkout+'</a>');
                }
                else
                {
                    $('#basket p').html('<img alt="" src="/plugins/product/img/empty-basket.png"> '+langCart.empty);
                }
            }
        },
        clear : function() {
            // полная очистка корзины
            $.localStorage.setItem('cart', null);
        },
        showMessage : function(message) {
            // показ сообщения при добавлении товара
        }
    }
    $.jCart.init();
})(jQuery);

$('#catalog .button img').click(function() {
    var elem = $(this);
    var obj = elem.parent().parent();
    if (elem.attr('src') == '/plugins/product/img/add.png'){
        elem.attr('src', '/plugins/product/img/del.png');
        var item = new Array();
        item[0] = obj.attr('data-key');
        item[1] = obj.find('td:eq(0)').text();
        item[2] = 1;
        item[3] = obj.find('td:eq(1)').text();
        $.jCart.add(item);
    }
    else {
        elem.attr('src', '/plugins/product/img/add.png');
        $.jCart.del(obj.attr('data-key'));
    }
    //$.jCart.add(obj);
    return false;
});

$('#product .buy input').click(function() {
    var elem = $(this);
    if (elem.hasClass('cart-del')){
        elem.removeClass('cart-del');
        $.jCart.del(elem.attr('data-key'));
        $(this).val(langCart.add);
    }
    else {
        elem.addClass('cart-del');
        var item = new Array();
        item[0] = elem.attr('data-key');
        item[1] = $("#product h1").text();
        item[2] = 1;
        item[3] = $("#product .price").html();
        $.jCart.add(item);
        $(this).val(langCart.del);
    }

    return false;
});
$(".form").validate({ignore: "#order_comm"});
$(".form").submit(function(e) {
    e.preventDefault();
    if ($('form').valid())
    {
        var amount = new Array();
        $( "input.amount" ).each(function( index ) {
            var item = new Array();
            item.push($(this).attr('data-key'), $(this).val());
            amount.push(item);
            //amount[$(this).attr('data-key')] = $(this).val();
        });

        var comp = $("#order_comp").val();
        $.localStorage.setItem('comp', comp);
        var phone = $("#order_phone").val();
        $.localStorage.setItem('phone', phone);
        var email = $("#order_email").val();
        $.localStorage.setItem('email', email);
        var fio = $("#order_fio").val();
        $.localStorage.setItem('comp', fio);
        var comm = $("#order_comm").val();
        var csrf = $("#product_csrf").val();

        $.post("/product/order", { order: 'add', items: JSON.stringify(amount), comp: comp, phone: phone, email: email, fio: fio, comm: comm, token: csrf})
            .done(function() {
                $.jCart.clear();
                location.href = '/product/confirm';
            });
    }
    return false;
});