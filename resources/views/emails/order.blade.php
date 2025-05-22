<h2 style="margin-bottom: 10px;">
    {{ $isClient ? '✅ Ваш заказ принят!' : '📦 Новый заказ' }}
</h2>

@if ($isClient)
    <p>Спасибо за заказ! С вами свяжится наш менеджер.</p>
    <hr>
    <p>Детали закза:</p>
@endif

<p><strong>ФИО:</strong> {{ $data['last_name'] }} {{ $data['first_name'] }}</p>
<p><strong>Телефон:</strong> {{ $data['phone'] }}</p>
@if (!empty($data['email']))
    <p><strong>Email:</strong> {{ $data['email'] }}</p>
@endif

<p><strong>Способ оплаты:</strong> {{ ucfirst($data['payment_method']) }}</p>
<p><strong>Способ получения:</strong> {{ $data['delivery_method'] === 'pickup' ? 'Самовывоз' : 'Доставка' }}</p>

@if ($data['delivery_method'] === 'delivery')
    <h3 style="margin-top: 20px;">🚚 Адрес доставки</h3>
    <p>
        {{ $data['address']['country'] }},
        {{ $data['address']['region'] }},
        {{ $data['address']['city'] }},
        {{ $data['address']['street'] }}
    </p>
@endif

@if (!empty($data['comment']))
    <p><strong>Комментарий:</strong> {{ $data['comment'] }}</p>
@endif

<h3 style="margin-top: 20px;">🛒 Товары:</h3>
<table cellpadding="6" cellspacing="0" border="1" style="border-collapse: collapse;">
    <thead>
        <tr>
            <th>Название</th>
            <th>Цена</th>
            <th>Кол-во</th>
            <th>Сумма</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['products'] as $product)
            <tr>
                <td>{{ $product['title'] }}</td>
                <td>
                    @if ($product['price'] != $product['discounted_price'])
                        <span style="text-decoration: line-through; color: gray;">{{ $product['price'] }} mdl</span><br>
                    @endif
                    <strong>{{ $product['discounted_price'] }} mdl</strong>
                </td>
                <td>{{ $product['qty'] }}</td>
                <td><strong>{{ $product['total'] }} mdl</strong></td>
            </tr>
        @endforeach
    </tbody>
</table>
<h3 style="margin-top: 20px;">💸 Итог:</h3>
@if ($data['discount_amount'] > 0)
    <p><strong>Сумма без скидки:</strong> {{ $data['total_original'] }} mdl</p>
    <p><strong>Скидка:</strong> -{{ $data['discount_amount'] }} mdl</p>
@endif
<p><strong>Итого к оплате:</strong> {{ $data['total_with_discount'] }} mdl</p>
