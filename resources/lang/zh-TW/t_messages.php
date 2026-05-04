<?php

return [
    'welcome_message' => [
        'from' => 'OGameX',
        'subject' => '歡迎來到 OGameX！',
        'body' => '皇帝 :player，您好！

恭喜您開始了輝煌的職業生涯。我將在此引導您完成最初的幾個步驟。

在左側，您可以看到允許您監督和管理您的銀河帝國的選單。

您已經看到了總覽。資源和設施允許您建造建築物來幫助您擴張帝國。首先建造一個太陽能發電廠來為您的礦井收集能量。

然後擴建您的金屬礦和晶體礦以生產重要資源。或者，您也可以自行四處看看。我相信您很快就會感到得心應手。

您可以在這裡找到更多幫助、提示和策略：

Discord 聊天：Discord 伺服器
論壇：OGameX 論壇
支援：遊戲支援

您只能在論壇中找到當前公告和遊戲變更。

現在您已為未來做好準備。祝您好運！

此訊息將在 7 天後刪除。',
    ],

    'return_of_fleet_with_resources' => [
        'from' => '艦隊指揮部',
        'subject' => '艦隊返回',
        'body' => '您的艦隊正在從 :from 返回 :to，並運送了貨物：

金屬：:metal
晶體：:crystal
重氫：:deuterium',
    ],

    'return_of_fleet' => [
        'from' => '艦隊指揮部',
        'subject' => '艦隊返回',
        'body' => '您的艦隊正在從 :from 返回 :to。

艦隊沒有運送貨物。',
    ],

    'fleet_deployment_with_resources' => [
        'from' => '艦隊指揮部',
        'subject' => '艦隊返回',
        'body' => '您的一支來自 :from 的艦隊已到達 :to 並運送了貨物：

金屬：:metal
晶體：:crystal
重氫：:deuterium',
    ],

    'fleet_deployment' => [
        'from' => '艦隊指揮部',
        'subject' => '艦隊返回',
        'body' => '您的一支來自 :from 的艦隊已到達 :to。艦隊沒有運送貨物。',
    ],

    'transport_arrived' => [
        'from' => '艦隊指揮部',
        'subject' => '到達星球',
        'body' => '您來自 :from 的艦隊到達 :to 並運送了貨物：
金屬：:metal 晶體：:crystal 重氫：:deuterium',
    ],

    'transport_received' => [
        'from' => '艦隊指揮部',
        'subject' => '來襲艦隊',
        'body' => '一支來自 :from 的來襲艦隊已到達您的星球 :to 並運送了貨物：
金屬：:metal 晶體：:crystal 重氫：:deuterium',
    ],

    'acs_defend_arrival_host' => [
        'from' => '太空監控',
        'subject' => '艦隊正在停靠',
        'body' => '一支艦隊已到達 :to。',
    ],

    'acs_defend_arrival_sender' => [
        'from' => '艦隊指揮部',
        'subject' => '艦隊正在停靠',
        'body' => '一支艦隊已到達 :to。',
    ],

    'colony_established' => [
        'from' => '艦隊指揮部',
        'subject' => '殖民報告',
        'body' => '艦隊已到達指定座標 :coordinates，在那裡發現了一個新星球，並立即開始開發。',
    ],

    'colony_establish_fail_astrophysics' => [
        'from' => '殖民者',
        'subject' => '殖民報告',
        'body' => '艦隊已到達指定座標 :coordinates，並確認該星球適合殖民。然而在開始開發後不久，殖民者意識到他們的天體物理學知識不足以完成新星球的殖民。',
    ],

    'espionage_report' => [
        'from' => '艦隊指揮部',
        'subject' => '來自 :planet 的間諜報告',
    ],

    'espionage_detected' => [
        'from' => '艦隊指揮部',
        'subject' => '來自星球 :planet 的間諜報告',
        'body' => '在您的星球附近發現了一支來自星球 :planet（:attacker_name）的外國艦隊
:defender
反間諜機率：:chance%',
    ],

    'battle_report' => [
        'from' => '艦隊指揮部',
        'subject' => '戰鬥報告 :planet',
    ],

    'fleet_lost_contact' => [
        'from' => '艦隊指揮部',
        'subject' => '與攻擊艦隊的聯繫已中斷。:coordinates',
        'body' => '（這意味著它在第一回合被摧毀了。）',
    ],

    'debris_field_harvest' => [
        'from' => '艦隊',
        'subject' => '來自 :coordinates DF 的收穫報告',
        'body' => '您的 :ship_name（:ship_amount 艘船）總儲存容量為 :storage_capacity。在目標 :to 太空中漂浮著 :metal 金屬、:crystal 晶體和 :deuterium 重氫。您收穫了 :harvested_metal 金屬、:harvested_crystal 晶體和 :harvested_deuterium 重氫。',
    ],

    'expedition_resources_captured' => ':resource_type :resource_amount 已被捕獲。',
    'expedition_dark_matter_captured' => '（:dark_matter_amount 暗物質）',
    'expedition_units_captured' => '以下船隻現在是艦隊的一部分：',

    'expedition_unexplored_statement' => '來自通訊官日誌的記錄：似乎宇宙的這一部分尚未被探索。',

    'expedition_failed' => [
        'from' => '艦隊指揮部',
        'subject' => '遠征結果',
        'body' => [
            '1' => '由於旗艦中央計算機故障，遠征任務不得不中止。不幸的是，由於計算機故障，艦隊空手而歸。',
            '2' => '您的遠征幾乎陷入了中子星的引力場，需要一些時間才能脫離。因此消耗了大量重氫，遠征艦隊不得不無功而返。',
            '3' => '由於不明原因，遠征跳躍完全失敗。它幾乎降落在一顆太陽的中心。幸運的是，它落在了一個已知系統中，但返回跳躍將花費比預期更長的時間。',
            '4' => '旗艦反應爐核心的故障幾乎摧毀了整個遠征艦隊。幸運的是，技術人員非常能幹，避免了最壞的情況。修理花費了相當長的時間，迫使遠征隊在未完成目標的情況下返回。',
            '5' => '一個由純能量構成的生物登上了船，並使所有遠征隊員陷入了一種奇怪的恍惚狀態，導致他們只能凝視著計算機螢幕上催眠般的圖案。當大多數人最終從這種催眠般的狀態中醒來時，遠征任務因重氫嚴重不足而不得不中止。',
            '6' => '新的導航模組仍然有缺陷。遠征跳躍不僅把他們帶到了錯誤的方向，而且還用盡了所有的重氫燃料。幸運的是，艦隊的跳躍使他們接近了出發星球的一個月球。有點失望的遠征隊現在沒有動力返回。回程將比預期更長。',
            '7' => '您的遠征隊了解到了太空的廣闊空虛。那裡甚至沒有一顆小行星、輻射或粒子可以使這次遠征變得有趣。',
            '8' => '好吧，我們現在知道那些紅色的 5 級異常不僅對船隻的導航系統產生混亂影響，還會在船員中產生大量的幻覺。遠征沒有帶回任何東西。',
            '9' => '您的遠征隊拍攝了一顆超新星的華麗照片。雖然沒有從遠征中獲取任何新東西，但至少有很好的機會在下一期 OGame 雜誌的「宇宙最佳圖片」比賽中獲勝。',
            '10' => '您的遠征艦隊跟隨奇怪的信號一段時間。最後他們注意到這些信號是由一個古老的探測器發送的，這個探測器是在幾代人之前發射的，旨在向外星物種致意。該探測器被保存下來，您家鄉星球的一些博物館已經表達了興趣。',
            '11' => '儘管對這個星區進行了最初非常有希望的掃描，但我們不幸地空手而歸。',
            '12' => '除了一些來自一個未知沼澤星球的古怪小寵物外，這次遠征沒有從旅程中帶回任何激動人心的東西。',
            '13' => '遠征的旗艦在沒有任何警告的情況下跳入艦隊時與一艘外國船隻相撞。外國船隻爆炸了，旗艦受損嚴重。在這種情況下遠征無法繼續，因此艦隊將在進行必要的修理後開始返回。',
            '14' => '我們的遠征隊發現了一個在億萬年前被遺棄的奇怪殖民地。登陸後，我們的船員開始因一種外星病毒而發高燒。據了解，這種病毒消滅了該星球上的整個文明。我們的遠征隊正在回家治療生病的船員。不幸的是，我們不得不中止任務，空手而歸。',
            '15' => '在離開我們的家鄉系統後不久，一種奇怪的計算機病毒攻擊了導航系統。這導致遠征艦隊在原地打轉。不用說，這次遠征並不成功。',
        ],
    ],

    'expedition_gain_resources' => [
        'from' => '艦隊指揮部',
        'subject' => '遠征結果',
        'body' => [
            '1' => '在一個孤立的小行星上，我們發現了一些容易獲取的資源場，並成功進行了收穫。',
            '2' => '您的遠征隊發現了一顆小行星，從中收穫了一些資源。',
            '3' => '您的遠征隊發現了一個古老的、滿載貨物但被遺棄的貨運船隊。部分資源被搶救了出來。',
            '4' => '您的遠征艦隊報告發現了一艘巨大的外星飛船殘骸。他們無法從其技術中學習，但他們能夠將飛船分解成主要部件，並從中製造出一些有用的資源。',
            '5' => '在一顆擁有自己大氣層的小型月球上，您的遠征隊發現了一些巨大的原材料儲存庫。地面上的船員正試圖裝載並運送這批自然寶藏。',
            '6' => '一個未知星球周圍的礦物帶蘊含著無數的資源。遠征船隻正在返回，它們的貨艙滿載！',
        ],
    ],

    'expedition_gain_dark_matter' => [
        'from' => '艦隊指揮部',
        'subject' => '遠征結果',
        'body' => [
            '1' => '遠征隊跟隨一些奇怪的訊號來到一顆小行星。在小行星的核心中發現了少量的暗物質。小行星被帶走，探險者們正試圖提取暗物質。',
            '2' => '遠征隊成功捕獲並儲存了一些暗物質。',
            '3' => '我們在一艘小船的架子上遇到了一個奇怪的外星人，他給了我們一箱暗物質，以換取一些簡單的數學計算。',
            '4' => '我們發現了一艘外星飛船的殘骸。我們在貨艙的架子上發現了一個裝有一些暗物質的小容器！',
            '5' => '我們的遠征隊與一個特殊種族進行了第一次接觸。看起來一個由純能量構成的生物，自稱 Legorian，飛過了遠征船隻，然後決定幫助我們這個欠發達的物種。一個裝有暗物質的箱子在艦橋上物質化了！',
            '6' => '我們的遠征隊接管了一艘幽靈船，它運載著少量的暗物質。我們沒有找到任何關於船上原始船員發生了什麼的線索，但我們的技術人員成功搶救了暗物質。',
            '7' => '我們的遠征隊完成了一個獨特的實驗。他們成功地從一顆垂死的恆星中收穫了暗物質。',
            '8' => '我們的遠征隊定位到一個生鏽的太空站，它似乎已經在太空中不受控制地漂浮了很長時間。太空站本身完全沒用，然而，我們發現反應爐中儲存了一些暗物質。我們的技術人員正試圖盡可能多地搶救。',
        ],
    ],

    'expedition_gain_ships' => [
        'from' => '艦隊指揮部',
        'subject' => '遠征結果',
        'body' => [
            '1' => '我們的遠征隊發現了一個在一系列戰爭中幾乎被摧毀的星球。軌道上有不同的船隻在漂浮。技術人員正試圖修復其中的一些。也許我們也會獲得關於這裡發生了什麼的資訊。',
            '2' => '我們發現了一個廢棄的海盜站。機庫裡有一些舊船隻。我們的技術人員正在確認其中是否還有一些有用。',
            '3' => '您的遠征隊偶然發現了一個在億萬年前被遺棄的殖民地的造船廠。在造船廠的機庫中，他們發現了一些可以打撈的船隻。技術人員正試圖讓其中一些重新飛翔。',
            '4' => '我們發現了先前遠征隊的殘骸！我們的技術人員將嘗試讓一些船隻重新工作。',
            '5' => '我們的遠征隊偶然發現了一個古老的自動造船廠。一些船隻仍在生產階段，我們的技術人員目前正試圖重新啟動造船廠的能量產生器。',
            '6' => '我們發現了一支艦隊的殘骸。技術人員直接前往幾乎完好的船隻，試圖讓它們重新工作。',
            '7' => '我們發現了一個已滅絕文明的星球。我們可以看到一個巨大的完好太空站在軌道上。您的一些技術人員和飛行員前往地表尋找一些仍可使用的船隻。',
        ],
    ],

    'expedition_gain_item' => [
        'from' => '艦隊指揮部',
        'subject' => '遠征結果',
        'body' => [
            '1' => '一支逃跑的艦隊留下了一件物品，目的是分散我們的注意力以幫助他們逃脫。',
        ],
    ],

    'expedition_failed_and_speedup' => [
        'from' => '艦隊指揮部',
        'subject' => '遠征結果',
        'body' => [
            '1' => '您的遠征隊在探索的星區沒有報告任何異常。但是艦隊在返回時遇到了太陽風。這導致回程加速。您的遠征隊提前返回家園。',
            '2' => '新任大膽的指揮官成功穿越了一個不穩定的蟲洞以縮短回程！然而，遠征本身並沒有帶來任何新東西。',
            '3' => '引擎能量線圈的意外回授加速了遠征隊的返回，它比預期更早返回家園。初步報告顯示他們沒有什麼激動人心的事情可報告。',
        ],
    ],

    'expedition_failed_and_delay' => [
        'from' => '艦隊指揮部',
        'subject' => '遠征結果',
        'body' => [
            '1' => '您的遠征隊進入了充滿粒子風暴的星區。這使能量儲備過載，大多數船隻的主系統崩潰。您的機械師能夠避免最壞的情況，但遠征隊將嚴重延遲返回。',
            '2' => '您的導航員在計算中犯了一個嚴重錯誤，導致遠征跳躍計算錯誤。艦隊不僅完全錯過了目標，而且回程將花費比原計劃多得多的時間。',
            '3' => '一顆紅巨星的太陽風破壞了遠征隊的跳躍，計算返回跳躍將花費相當多的時間。那個星區的恆星之間除了空虛的太空之外什麼都沒有。艦隊將比預期更晚返回。',
        ],
    ],

    'expedition_battle' => [
        'from' => '艦隊指揮部',
        'subject' => '遠征結果',
        'body' => [
            '1' => '一些原始野蠻人用甚至不能被稱為太空船的飛船攻擊我們。如果情況變得嚴重，我們將被迫反擊。',
            '2' => '我們需要與一些海盜戰鬥，幸運的是，只有少數幾個。',
            '3' => '我們截獲了一些來自醉酒海盜的無線電傳輸。看來我們很快就會受到攻擊。',
            '4' => '我們的遠征隊遭到一組小型未知船隻的攻擊！',
            '5' => '一些非常絕望的太空海盜試圖捕獲我們的遠征艦隊。',
            '6' => '一些外形奇特的船隻未經警告就攻擊了遠征艦隊！',
            '7' => '您的遠征艦隊與一個未知物種進行了不友好的第一次接觸。',
        ],
    ],

    'expedition_battle_pirates' => [
        'from' => '艦隊指揮部',
        'subject' => '遠征結果',
        'body' => [
            '1' => '一些原始野蠻人用甚至不能被稱為太空船的飛船攻擊我們。如果情況變得嚴重，我們將被迫反擊。',
            '2' => '我們需要與一些海盜戰鬥，幸運的是，只有少數幾個。',
            '3' => '我們截獲了一些來自醉酒海盜的無線電傳輸。看來我們很快就會受到攻擊。',
            '4' => '我們的遠征隊遭到一組太空海盜的攻擊！',
            '5' => '一些非常絕望的太空海盜試圖捕獲我們的遠征艦隊。',
            '6' => '海盜未經警告就伏擊了遠征艦隊！',
            '7' => '一支雜牌太空海盜艦隊攔截了我們，要求進貢。',
        ],
    ],

    'expedition_battle_aliens' => [
        'from' => '艦隊指揮部',
        'subject' => '遠征結果',
        'body' => [
            '1' => '我們收到了來自未知船隻的奇怪訊號。結果發現它們是敵對的！',
            '2' => '一支外星巡邏隊發現了我們的遠征艦隊並立即發起攻擊！',
            '3' => '您的遠征艦隊與一個未知物種進行了不友好的第一次接觸。',
            '4' => '一些外形奇特的船隻未經警告就攻擊了遠征艦隊！',
            '5' => '一支外星戰艦艦隊從超空間出現並與我們交戰！',
            '6' => '我們遇到了一個技術先進的外星物種，它們並不和平。',
            '7' => '我們的感測器在外星飛船攻擊之前檢測到了未知的能量特徵！',
        ],
    ],

    'expedition_loss_of_fleet' => [
        'from' => '艦隊指揮部',
        'subject' => '遠征結果',
        'body' => [
            '1' => '主艦的核心熔毀導致連鎖反應，在一場壯觀的爆炸中摧毀了整個遠征艦隊。',
        ],
    ],

    'expedition_merchant_found' => [
        'from' => '艦隊指揮部',
        'subject' => '遠征結果',
        'body' => [
            '1' => '您的遠征艦隊與一個友好的外星種族建立了聯繫。他們宣布將派一名代表帶著貨物前往您的世界進行交易。',
            '2' => '一艘神秘的商船接近了您的遠征隊。該商人提出訪問您的星球並提供特殊的貿易服務。',
            '3' => '遠征隊遇到了一個星際商人車隊。其中一位商人已同意訪問您的家鄉世界以提供貿易機會。',
        ],
    ],

    'buddy_request_received' => [
        'from' => '好友系統',
        'subject' => '好友請求',
        'body' => '您收到了一個來自 :sender_name 的新好友請求。<span style="display:none;">:buddy_request_id</span>',
    ],

    'buddy_request_accepted' => [
        'from' => '好友系統',
        'subject' => '好友請求已接受',
        'body' => '玩家 :accepter_name 已將您加入好友清單。',
    ],

    'buddy_removed' => [
        'from' => '好友系統',
        'subject' => '您已從好友清單中被刪除',
        'body' => '玩家 :remover_name 已將您從好友清單中移除。',
    ],

    'missile_attack_report' => [
        'from' => '艦隊指揮部',
        'subject' => '對 :target_coords 的導彈攻擊',
        'body' => '您來自 :origin_planet_name :origin_planet_coords (ID: :origin_planet_id) 的星際導彈已到達 :target_planet_name :target_coords (ID: :target_planet_id, 類型: :target_type) 的目標。

發射的導彈：:missiles_sent
攔截的導彈：:missiles_intercepted
命中的導彈：:missiles_hit

摧毀的防禦：:defenses_destroyed',
    ],

    'missile_defense_report' => [
        'from' => '防禦指揮部',
        'subject' => '對 :planet_coords 的導彈攻擊',
        'body' => '您的星球 :planet_name 在 :planet_coords (ID: :planet_id) 遭到了來自 :attacker_name 的星際導彈攻擊！

來襲導彈：:missiles_incoming
攔截的導彈：:missiles_intercepted
命中的導彈：:missiles_hit

摧毀的防禦：:defenses_destroyed',
    ],

    'alliance_broadcast' => [
        'from' => ':sender_name',
        'subject' => '[:alliance_tag] 來自 :sender_name 的聯盟廣播',
        'body' => ':message',
    ],

    'alliance_application_received' => [
        'from' => '聯盟管理',
        'subject' => '新的聯盟申請',
        'body' => '玩家 :applicant_name 已申請加入您的聯盟。

申請訊息：
:application_message',
    ],

    'planet_relocation_success' => [
        'from' => '殖民地管理',
        'subject' => ':planet_name 的搬遷已成功',
        'body' => '星球 :planet_name 已成功從座標 [coordinates]:old_coordinates[/coordinates] 搬遷至 [coordinates]:new_coordinates[/coordinates]。',
    ],

    'fleet_union_invite' => [
        'from' => '艦隊指揮部',
        'subject' => '聯盟戰鬥邀請',
        'body' => ':sender_name 邀請您參與任務 :union_name，目標是 :target_player 位於 [:target_coords]，艦隊已定時於 :arrival_time 到達。

注意：由於加入的艦隊，到達時間可能會改變。每個新艦隊最多可以延長此時間 30%，否則將不允許加入。

注意：所有參與者的總實力與防守者總實力的比較將決定這是否是一場光榮的戰鬥。',
    ],

    'Shipyard is being upgraded.' => '造船廠正在升級中。',
    'Nanite Factory is being upgraded.' => '納米工廠正在升級中。',

    'moon_destruction_success' => [
        'from' => '艦隊指揮部',
        'subject' => '月球 :moon_name [:moon_coords] 已被摧毀！',
        'body' => '在摧毀機率為 :destruction_chance 且死星損失機率為 :loss_chance 的情況下，您的艦隊已成功摧毀了 :moon_coords 的月球 :moon_name。',
    ],

    'moon_destruction_failure' => [
        'from' => '艦隊指揮部',
        'subject' => '在 :moon_coords 的月球摧毀失敗',
        'body' => '在摧毀機率為 :destruction_chance 且死星損失機率為 :loss_chance 的情況下，您的艦隊未能摧毀 :moon_coords 的月球 :moon_name。艦隊正在返回。',
    ],

    'moon_destruction_catastrophic' => [
        'from' => '艦隊指揮部',
        'subject' => '在 :moon_coords 的月球摧毀中遭遇災難性損失',
        'body' => '在摧毀機率為 :destruction_chance 且死星損失機率為 :loss_chance 的情況下，您的艦隊未能摧毀 :moon_coords 的月球 :moon_name。此外，所有死星都在此嘗試中損失了。沒有殘骸。',
    ],

    'moon_destruction_mission_failed' => [
        'from' => '艦隊指揮部',
        'subject' => '在 :coordinates 的月球摧毀任務失敗',
        'body' => '您的艦隊已到達 :coordinates，但在目標位置未發現月球。艦隊正在返回。',
    ],

    'moon_destruction_repelled' => [
        'from' => '太空監控',
        'subject' => '對月球 :moon_name [:moon_coords] 的摧毀企圖已被擊退',
        'body' => ':attacker_name 在摧毀機率為 :destruction_chance 且死星損失機率為 :loss_chance 的情況下攻擊了您在 :moon_coords 的月球 :moon_name。您的月球在攻擊中倖存！',
    ],

    'moon_destroyed' => [
        'from' => '太空監控',
        'subject' => '月球 :moon_name [:moon_coords] 已被摧毀！',
        'body' => '您在 :moon_coords 的月球 :moon_name 已被屬於 :attacker_name 的死星艦隊摧毀！',
    ],

    'wreck_field_repair_completed' => [
        'from' => '系統訊息',
        'subject' => '修復完成',
        'body' => '您在星球 :planet 的修復請求已完成。
:ship_count 艘船已重新投入服役。',
    ],
];
