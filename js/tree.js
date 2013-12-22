$(document).ready(function(){
    /*$("span[parent=true]").each(function() {
     $('.' + $(this).attr('rel')).hide();
     $(this).html("+").attr('view', 'false');
     });*/

    $("span[parent=true]").css('cursor','pointer');
    $("span[parent=false]").css('padding-right', '6px');
    $("span[hide=all]").click(function() {
        $("span[parent=true]").each(function() {
            $('.' + $(this).attr('rel')).hide();
            $(this).html("+").attr('view', 'false');
        });
    });

    $("span[parent=true]").click(function() {
        var rel = $(this).attr('rel');
        if ($(this).html() == "-") {
            $.jTree.add(rel);
            $(this).attr('view', 'false');
            $('.' + rel).hide();
            $(this).html("+");
        } else {
            $.jTree.del(rel);
            $(this).attr('view', 'true');
            $('.' + rel).show();
            $("span[view=false]").each(function() {
                $('.' + $(this).attr('rel')).hide();
            });
            $(this).html("-");
        }
    });

    $("a[data-action=delProdImg]").click(function() {
        if (confirm('Действительно удалить файл?'))
        {
            var dir = $(this).attr('data-dir');
            var uid = $(this).attr('data-key');
            var csrf = $("#product_csrf").val();

            $.post("/product/order", { item: 'del', dir: dir, id: uid, token: csrf});
        }
    });

    $.jCart = {
        init : function(){ //settings
        },
        save: function(id){ //settings
            var amount = new Array();
            $( "input.amount" ).each(function( index ) {
                var item = new Array();
                item.push($(this).attr('data-key'), $(this).val());
                amount.push(item);
                //amount[$(this).attr('data-key')] = $(this).val();
            });
            var csrf = $("#product_csrf").val();

            $.post("/product/order", { order: 'save', items: JSON.stringify(amount), token: csrf, id: id})
                .done(function() {alert('Сохранено')});
        }
    };

    $.jTree = {
        init : function(){
            $.jTree.tree = new Array();
            $.jTree.getTree = $.localStorage.getItem('tree');

            if ($.jTree.getTree != null && $.jTree.getTree.length > 0 && $.jTree.getTree != 'null')
            {
                $.jTree.tree = JSON.parse($.jTree.getTree);
                for(var i=0; i<$.jTree.tree.length;i++) {
                    var rel = $.jTree.tree[i];
                    $('span[rel="'+rel+'"]').attr('view', 'false');
                    $('.' + rel).hide();
                    $('span[rel="'+rel+'"]').html("+");
                }
            }
        },
        add: function(item){
            if ($.jTree.getTree == null || $.jTree.getTree.length == 0 || $.jTree.getTree == 'null')
            {
                $.jTree.tree.push(item);
                $.localStorage.setItem('tree', JSON.stringify($.jTree.tree));
            }
            else
            {
                $.jTree.tree = JSON.parse($.jTree.getTree);
                $.jTree.tree.push(item);
                $.localStorage.setItem('tree', JSON.stringify($.jTree.tree));
            }
        },
        del: function(item){
            if ($.jTree.getTree != null && $.jTree.getTree.length > 0 && $.jTree.getTree != 'null')
            {
                $.jTree.tree = JSON.parse($.jTree.getTree);
                for(var i=0; i<$.jTree.tree.length;i++) {
                    if ($.jTree.tree[i] == item)
                    {
                        $.jTree.tree.splice(i,1);
                    }
                }
                $.localStorage.setItem('tree', JSON.stringify($.jTree.tree));
            }
        }
    }
    $.jTree.init();
});