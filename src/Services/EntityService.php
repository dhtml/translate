<?php

namespace Dhtml\Translate\Services;

class EntityService
{

    public static function toArray($entityName, $model) {
        switch ($entityName) {
            case "page":
                $tdata = ["title" => $model->title, "content" => $model->content];
                break;
            case "discussion":
                $tdata = ["title" => $model->title];
                break;
            case "tag":
                $tdata = ["name" => $model->name, "description" => $model->description];
                break;
            case "post":
                $tdata = ["contentHtml" => $model->content];
                break;
            case "badge":
                $tdata = ["name" => $model->name];
                break;
            case "string":
            case "attribute":
                $tdata = ["original" => $model->original];
                break;
            default:
                trigger_error("Unable to process $entityName");
        }
        return $tdata;
    }

    public static function generateHash($entityName, $model) {
        return md5(json_encode(self::toArray($entityName, $model)));
    }

}
