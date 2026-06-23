<?php

return [
    'wreck_field' => '殘骸場',
    'wreck_field_formed' => '在座標 {coordinates} 形成了殘骸場',
    'wreck_field_expired' => '殘骸場已到期',
    'wreck_field_burned' => '殘骸場已燒毀',

    'formation_conditions' => '當至少損失 {min_resources} 資源且至少 {min_percentage}% 的防守艦隊被摧毀時，就會形成殘骸場。',
    'resources_lost' => '損失資源：{amount}',
    'fleet_percentage' => '艦隊摧毀：{percentage}%',

    'repair_time' => '修復時間',
    'repair_progress' => '修復進度',
    'repair_completed' => '修復完成',
    'repairs_underway' => '修復進行中',
    'repair_duration_min' => '最短修復時間：{minutes} 分鐘',
    'repair_duration_max' => '最長修復時間：{hours} 小時',
    'repair_speed_bonus' => '太空船塢等級 {level} 提供 {bonus}% 修復速度加成',

    'ships_in_wreck_field' => '殘骸場中的船隻',
    'ship_type' => '船隻類型',
    'quantity' => '數量',
    'repairable' => '可修復',
    'total_ships' => '船隻總數：{count}',

    'start_repairs' => '開始修復',
    'complete_repairs' => '完成修復',
    'burn_wreck_field' => '燒毀殘骸場',
    'cancel_repairs' => '取消修復',

    'repair_started' => '修復已開始。完成時間：{time}',
    'repairs_completed' => '所有修復已完成。船隻準備部署。',
    'wreck_field_burned_success' => '殘骸場已成功燒毀。',
    'cannot_repair' => '此殘骸場無法修復。',
    'cannot_burn' => '修復進行中無法燒毀此殘骸場。',

    'wreck_field_icon' => 'WF',
    'wreck_field_tooltip' => '殘骸場（剩餘 {time_remaining}）',
    'click_to_repair' => '點擊前往太空船塢進行修復',
    'no_wreck_field' => '無殘骸場',

    'space_dock_required' => '需要太空船塢等級 1 才能修復殘骸場。',
    'space_dock_level' => '太空船塢等級：{level}',
    'upgrade_space_dock' => '升級太空船塢以修復更多船隻',
    'repair_capacity_reached' => '已達最大修復容量。升級太空船塢以增加容量。',

    'wreck_field_section' => '殘骸場資訊',
    'ships_available_for_repair' => '可修復的船隻：{count}',
    'wreck_field_resources' => '殘骸場約有價值 {value} 資源的船隻。',

    'settings_title' => '殘骸場設定',
    'enabled_description' => '殘骸場允許透過太空船塢建築回收被摧毀的船隻。當摧毀滿足特定條件時，可以修復船隻。',
    'percentage_setting' => '殘骸場中的摧毀船隻：',
    'min_resources_setting' => '殘骸場的最低摧毀資源：',
    'min_fleet_percentage_setting' => '最低艦隊摧毀百分比：',
    'lifetime_setting' => '殘骸場持續時間（小時）：',
    'repair_max_time_setting' => '最長修復時間（小時）：',
    'repair_min_time_setting' => '最短修復時間（分鐘）：',

    'error_no_wreck_field' => '在此位置未找到殘骸場。',
    'error_not_owner' => '您不擁有此殘骸場。',
    'error_already_repairing' => '修復正在進行中。',
    'error_no_ships' => '沒有可修復的船隻。',
    'error_space_dock_required' => '需要太空船塢等級 1 才能修復殘骸場。',
    'error_cannot_collect_late_added' => '在進行修復期間添加的船隻無法手動收集。您必須等待所有修復自動完成。',
    'warning_auto_return' => '修復後的船隻將在修復完成後 {hours} 小時自動返回服役。',

    'time_remaining' => '剩餘 {hours}小時 {minutes}分鐘',
    'expires_soon' => '即將到期',
    'repair_time_remaining' => '修復完成：{time}',

    'status_active' => '啟用',
    'status_repairing' => '修復中',
    'status_completed' => '已完成',
    'status_burned' => '已燒毀',
    'status_expired' => '已到期',

    'repairs_started' => '修復已成功開始',
    'all_ships_deployed' => '所有船隻已重新投入服役',
    'no_ships_ready' => '沒有船隻準備好收集',
    'repairs_not_started' => '修復尚未開始',
];
