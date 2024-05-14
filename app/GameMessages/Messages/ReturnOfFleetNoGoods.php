<?php

namespace OGame\GameMessages\Messages;

use OGame\GameMessages\Abstracts\MessageType;

class ReturnOfFleetNoGoods extends MessageType
{
    protected function initialize(): void
    {
        $this->id = 42;
        $this->subjectKey = 'return_of_fleet_no_goods_subject';
        $this->bodyKey = 'return_of_fleet_no_goods_body';
        $this->params = ['planet_name', 'coordinates'];
        $this->category = 'fleets';
    }
}