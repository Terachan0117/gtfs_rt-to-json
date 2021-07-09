<?php

header("Content-Type: application/json; charset=UTF-8");
header("X-Content-Type-Options: nosniff");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

// クエリ'source'を取得
$source = htmlspecialchars($_GET["source"]);

// 変数sourceに何もない場合は終了
if(!$source){
    exit('[]');
}

// Composerで生成したautoloadを読み込む
require_once 'vendor/autoload.php';

//  GTFSリアルタイム言語バインディングを使用(https://github.com/MobilityData/gtfs-realtime-bindings)
use transit_realtime\FeedMessage;

// 変数sourceに指定されているGTFS-RTの配信URLからデータを取得
$data = file_get_contents($source);

// バイナリデータからモデルオブジェクトにパース
$feed = new FeedMessage();
$feed->parse($data);

// 実データ部分のみを取り出し
$entity_list = $feed->getEntityList();

// 最終的に出力するデータを格納するArray
$array = array();

// 各エントリーからデータを取り出し変数arrayに追加していく
foreach($entity_list as $entity)
{
    if($entity->alert){ // Alert
        $informedentity_array = array();
        $informedentity_list = $entity->alert->informed_entity;
        foreach($informedentity_list as $informedentity){
            array_push($informedentity_array, array(
                "agencyId" => $informedentity->agency_id,
                "routeId" => $informedentity->route_id,
                "routeType" => $informedentity->route_type,
                "stopId" => $informedentity->stop_id
            ));
        }
        $headertext_array = array();
        $headertext_list = $entity->alert->header_text->translation;
        foreach($headertext_list as $headertext){
            array_push($headertext_array, array(
                "text" => $headertext->text,
                "language" => $headertext->language
            ));
        }
        $descriptiontext_array = array();
        $descriptiontext_list = $entity->alert->description_text->translation;
        foreach($descriptiontext_list as $descriptiontext){
            array_push($descriptiontext_array, array(
                "text" => $descriptiontext->text,
                "language" => $descriptiontext->language
            ));
        }
        array_push($array, array(
            "id" => $entity->id,
            "alert" => array(
                "informedEntity" => $informedentity_array,
                "headerText" => array(
                    "translation" => $headertext_array
                ),
                "descriptionText" => array(
                    "translation" => $descriptiontext_array
                )
            )
        ));    
    }else if($entity->trip_update){ //TripUpdate
        $stoptimeupdate_array = array();
        $stoptimeupdate_list = $entity->trip_update->stop_time_update;
        if($stoptimeupdate_list){
            foreach($stoptimeupdate_list as $stoptimeupdate){
                array_push($stoptimeupdate_array, array(
                    "stopSequence" => $stoptimeupdate->stop_sequence,
                    "arrival" => array(
                        "delay" => $stoptimeupdate->arrival->delay,
                        "time" => $stoptimeupdate->arrival->time
                    ),
                    "departure" => array(
                        "delay" => $stoptimeupdate->departure->delay,
                        "time" => $stoptimeupdate->departure->time
                    )
                ));
            }
        }
        array_push($array, array(
            "id" => $entity->id,
            "tripUpdate" => array(
                "trip" => array(
                    "tripId" => $entity->trip_update->trip->trip_id,
                    "scheduleRelationship" => $entity->trip_update->trip->schedule_relationship
                ),
                "vehicle" => array(
                    "id" => $entity->trip_update->vehicle->id,
                    "label" => $entity->trip_update->vehicle->label
                ),
                "stopTimeUpdate" => $stoptimeupdate_array
            )
        ));
    }else{ // VehiclePositions
        array_push($array, array(
            "id" => $entity->id,
            "vehicle" => array(
                "trip" => array(
                    "tripId" => $entity->vehicle->trip->trip_id,
                    "scheduleRelationship" => $entity->vehicle->trip->schedule_relationship
                ),
                "vehicle" => array(
                    "id" => $entity->vehicle->vehicle->id,
                    "label" => $entity->vehicle->vehicle->label
                ),
                "position" => array(
                    "latitude" => $entity->vehicle->position->latitude,
                    "longitude" =>$entity->vehicle->position->longitude
                ),
                "currentStopSequence" => $entity->vehicle->current_stop_sequence,
                "currentStatus" => $entity->vehicle->current_status,
                "timestamp" => $entity->vehicle->timestamp,
                "occupancyStatus" => $entity->vehicle->occupancy_status
            )
        ));
    }
}

// 文字のエンコードを変更
$array = mb_convert_encoding($array, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');

// JSONで出力
echo json_encode($array, JSON_UNESCAPED_UNICODE);
?>
