@props(['seo' => null])

@php
    $seoModel = $seo;
    if (!$seoModel && isset($seoData) && $seoData instanceof \App\Support\Seo) {
        $seoModel = $seoData;
    }
    if (!$seoModel) {
        $seoModel = \App\Support\Seo::make();
    }

    $yieldTitle = trim($__env->yieldContent('title'));
    $pageTitle = filled($yieldTitle) ? $yieldTitle : $seoModel->getTitle();

    $yieldDesc = trim($__env->yieldContent('meta_description'));
    $pageDesc = filled($yieldDesc) ? $yieldDesc : $seoModel->getDescription();

    $yieldCanonical = trim($__env->yieldContent('canonical_url'));
    $pageCanonical = filled($yieldCanonical) ? $yieldCanonical : $seoModel->getCanonical();

    $yieldRobots = trim($__env->yieldContent('meta_robots'));
    $pageRobots = filled($yieldRobots) ? $yieldRobots : $seoModel->getRobots();

    $yieldOgType = trim($__env->yieldContent('og_type'));
    $ogType = filled($yieldOgType) ? $yieldOgType : $seoModel->getOgType();

    $yieldOgImage = trim($__env->yieldContent('og_image'));
    $ogImage = filled($yieldOgImage) ? $yieldOgImage : $seoModel->getOgImage();

    $siteName = $seoModel->getSiteName();
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDesc }}">
<link rel="canonical" href="{{ $pageCanonical }}">
<meta name="robots" content="{{ $pageRobots }}">

<!-- Open Graph / Meta Social -->
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDesc }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:url" content="{{ $pageCanonical }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="id_ID">
<meta property="og:image" content="{{ $ogImage }}">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $pageDesc }}">
<meta name="twitter:image" content="{{ $ogImage }}">

<!-- Favicon & Touch Icons -->
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo-sam.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/logo-sam.png') }}">
<link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/logo-sam.png') }}">
<link rel="apple-touch-icon" href="{{ asset('images/logo-sam.png') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">
<meta name="theme-color" content="#0f2b48">
