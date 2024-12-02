<?php

$campaigns = [
    [
        "campaignname" => "Transsion_Native_Campaign_Test_Nov_30_2024",
        "advertiser" => "TestGP",
        "code" => "1179674AE0080CB1F",
        "price" => 0.1,
        "bidtype" => "CPM",
        "country" => "Bangladesh",
        "device_make" => "No Filter",
        "hs_os" => "Android,iOS,Desktop",
        "native_title" => "GameStar",
        "native_data_value" => "Play Tournament Game",
        "native_data_cta" => "PLAY N WIN",
        "native_img_icon" => "https://example.com/image.jpg",
        "native_tracking_url" => "https://example.com/track"
    ]
];

$bidRequestJson = '{
    "id": "64dd7619-5723-450b-ab12-36b3367fae97",
    "imp": [{
        "id": "1",
        "bidfloor": 0.1,
        "secure": 1,
        "native": {
            "request": "{\"native\":{\"ver\":\"1.2\",\"assets\":[{\"id\":101,\"required\":1,\"title\":{\"len\":150}}]}}"
        },
        "ext": {
            "materialType": 2,
            "launchAppType": ["1", "3"],
            "secondCategories": ["App", "Game"]
        }
    }],
    "app": {
        "id": "ca-app-pub-2476175026271293~2052525764",
        "name": "ExampleApp",
        "storeurl": "http://example.com/app.apk",
        "ext": {
            "mediaType": 0
        }
    },
    "device": {
        "ua": "Mozilla/5.0 (Linux; Android 11; en-us; TECNO KF8 Build/RP1A.200720.011)",
        "ip": "36.255.82.232",
        "geo": {
            "country": "BGD"
        },
        "os": "ANDROID",
        "w": 720,
        "h": 1600,
        "devicetype": 1
    },
    "user": {
        "id": "1bbbfc6b-7342-47a9-8648-ab8dca628bd2"
    }
}';

$bidRequest = json_decode($bidRequestJson, true);

if (!$bidRequest) {
    die('Invalid Bid Request JSON');
}

$bidResponse = [
    "id" => $bidRequest['id'],
    "bidid" => uniqid(),
    "seatbid" => []
];

foreach ($campaigns as $campaign) {
    if ($bidRequest['device']['geo']['country'] == 'BGD' && $campaign['country'] == 'Bangladesh') {
        if (strpos($campaign['device_make'], 'No Filter') !== false) {
            if ($bidRequest['imp'][0]['bidfloor'] <= $campaign['price']) {
                $seatBid = [
                    "bid" => [
                        [
                            "price" => $campaign['price'],
                            "adm" => json_encode([
                                "native" => [
                                    "assets" => [
                                        ["id" => 101, "title" => ["text" => $campaign['native_title']]],
                                        ["img" => ["url" => $campaign['native_img_icon'], "w" => 100, "h" => 100, "id" => 104]],
                                        ["data" => ["value" => $campaign['native_data_value'], "type" => 2], "id" => 102],
                                        ["data" => ["value" => $campaign['native_data_cta'], "type" => 12], "id" => 103]
                                    ],
                                    "imptrackers" => ["https://example.com/imp?price=" . $campaign['price']],
                                    "link" => [
                                        "url" => $campaign['native_tracking_url'],
                                        "fallback" => "http://example.com/landPage"
                                    ],
                                    "ver" => "1.2"
                                ]
                            ]),
                            "id" => uniqid(),
                            "cid" => uniqid(),
                            "impid" => $bidRequest['imp'][0]['id'],
                            "crid" => uniqid(),
                            "bundle" => "ADX.Liru.Com"
                        ]
                    ],
                    "seat" => "1003"
                ];

                $bidResponse['seatbid'][] = $seatBid;
            }
        }
    }
}

if (empty($bidResponse['seatbid'])) {
    $bidResponse['error'] = "No suitable campaign found.";
}

header('Content-Type: application/json');
echo json_encode($bidResponse, JSON_PRETTY_PRINT);
?>
