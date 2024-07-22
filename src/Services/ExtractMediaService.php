<?php

namespace Dhtml\Translate\Services;
use Flarum\Foundation\Paths;
use Symfony\Component\DomCrawler\Crawler;

class ExtractMediaService
{
    public static function extractFirstMedia($html)
    {
        $crawler = new Crawler($html);
        $result = [];

        // Find the first YouTube iframe
        $youtubeNode = $crawler->filter('iframe[src*="youtube.com/embed"]')->first();
        if ($youtubeNode->count()) {
            $result[] = ['type'=>'youtube', 'src' => $youtubeNode->attr('src')];
        }

        // Find the first video element
        $videoNode = $crawler->filter('video')->first();
        if ($videoNode->count()) {
            $result[] = ['type'=>'video',  'src'=> $videoNode->attr('src')];
        }

        // Find the first image element inside a link
        $imageNode = $crawler->filter('a[href*="imgur.com"] img')->first();
        if ($imageNode->count()) {
            $result[] = ['type'=>'image', 'src'=> $imageNode->attr('src')];
        }

        /*
        if(empty($result)) {
            $result[] = ['type'=>'image', 'src' =>"https://static.africoders.com/img/post-image.png"];
        }
        */

        return $result;
    }

    public static function toHTML(array $postMedia)
    {
        if(empty($postMedia) || !isset($postMedia[0])) {return null;}

        $item = $postMedia[0];

        $type = $item['type'];
        $src = $item['src'];

        $result = "";

        switch ($type) {
            case "image":
                $result = "<img class=\"preview-first-post-image\" src=\"$src\" alt=\"\">";
                break;
            case "video":
                $result = "<video class=\"preview-first-post-video\" controls src=\"$src\"></video>";
                break;
            case "youtube":
                $result = "<iframe  class=\"preview-first-post-youtube\" width=\"560\" height=\"315\" src=\"$src\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" referrerpolicy=\"strict-origin-when-cross-origin\" allowfullscreen></iframe>";
                break;
        }

        return $result;
    }

    public static function logInfo($content)
    {
        $paths = resolve(Paths::class);
        $logPath = $paths->storage . (DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'dhtml-extract-media-service.log');
        $content = var_export($content, true);
        file_put_contents($logPath, $content, FILE_APPEND);
    }
}
