<h2 style="margin-bottom: 10px;">
    {{ $isClient ? '✅ Ваш заказ принят!' : '📦 Новый заказ' }}
</h2>

@if ($isClient)
    <p>Спасибо за заказ! С вами свяжется наш менеджер.</p>
    <hr>
    <p>Детали заказа:</p>
@endif

<p><strong>ФИО:</strong> {{ $data['last_name'] }} {{ $data['first_name'] }}</p>
<p><strong>Телефон:</strong> {{ $data['phone'] }}</p>
@if (!empty($data['email']))
    <p><strong>Email:</strong> {{ $data['email'] }}</p>
@endif

<p><strong>Способ оплаты:</strong>
    {{ $data['payment_method'] === 'cash'
        ? 'Наличные'
        : ($data['payment_method'] === 'card'
            ? 'Банковская карта'
            : 'По счету') }}
</p>
<p><strong>Способ получения:</strong> {{ $data['delivery_method'] === 'pickup' ? 'Самовывоз' : 'Доставка' }}</p>

@if ($data['delivery_method'] === 'delivery' && !empty($data['address']))
    <h3 style="margin-top: 20px;">🚚 Адрес доставки</h3>
    <p>
        {{ $data['address']['country'] ?? '' }},
        {{ $data['address']['region'] ?? '' }},
        {{ $data['address']['city'] ?? '' }},
        {{ $data['address']['street'] ?? '' }}
    </p>
@endif

@if (!empty($data['comment']))
    <p><strong>Комментарий:</strong> {{ $data['comment'] }}</p>
@endif

<h3 style="margin-top: 20px;">🛒 Товары:</h3>
<table cellpadding="6" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th style="text-align: left;">Товар</th>
            <th style="text-align: center;">Артикул</th>
            <th style="text-align: right;">Цена</th>
            <th style="text-align: center;">Кол-во</th>
            <th style="text-align: right;">Сумма</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['products'] as $product)
            <tr>
                <td>{{ $product['title'] }}</td>
                <td style="text-align: center; font-family: monospace;">{{ $product['sku'] ?? '' }}</td>
                <td style="text-align: right;">
                    @if ($product['price'] != $product['discounted_price'])
                        <span style="text-decoration: line-through; color: gray;">{{ $product['price'] }}
                            MDL</span><br>
                    @endif
                    <strong>{{ $product['discounted_price'] }} MDL</strong>
                </td>
                <td style="text-align: center;">{{ $product['qty'] }}</td>
                <td style="text-align: right;"><strong>{{ $product['total'] }} MDL</strong></td>
            </tr>
        @endforeach
    </tbody>
</table>

<h3 style="margin-top: 20px;">💸 Итог:</h3>
@if ($data['discount_amount'] > 0)
    <p><strong>Сумма без скидки:</strong> {{ $data['total_original'] }} MDL</p>
    <p><strong>Скидка:</strong> -{{ $data['discount_amount'] }} MDL</p>
@endif
<p style="font-size: 18px;"><strong>Итого к оплате:</strong> {{ $data['total_with_discount'] }} MDL</p>
