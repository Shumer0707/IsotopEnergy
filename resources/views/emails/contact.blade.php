<!DOCTYPE html>
<html>

<head>
    <title>Новое сообщение</title>
</head>

<body>
    <p><strong>Имя:</strong> {{ $data['name'] }}</p>
    <p><strong>Телефон:</strong> {{ $data['phone'] }}</p>
    <p><strong>Email:</strong> {{ $data['email'] ?? '—' }}</p>
    <p><strong>Сообщение:</strong></p>
    <p>{{ $data['message'] }}</p>
</body>

</html>
