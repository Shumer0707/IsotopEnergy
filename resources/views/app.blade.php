<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- SEO теги (динамические из контроллера или fallback) -->
    <title inertia>{{ $page['props']['seo']['title'] ?? 'IsotopEnergy — Термопанели и архитектурный декор в Молдове' }}
    </title>
    <meta head-key="description" name="description"
        content="{{ $page['props']['seo']['description'] ?? 'Производство термопанелей для утепления домов и архитектурного декора. Доставка по Молдове.' }}">

    <!-- Open Graph -->
    <meta head-key="og:title" property="og:title"
        content="{{ $page['props']['seo']['og_title'] ?? ($page['props']['seo']['title'] ?? 'IsotopEnergy') }}">
    <meta head-key="og:description" property="og:description"
        content="{{ $page['props']['seo']['og_description'] ?? ($page['props']['seo']['description'] ?? 'Термопанели в Молдове') }}">
    <meta head-key="og:type" property="og:type" content="website">
    <meta head-key="og:url" property="og:url" content="{{ $page['props']['seo']['canonical'] ?? request()->url() }}">
    <meta head-key="og:locale" property="og:locale"
        content="{{ $page['props']['locale'] === 'ro' ? 'ro_RO' : 'ru_RU' }}">
    <!-- Open Graph изображение -->
    <meta head-key="og:image" property="og:image" content="{{ asset('/images/og-image.jpg') }}">
    <meta head-key="og:image:width" property="og:image:width" content="1200">
    <meta head-key="og:image:height" property="og:image:height" content="630">
    <meta head-key="og:image:type" property="og:image:type" content="image/jpeg">

    <!-- Canonical -->
    <link head-key="canonical" rel="canonical" href="{{ $page['props']['seo']['canonical'] ?? request()->url() }}">

    <!-- Остальное без изменений -->
    <link rel="icon" type="image/svg+xml" href="/favicon-2.svg">
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
