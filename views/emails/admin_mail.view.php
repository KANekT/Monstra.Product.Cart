<style>
    #adminmail {
        margin: 10px auto;
        width: 680px;
    }

    #adminmail .amount {
        float: right;
        text-align: right;
        width: 60px;
    }

    #adminmail a {
        color: #333;
        text-decoration: underline;
    }

    #adminmail a:hover {
        text-decoration: none;
    }

    #adminmail table {
        border-collapse: collapse;
        width: 100%;
    }

    #adminmail table th {
        background: #eee;
        line-height: 18px;
        padding: 5px;
        text-align: left;
    }

    #adminmail table td {
        border-bottom: 1px solid #eee;
        border-top: 1px solid #eee;
        line-height: 18px;
        padding: 5px;

    }

    #adminmail table tr:hover {
        background: #f8f8f8;
    }

    #adminmail .total {
        font-size: 18px;
        margin: 20px 0 0;
        text-align: right;
    }
</style>

<div id="adminmail">
    <h1>Заказ от компании <?php echo $order['comp'];?></h1>
    <table>
        <p>Поступил новый заказ</p>
        <thead>
        <tr>
            <th>Наименование</th>
            <th>Количество</th>
            <th>Цена</th>
            <th> </th>
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
    <p><strong>Название компании</strong></p>
    <p><?php echo $order['comp'];?></p>
    <p><strong>Телефон</strong></p>
    <p><?php echo $order['phone'];?></p>
    <p><strong>Контактный E-mail</strong></p>
    <p><?php echo $order['email'];?></p>
    <p><strong>Контактное лицо</strong></p>
    <p><?php echo $order['fio'];?></p>
    <p><strong>Комментарий к заказу</strong></p>
    <p><?php echo $order['comm'];?></p>
</div>