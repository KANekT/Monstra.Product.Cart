<style>
    #clientmail {
        margin: 10px auto;
        width: 680px;
    }

    #clientmail .amount {
        float: right;
        text-align: right;
        width: 60px;
    }

    #clientmail a {
        color: #333;
        text-decoration: underline;
    }

    #clientmail a:hover {
        text-decoration: none;
    }

    #clientmail table {
        border-collapse: collapse;
        width: 100%;
    }

    #clientmail table th {
        background: #eee;
        line-height: 18px;
        padding: 5px;
        text-align: left;
    }

    #clientmail table td {
        border-bottom: 1px solid #eee;
        border-top: 1px solid #eee;
        line-height: 18px;
        padding: 5px;
    }

    #clientmail table tr:hover {
        background: #f8f8f8;
    }

    #clientmail .total {
        font-size: 18px;
        margin: 20px 0 0;
        text-align: right;
    }
</style>
<div id="clientmail">
    <h1>Заказ с сайта <?php echo $url;?></h1>
    <table>
        <p>Ваш заказ #<?php echo $id;?> выполнен. Спасибо за сотрудничество!</p>
        <thead>
        <tr>
            <th>Наименование</th>
            <th>Количество</th>
            <th>Цена</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $all = 0;
        foreach($items as $key => $item):
            $all += (float)$item['price'] * (int)$item['amount'];
        ?>
        <tr>
            <td><?php echo $item['name'];?></td>
            <td class="alignright"><?php echo $item['amount'];?></td>
            <td class="alignright"><?php echo $item['price'];?> руб</td>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>
    <p class="total">Итого: <strong><?php echo $all;?></strong> руб.</p>
    <p>Компания <a href="<?php echo $url;?>"><?php echo $site;?></a>.</p>
</div>