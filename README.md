# GTFS-RT to JSON
GTFS-RT to JSON は、バスの全国的なオープンデータフォーマットであるGTFSのうち、動的データとしてプロトコルバッファ形式をベースにして提供されるGTFS-RT(GTFS-Realtime)をJSON形式に変換するAPIです。GTFS-RTの配信先URLをクエリとして指定するだけでJSON形式に変換した結果を返します。

## ドキュメント
### エンドポイント
`https://api.tera-chan.com/api/gtfs-rt_to_json/v1.php`
### 必須クエリ
|  Query  |  Description  |
| ---- | ---- |
|  source  |  変換元のGTFS-RT配信URL  |
### リクエスト例
`curl -X GET https://api.tera-chan.com/api/gtfs-rt_to_json/v1.php?source=https://files-skybrain.ekispert.jp/toyama/gtfs-rt/latest/chitetsu/TripUpdates.pb`
### レスポンス例
`[{"id":"TUchitetsu_349000013840","tripUpdate":{"trip":{"tripId":"平日_10時43分_系統112_2_6","scheduleRelationship":0},"vehicle":{"id":"chitetsu_3490","label":"32富山駅・赤十字病院（41号線・富山県美術館 経由）"},"stopTimeUpdate":[{"stopSequence":36,"arrival":{"delay":null,"time":1601432772},"departure":{"delay":null,"time":null}},{"stopSequence":37,"arrival":{"delay":null,"time":1601433192},"departure":{"delay":null,"time":null}},{"stopSequence":38,"arrival":{"delay":null,"time":1601433432},"departure":{"delay":null,"time":null}},{"stopSequence":39,"arrival":{"delay":null,"time":1601433492},"departure":{"delay":null,"time":null}},{"stopSequence":40,"arrival":{"delay":null,"time":1601433552},"departure":{"delay":null,"time":null}},{"stopSequence":41,"arrival":{"delay":null,"time":1601433672},"departure":{"delay":null,"time":null}},{"stopSequence":42,"arrival":{"delay":null,"time":1601433732},"departure":{"delay":null,"time":null}},{"stopSequence":43,"arrival":{"delay":null,"time":1601433852},"departure":{"delay":null,"time":null}}]}}, ...]`
### 注釈
GTFS-RTで提供されているTripUpdate（ルート最新情報）、VehiclePosition（車両位置情報）、Alert（運行情報）すべてをJSONに変換できます。GTFS-RTが配信されている路線の一覧は[こちら](https://tshimada291.sakura.ne.jp/transport/gtfs-list.html)をご覧ください。フィードメッセージなど一部のフィールドは簡略化のため省略されています。GTFS-RTの配信先へ直アクセスしデータを取得後、サーバーで変換し結果を返却しています。過度なアクセスはご遠慮ください。

## ライセンス
本ソフトウェアは、[MITライセンス](./LICENSE)の下に提供されています。
