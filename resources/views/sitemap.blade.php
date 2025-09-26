{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach ($urls as $u)
<url>
    <loc>{{ $u['loc'] }}</loc>
    <xhtml:link rel="alternate" hreflang="ru" href="{{ $u['alt']['ru'] }}"/>
    <xhtml:link rel="alternate" hreflang="ro" href="{{ $u['alt']['ro'] }}"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $u['alt']['x'] }}"/>
    @if(isset($u['lastmod']))<lastmod>{{ $u['lastmod'] }}</lastmod>@endif
    @if(isset($u['priority']))<priority>{{ $u['priority'] }}</priority>@endif
</url>
@endforeach
</urlset>
