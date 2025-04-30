<h2>Новый заказ</h2>

<p><strong>Имя:</strong> {{ $data['name'] }}</p>
<p><strong>Телефон:</strong> {{ $data['phone'] }}</p>
<p><strong>Комментарий:</strong> {{ $data['comment'] ?? '—' }}</p>

<h3>Товары:</h3>
<ul>
    @foreach($data['cart'] as $productId => $quantity)
        <li>ID: {{ $productId }}, Кол-во: {{ $quantity }}</li>
    @endforeach
</ul>
