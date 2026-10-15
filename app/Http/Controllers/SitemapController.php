<?php

namespace App\Http\Controllers;

use App\Models\Ad;

class SitemapController extends Controller
{
    public function __invoke()
    {
        // id et titre servent à fabriquer l'URL « id-titre » de chaque annonce
        $ads = Ad::select('id', 'title', 'updated_at')
            ->latest('updated_at')
            ->limit(5000)
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .view('sitemap', compact('ads'))->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
