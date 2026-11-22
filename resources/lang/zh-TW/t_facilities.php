<?php

return [
    'space_dock' => [
        'name' => '太空船塢',
        'description' => '可在太空船塢中修復殘骸。',
        'description_long' => '太空船塢提供修復在戰鬥中被摧毀並留下殘骸的船隻的可能性。修復時間最長為 12 小時，但至少需要 30 分鐘才能讓船隻重新投入服役。

由於太空船塢在軌道上漂浮，因此不需要佔用星球土地。',
        'requirements' => '需要造船廠等級 2',
        'field_consumption' => '不消耗星球土地（在軌道上漂浮）',

        'wreck_field_section' => '殘骸場',
        'no_wreck_field' => '此位置無可用殘骸場。',
        'wreck_field_info' => '存在可修復船隻的殘骸場。',
        'ships_available' => '可修復的船隻：{count}',
        'repair_capacity' => '基於太空船塢等級 {level} 的修復容量',

        'start_repair' => '開始修復殘骸場',
        'repair_in_progress' => '修復進行中',
        'repair_completed' => '修復完成',
        'deploy_ships' => '部署已修復的船隻',
        'burn_wreck_field' => '燒毀殘骸場',

        'repair_time' => '預計修復時間：{time}',
        'repair_progress' => '修復進度：{progress}%',
        'completion_time' => '完成時間：{time}',
        'auto_deploy_warning' => '修復完成後 {hours} 小時，船隻將會自動部署（若未手動部署）。',

        'level_effects' => [
            'repair_speed' => '修復速度提升 {bonus}%',
            'capacity_increase' => '最大可修復船隻數量增加',
        ],

        'status' => [
            'no_dock' => '需要太空船塢才能修復殘骸場',
            'level_too_low' => '需要太空船塢等級 1 才能修復殘骸場',
            'no_wreck_field' => '無可用殘骸場',
            'repairing' => '正在修復殘骸場',
            'ready_to_deploy' => '修復完成，船隻準備部署',
        ],
    ],

    'actions' => [
        'build' => '建造',
        'upgrade' => '升級至等級 {level}',
        'downgrade' => '降級至等級 {level}',
        'demolish' => '拆除',
        'cancel' => '取消',
    ],

    'requirements' => [
        'met' => '已滿足需求',
        'not_met' => '未滿足需求',
        'research' => '研究：{requirement}',
        'building' => '建築：{requirement} 等級 {level}',
    ],

    'cost' => [
        'metal' => '金屬：{amount}',
        'crystal' => '晶體：{amount}',
        'deuterium' => '重氫：{amount}',
        'energy' => '能量：{amount}',
        'dark_matter' => '暗物質：{amount}',
        'total' => '總成本：{amount}',
    ],

    'construction_time' => '建造時間：{time}',
    'upgrade_time' => '升級時間：{time}',
];
