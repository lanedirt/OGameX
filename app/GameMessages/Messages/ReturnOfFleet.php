<?php

namespace OGame\GameMessages\Messages;

use OGame\GameMessages\Abstracts\MessageType;

class ReturnOfFleet extends MessageType
{
    protected function initialize(): void
    {
        $this->id = 41;
        $this->subjectKey = 'return_of_fleet_subject';
        $this->bodyKey = 'return_of_fleet_body';
        $this->params = ['planet_name', 'coordinates', 'metal', 'crystal', 'deuterium'];
        $this->category = 'fleets';
    }
}