<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- SEO теги (серверные для соцсетей) -->
    <title>{{ $seoData['title'] ?? 'IsotopEnergy — Термопанели и архитектурный декор в Молдове' }}</title>

    <meta name="description"
        content="{{ $seoData['description'] ?? 'Производство термопанелей для утепления домов и архитектурного декора. Доставка по Молдове.' }}">

    <!-- Open Graph для соцсетей -->
    <meta property="og:title" content="{{ $seoData['og_title'] ?? ($seoData['title'] ?? 'IsotopEnergy') }}">

    <meta property="og:description"
        content="{{ $seoData['og_description'] ?? ($seoData['description'] ?? 'Термопанели в Молдове') }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $seoData['canonical'] ?? request()->url() }}">
    <meta property="og:locale" content="{{ ($locale ?? 'ru') === 'ro' ? 'ro_RO' : 'ru_RU' }}">

    <!-- Open Graph изображение -->
    <meta property="og:image" content="{{ asset('/images/og-image.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/jpeg">

    <!-- Canonical -->
    <link rel="canonical" href="{{ $seoData['canonical'] ?? request()->url() }}">

    <!-- Остальное без изменений -->
    <link rel="icon" type="image/svg+xml" href="storage/logo/logo.png">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Raleway:wght@600;700&display=swap&subset=latin,cyrillic-ext"
        rel="stylesheet">

    <meta name="google-site-verification" content="4K6Xd5ZgxKMDtW_o70iKg1HfJhTh0yTp4ZrH1zw1ERY" />

    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
