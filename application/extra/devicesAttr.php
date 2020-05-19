<?php

$MineName = 'mine';
$AligenieName = 'aligenie';
$MiotName = 'miot';

return [
    //如果增加云平台请在此也要添加
    'MineName' => $MineName,
    'AligenieName' => $AligenieName,
    'MiotName' => $MiotName,

    'light' => [
        'ch' => '灯具',
        'en' => 'light',
        'htmlControl' => 'control2light',
        $AligenieName => [
            'type' => 'light',
            //支持查询或设置的指令
            //可查询的属性
            'properties_query' => [
                0 => 'powerstate', //电源
                1 => 'onlinestate', //	设备在线状态
                2 => 'remotestatus', //	设备远程状态
                3 => 'brightness', //亮度
            ],
            //支持控制属性
            'properties_control' => [
                0 => 'powerstate', //电源
                1 => 'brightness', //亮度
            ],
            //支持控制属性对应的动作
            'properties_control_action' => [
                "TurnOn",
                "TurnOff",
                "SetBrightness",
                "AdjustBrightness",
                "Query",
            ],
        ],
        $MiotName => [
            'name' => 'light',
        ]
    ],
    'rgbLight' => [
        'ch' => '七彩冷暖灯',
        'en' => 'rgbLight',
        'htmlControl' => 'control2rgb',
        //各大平台转私有协议
        $MineName => [
            $AligenieName => [
                'type' => 'light',
                //可查询的属性
                'properties_query' => [
                    'powerstate' => 'powerstate', //电源
                    'onlinestate' => 'onlinestate', //	设备在线状态
                    'remotestatus' => 'remotestatus', //	设备远程状态
                    'colorTemperature' => 'colorTemperature', //色温
                    'brightness' => 'brightness', //亮度
                    'mode' => 'mode', //模式
                    'color' => 'color', //颜色
                ],
                //支持控制属性
                'properties_control' => [
                    'powerstate' => 'powerstate', //电源
                    'colorTemperature' => 'colorTemperature', //色温
                    'brightness' => 'brightness', //亮度
                    'mode' => 'mode', //模式
                    'color' => 'color', //颜色
                ],
                //支持控制属性对应的动作
                'properties_control_action' => [
                    "TurnOn" => "TurnOn",
                    "TurnOff" => "TurnOff",
                    "SetBrightness" => "SetBrightness",
                    "AdjustBrightness" => "AdjustBrightness",
                    "SetTemperature" => "SetTemperature",
                    "Query" => "Query",
                    'SetColor' => 'SetColor',
                ],
                //支持控制属性的数值规范
                'properties_control_value_range' => [
                    'powerstate' => [
                        0 => ('eq:off'),
                        1 => ('eq:on'),
                    ],
                    'colorTemperature' => [
                        0 => ('eq:max'),
                        1 => ('eq:min'),
                        2 => ('between:2700,6500'),
                    ], //色温
                    'brightness' => [
                        0 => ('eq:max'),
                        1 => ('eq:min'),
                        2 => ('between:0,100'),
                    ], //亮度
                    'mode' => [
                        0 => ('eq:reading'),
                        1 => ('eq:movie'),
                        2 => ('eq:sleep'),
                        3 => ('eq:live'),
                        4 => ('eq:night'),
                        5 => ('eq:nightLight'),
                    ], //模式
                    'color' => [
                        0 => ('eq:Red'),
                        1 => ('eq:Yellow'),
                        2 => ('eq:Blue'),
                        3 => ('eq:Green'),
                        4 => ('eq:White'),
                        5 => ('eq:Cyan'),
                        6 => ('eq:Purple'), //青色
                        7 => ('eq:Orange'), //紫色
                    ], //颜色
                ],
            ],
            $MiotName => [
                'type' => 'light',
                //可查询的属性
                'properties_query' => [
                    'siid1' => [
                        'piid1' => 'xClouds', //manufacturer
                        'piid2' => 'model', //model
                        'piid3' => 'serial-number',  ///serial-number
                        'piid4' => 'firmware-revision', //firmware-revision
                    ],
                    'siid2' => [
                        'piid1' => 'powerstate', //电源
                        'piid2' => 'brightness', //亮度
                        'piid3' => 'colorTemperature',  ///色温
                        'piid4' => 'color', //颜色
                        'piid5' => 'mode', //模式
                    ],
                ],
                //支持控制属性的数值规范
                'properties_control_value_range' => [
                    'powerstate' => [
                        0 => ('boolean'),
                    ],
                    'colorTemperature' => [
                        0 => ('eq:max'),
                        1 => ('eq:min'),
                        2 => ('between:1000,10000'),
                    ], //色温
                    'brightness' => [
                        0 => ('eq:max'),
                        1 => ('eq:min'),
                        2 => ('between:0,100'),
                    ], //亮度
                    'mode' => [
                        0 => ('eq:0'),
                        1 => ('eq:1'),
                        2 => ('eq:2'),
                        3 => ('eq:3'),
                        4 => ('eq:4'),
                        5 => ('eq:5'),
                    ], //模式
                    'color' => [
                        0 => ('eq:max'),
                        1 => ('eq:min'),
                        2 => ('between:0,16777215'),
                    ], //颜色
                ],
                //控制动作规范
                'properties_control' => [
                    'siid1' => [
                        'piid1' => 'xClouds', //manufacturer
                        'piid2' => 'model', //model
                        'piid3' => 'serial-number',  ///serial-number
                        'piid4' => 'firmware-revision', //firmware-revision
                    ],
                    'siid2' => [
                        'piid1' => 'powerstate', //电源
                        'piid2' => 'SetBrightness', //亮度
                        'piid3' => 'SetColorTemperature',  ///色温
                        'piid4' => 'SetColor', //颜色
                        'piid5' => 'SetMode', //模式
                    ],
                ]
            ],
        ],
        //私有协议转天猫精灵协议
        $AligenieName => [
            'type' => 'light',
            //可查询的属性
            'properties_query' => [
                0 => 'powerstate', //电源
                1 => 'onlinestate', //	设备在线状态
                2 => 'remotestatus', //	设备远程状态
                3 => 'colorTemperature', //色温
                4 => 'brightness', //亮度
                5 => 'mode', //模式
                6 => 'color', //颜色
            ],
            //支持控制属性
            'properties_control' => [
                0 => 'powerstate', //电源
                1 => 'colorTemperature', //色温
                2 => 'brightness', //亮度
                3 => 'mode', //模式
                4 => 'color', //颜色
            ],
            //支持控制属性对应的动作
            'properties_control_action' => [
                "TurnOn",
                "TurnOff",
                "SetBrightness",
                "AdjustBrightness",
                "SetTemperature",
                "SetColorTemperature",
                "Query",
                'SetColor',
                'SetMode',
            ]
        ],
        //私有协议转米家协议
        $MiotName => [
            'type' => 'light',
            'model' => 'urn:miot-spec-v2:device:light:0000A001:180-light:1:0000C801',
            //可查询的属性
            'properties_query' => [
                0 => 'powerstate', //电源
                1 => 'onlinestate', //	设备在线状态
                2 => 'remotestatus', //	设备远程状态
                3 => 'colorTemperature', //色温
                4 => 'brightness', //亮度
                5 => 'mode', //模式
                6 => 'color', //颜色
            ],
            //支持控制属性 第一个是所在的技能 第二个是属性
            'properties_control' => [
                'powerstate' => ['siid2', 'piid1'], //电源
                'brightness' => ['siid2', 'piid2'], //亮度
                'colorTemperature' => ['siid2', 'piid3'],  ///色温
                'color' => ['siid2', 'piid4'], //颜色
                'mode' => ['siid2', 'piid5'], //模式
            ],
            //支持控制属性的数值转化
            'properties_control_value_range' => [
                'powerstate' => [
                    'off' => false,
                    "on" => true,
                ],
                'colorTemperature' => [
                    0 => ('eq:max'),
                    1 => ('eq:min'),
                    2 => ('between:2700,6500'),
                ], //色温
                'brightness' => [
                    0 => ('eq:max'),
                    1 => ('eq:min'),
                    2 => ('between:0,100'),
                ], //亮度
                'mode' => [
                    0 => ('eq:reading'),
                    1 => ('eq:movie'),
                    2 => ('eq:sleep'),
                    3 => ('eq:live'),
                    4 => ('eq:night'),
                    5 => ('eq:nightLight'),
                ], //模式
                'color' => [
                    0 => ('eq:Red'),
                    1 => ('eq:Yellow'),
                    2 => ('eq:Blue'),
                    3 => ('eq:Green'),
                    4 => ('eq:White'),
                    5 => ('eq:Cyan'),
                    6 => ('eq:Purple'), //青色
                    7 => ('eq:Orange'), //紫色
                ], //颜色
            ]
        ],
    ],
    'rgb' => [
        'ch' => '七彩灯',
        'en' => 'rgb',
        'htmlControl' => 'control2rgb',
        //各大平台转私有协议
        $MineName => [
            $AligenieName => [
                'type' => 'light',
                //可查询的属性
                'properties_query' => [
                    'powerstate' => 'powerstate', //电源
                    'onlinestate' => 'onlinestate', //	设备在线状态
                    'remotestatus' => 'remotestatus', //	设备远程状态
                    'colorTemperature' => 'colorTemperature', //色温
                    'brightness' => 'brightness', //亮度
                    'mode' => 'mode', //模式
                    'color' => 'color', //颜色
                ],
                //支持控制属性
                'properties_control' => [
                    'powerstate' => 'powerstate', //电源
                    'colorTemperature' => 'colorTemperature', //色温
                    'brightness' => 'brightness', //亮度
                    'mode' => 'mode', //模式
                    'color' => 'color', //颜色
                ],
                //支持控制属性对应的动作
                'properties_control_action' => [
                    "TurnOn" => "TurnOn",
                    "TurnOff" => "TurnOff",
                    "SetBrightness" => "SetBrightness",
                    "AdjustBrightness" => "AdjustBrightness",
                    "SetTemperature" => "SetTemperature",
                    "Query" => "Query",
                    'SetColor' => 'SetColor',
                ],
                //支持控制属性的数值规范
                'properties_control_value_range' => [
                    'powerstate' => [
                        0 => ('eq:off'),
                        1 => ('eq:on'),
                    ],
                    'colorTemperature' => [
                        0 => ('eq:max'),
                        1 => ('eq:min'),
                        2 => ('between:2700,6500'),
                    ], //色温
                    'brightness' => [
                        0 => ('eq:max'),
                        1 => ('eq:min'),
                        2 => ('between:0,100'),
                    ], //亮度
                    'mode' => [
                        0 => ('eq:reading'),
                        1 => ('eq:movie'),
                        2 => ('eq:sleep'),
                        3 => ('eq:live'),
                        4 => ('eq:night'),
                        5 => ('eq:nightLight'),
                    ], //模式
                    'color' => [
                        0 => ('eq:Red'),
                        1 => ('eq:Yellow'),
                        2 => ('eq:Blue'),
                        3 => ('eq:Green'),
                        4 => ('eq:White'),
                        5 => ('eq:Cyan'),
                        6 => ('eq:Purple'), //青色
                        7 => ('eq:Orange'), //紫色
                    ], //颜色
                ],
            ],
            $MiotName => [
                'type' => 'light',
                //可查询的属性
                'properties_query' => [
                    'siid1' => [
                        'piid1' => 'xClouds', //manufacturer
                        'piid2' => 'model', //model
                        'piid3' => 'serial-number',  ///serial-number
                        'piid4' => 'firmware-revision', //firmware-revision
                    ],
                    'siid2' => [
                        'piid1' => 'powerstate', //电源
                        'piid2' => 'brightness', //亮度
                        'piid3' => 'colorTemperature',  ///色温
                        'piid4' => 'color', //颜色
                        'piid5' => 'mode', //模式
                    ],
                ],
                //支持控制属性的数值规范
                'properties_control_value_range' => [
                    'powerstate' => [
                        0 => ('boolean'),
                    ],
                    'colorTemperature' => [
                        0 => ('eq:max'),
                        1 => ('eq:min'),
                        2 => ('between:1000,10000'),
                    ], //色温
                    'brightness' => [
                        0 => ('eq:max'),
                        1 => ('eq:min'),
                        2 => ('between:0,100'),
                    ], //亮度
                    'mode' => [
                        0 => ('eq:0'),
                        1 => ('eq:1'),
                        2 => ('eq:2'),
                        3 => ('eq:3'),
                        4 => ('eq:4'),
                        5 => ('eq:5'),
                    ], //模式
                    'color' => [
                        0 => ('eq:max'),
                        1 => ('eq:min'),
                        2 => ('between:0,16777215'),
                    ], //颜色
                ],
                //控制动作规范
                'properties_control' => [
                    'siid1' => [
                        'piid1' => 'xClouds', //manufacturer
                        'piid2' => 'model', //model
                        'piid3' => 'serial-number',  ///serial-number
                        'piid4' => 'firmware-revision', //firmware-revision
                    ],
                    'siid2' => [
                        'piid1' => 'powerstate', //电源
                        'piid2' => 'SetBrightness', //亮度
                        'piid3' => 'SetColorTemperature',  ///色温
                        'piid4' => 'SetColor', //颜色
                        'piid5' => 'SetMode', //模式
                    ],
                ],
                //控制数值的转换
                'properties_control_value_convert' => [
                    'color' => [
                        16711680 => 'Red',//红色：
                        65280 => 'Green',//绿色：65280
                        255 => 'Blue',//蓝色：255
                        //16753920 => 'Orange',//橙色：16753920
                        //65535 => 'Red',//青色：65535
                        10494192 => 'Red',//紫色：10494192
                        16777215 => 'White',//白色：16777215
                        16776960 => 'Yellow',//黄色
                    ]
                ]
            ],
        ],
        //私有协议转天猫精灵协议
        $AligenieName => [
            'type' => 'light',
            //可查询的属性
            'properties_query' => [
                0 => 'powerstate', //电源
                1 => 'onlinestate', //	设备在线状态
                2 => 'remotestatus', //	设备远程状态
                3 => 'colorTemperature', //色温
                4 => 'brightness', //亮度
                5 => 'mode', //模式
                6 => 'color', //颜色
            ],
            //支持控制属性
            'properties_control' => [
                0 => 'powerstate', //电源
                1 => 'colorTemperature', //色温
                2 => 'brightness', //亮度
                3 => 'mode', //模式
                4 => 'color', //颜色
            ],
            //支持控制属性对应的动作
            'properties_control_action' => [
                "TurnOn",
                "TurnOff",
                "SetBrightness",
                "AdjustBrightness",
                "SetTemperature",
                "SetColorTemperature",
                "Query",
                'SetColor',
                'SetMode',
            ]
        ],
        //私有协议转米家协议
        $MiotName => [
            'type' => 'light',
            'model' => 'urn:miot-spec-v2:device:light:0000A001:180-xuhong:1:0000C814',
            //可查询的属性
            'properties_query' => [
                0 => 'powerstate', //电源
                1 => 'onlinestate', //	设备在线状态
                2 => 'remotestatus', //	设备远程状态
                3 => 'colorTemperature', //色温
                4 => 'brightness', //亮度
                5 => 'mode', //模式
                6 => 'color', //颜色
            ],
            //支持控制属性 第一个是所在的技能 第二个是属性
            'properties_control' => [
                'powerstate' => ['siid2', 'piid1'], //电源
                'brightness' => ['siid2', 'piid2'], //亮度
                'colorTemperature' => ['siid2', 'piid3'],  ///色温
                'color' => ['siid2', 'piid4'], //颜色
                'mode' => ['siid2', 'piid5'], //模式
            ],
            //支持控制属性的数值转化
            'properties_control_value_range' => [
                'powerstate' => [
                    'off' => false,
                    "on" => true,
                ],
                'colorTemperature' => [
                    0 => ('eq:max'),
                    1 => ('eq:min'),
                    2 => ('between:2700,6500'),
                ], //色温
                'brightness' => [
                    0 => ('eq:max'),
                    1 => ('eq:min'),
                    2 => ('between:0,100'),
                ], //亮度
                'mode' => [
                    0 => ('eq:reading'),
                    1 => ('eq:movie'),
                    2 => ('eq:sleep'),
                    3 => ('eq:live'),
                    4 => ('eq:night'),
                    5 => ('eq:nightLight'),
                ], //模式
                'color' => [
                    0 => ('eq:Red'),
                    1 => ('eq:Yellow'),
                    2 => ('eq:Blue'),
                    3 => ('eq:Green'),
                    4 => ('eq:White'),
                    5 => ('eq:Cyan'),
                    6 => ('eq:Purple'), //青色
                    7 => ('eq:Orange'), //紫色
                ], //颜色
            ]
        ],
    ]

];