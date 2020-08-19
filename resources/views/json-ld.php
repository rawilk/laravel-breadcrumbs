<?php

$json = [
    '@context' => 'http://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [],
];

foreach ($breadcrumbs as $index => $breadcrumb) {
    $json['itemListElement'][] = [
        '@type' => 'ListItem',
        'position' => $index + 1,
        'item' => [
            '@id' => $breadcrumb->url ?: request()->fullUrl(),
            'name' => $breadcrumb->title,
            'image' => $breadcrumb->image ?? null,
        ],
    ];
}

?>
<script type="application/ld+json"><?php echo json_encode($json); ?></script>
