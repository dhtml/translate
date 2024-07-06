<?php

namespace Dhtml\Translate\Services;

use Dhtml\Translate\Badge;
use Dhtml\Translate\Discussion;
use Dhtml\Translate\Page;
use Dhtml\Translate\Post;
use Dhtml\Translate\Tag;

class StatsService
{
    public function __construct()
    {
    }

    protected function renderEntityStat($entityName) {
        extract($this->stats[$entityName]);

        $total = number_format($total);
        $translated = number_format($translated);
        $pending = number_format($pending);
        $outdated = number_format($outdated);
        $failed = number_format($failed);
        $data="
        <tr>
        <td>$name</td>
        <td><b>$total</b></td>
        <td><b>$translated</b></td>
        <td><b>$pending</b></td>
        <td><b>$outdated</b></td>
        <td><b>$failed</b></td>
        </tr>
        ";
        return $data;
    }
    public function display()
    {
        $this->stats = [
            "discussions" => [
                'name' => "Discussions",
                'total' => Discussion::count(),
                'translated' => Discussion::where('_translated', 1)->where('_failed', 0)->count(),
                'pending' => Discussion::where('_translated', 0)->where('_failed', 0)->count(),
                'outdated' => Discussion::where('_translated', 1)->where("_outdated", 1)->where('_failed', 0)->count(),
                'failed' => Discussion::where('_failed', 1)->count(),
            ],
            "posts" => [
                'name' => "Posts",
                'total' => Post::where('type','comment')->count(),
                'translated' => Post::where('type','comment')->where('_translated', 1)->where('_failed', 0)->count(),
                'pending' => Post::where('type','comment')->where('_translated', 0)->where('_failed', 0)->count(),
                'outdated' => Post::where('type','comment')->where('_translated', 1)->where("_outdated", 1)->where('_failed', 0)->count(),
                'failed' => Post::where('_failed', 1)->count(),
            ],
            "tags" => [
                'name' => "Tags",
                'total' => Tag::count(),
                'translated' => Tag::where('_translated', 1)->where('_failed', 0)->count(),
                'pending' => Tag::where('_translated', 0)->where('_failed', 0)->count(),
                'outdated' => Tag::where('_translated', 1)->where("_outdated", 1)->where('_failed', 0)->count(),
                'failed' => Tag::where('_failed', 1)->count(),
            ],
            "badges" => [
                'name' => "Badges",
                'total' => Badge::count(),
                'translated' => Badge::where('_translated', 1)->where('_failed', 0)->count(),
                'pending' => Badge::where('_translated', 0)->where('_failed', 0)->count(),
                'outdated' => Badge::where('_translated', 1)->where("_outdated", 1)->where('_failed', 0)->count(),
                'failed' => Badge::where('_failed', 1)->count(),
            ],
            "pages" => [
                'name' => "Pages",
                'total' => Page::count(),
                'translated' => Page::where('_translated', 1)->where('_failed', 0)->count(),
                'pending' => Page::where('_translated', 0)->where('_failed', 0)->count(),
                'outdated' => Page::where('_translated', 1)->where("_outdated", 1)->where('_failed', 0)->count(),
                'failed' => Page::where('_failed', 1)->count(),
            ],
        ];

        $result = "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv='refresh' content='60'>
    <title>Translations</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        .container {
            text-align: center;
        }
        h1 {
            font-size: 2em;
            margin: 0.5em 0;
        }
        td {padding:5px;}
    </style>
</head>
<body onclick='location.reload();'>
    <div class='container'>
<h1>Translations:</h1>
<table border='1' cellpadding='4' cellspacing='4'>
    <tr>
        <th>Name</th>
        <th>Total</th>
        <th>Translated</th>
        <th>Pending</th>
        <th>Outdated</th>
        <th>Failed</th>
    </tr>
    {$this->renderEntityStat('posts')}
    {$this->renderEntityStat('discussions')}
    {$this->renderEntityStat('tags')}
    {$this->renderEntityStat('badges')}
    {$this->renderEntityStat('pages')}
    </table>
    </div>
</body>
</html>
";

        return $result;
    }
}
