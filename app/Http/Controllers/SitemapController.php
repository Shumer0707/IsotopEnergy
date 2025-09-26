<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
  public function index(Request $request)
  {
    $urls = Cache::remember('sitemap_urls', 60 * 24, function () use ($request) {
      return $this->buildUrls($request);
    });

    $this->outputXML($urls);
  }

  private function outputXML(array $urls): void
  {
    header('Content-Type: application/xml; charset=UTF-8');

    echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . PHP_EOL;

    foreach ($urls as $u) {
      echo '  <url>' . PHP_EOL;
      echo '    <loc>' . htmlspecialchars($u['loc']) . '</loc>' . PHP_EOL;
      echo '    <xhtml:link rel="alternate" hreflang="ru" href="' . htmlspecialchars($u['alt']['ru']) . '"/>' . PHP_EOL;
      echo '    <xhtml:link rel="alternate" hreflang="ro" href="' . htmlspecialchars($u['alt']['ro']) . '"/>' . PHP_EOL;
      echo '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($u['alt']['x']) . '"/>' . PHP_EOL;

      if (isset($u['lastmod'])) {
        echo '    <lastmod>' . $u['lastmod'] . '</lastmod>' . PHP_EOL;
      }
      if (isset($u['priority'])) {
        echo '    <priority>' . $u['priority'] . '</priority>' . PHP_EOL;
      }

      echo '  </url>' . PHP_EOL;
    }

    echo '</urlset>';
    exit;
  }

  private function buildUrls(Request $request): array
  {
    $host = rtrim($request->getSchemeAndHttpHost(), '/');
    $urls = [];

    $add = function (string $basePath, ?string $lastmod, ?string $priority = null) use (&$urls, $host) {
      $basePath = '/' . trim($basePath, '/');
      if ($basePath !== '/') {
        $basePath .= '/';
      }

      $build = fn(string $loc) => $basePath === '/'
        ? "$host/$loc/"
        : "$host/$loc$basePath";

      foreach (['ru', 'ro'] as $loc) {
        $locUrl = $build($loc);
        $urls[] = [
          'loc'      => $locUrl,
          'alt'      => [
            'ru' => $build('ru'),
            'ro' => $build('ro'),
            'x'  => $build('ru'),
          ],
          'lastmod'  => $lastmod,
          'priority' => $priority,
        ];
      }
    };

    $today = now()->toDateString();

    // Статические страницы
    $add('/',         $today, '1.0');
    $add('/about',    $today, '0.8');
    $add('/contacts', $today, '0.8');

    // Категории
    Category::select('id', 'updated_at')->orderBy('id')
      ->chunkById(500, function ($chunk) use (&$add) {
        foreach ($chunk as $c) {
          $add("/category/{$c->id}", optional($c->updated_at)->toDateString(), '0.7');
        }
      });

    // Товары
    Product::select('id', 'updated_at')->orderBy('id')
      ->chunkById(500, function ($chunk) use (&$add) {
        foreach ($chunk as $p) {
          $add("/product/{$p->id}", optional($p->updated_at)->toDateString(), '0.6');
        }
      });

    return $urls;
  }
}
