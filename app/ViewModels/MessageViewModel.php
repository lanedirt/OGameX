<?php

namespace OGame\ViewModels;

use Illuminate\Support\Carbon;
use OGame\Factories\GameMessageFactory;
use OGame\GameMessages\Abstracts\GameMessage;
use OGame\Models\Message;

/**
 * MessageViewModel
 */
class MessageViewModel
{
    public int $id;
    public int $user_id;
    public string $key;
    public ?string $subject;
    public ?string $body;
    /**
     * @var array<string, string> $params
     */
    public ?array $params;
    public int $viewed;
    public ?Carbon $created_at;
    public ?Carbon $updated_at;
    private ?GameMessage $gameMessage = null;
    private Message $message;

    /**
     * Constructor
     */
    public function __construct(Message $message)
    {
        $this->id = $message->id;
        $this->user_id = $message->user_id;
        $this->key = $message->key;
        $this->subject = $message->subject;
        $this->body = $message->body;
        $this->params = $message->params;
        $this->viewed = $message->viewed;
        $this->created_at = $message->created_at;
        $this->updated_at = $message->updated_at;

        $gameMessage = GameMessageFactory::createGameMessage($this->key);
        $this->gameMessage = $gameMessage;

        // TODO: if we have enough with just the message object itself, we can remove duplicate properties above
        // as it is already stored in the message object and does not make sense to duplicate it here.
        $this->message = $message;
    }

    /**
     * Get the message sender.
     *
     * @return string
     */
    public function getFrom(): string
    {
        return $this->gameMessage->getFrom();
    }

    /**
     * Get the message ID.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the message subject.
     *
     * @return string
     */
    public function getSubject(): string
    {
        if ($this->gameMessage !== null) {
            // TODO: do we need replacements here or is the subject always static?
            return $this->gameMessage->getSubject($this->message);
        }

        return $this->subject;
    }

    /**
     * Get the dynamic message body with placeholders replaced with actual values.
     */
    public function getBody(): string
    {
        if ($this->gameMessage !== null) {
            return $this->gameMessage->getBody($this->message);
        } else {
            // TODO: implement dynamic messages without templates (e.g. mass messages from admin to players)
            return '';
        }
    }

    /**
     * Get the body of the message used for the overlay view of the message.
     */
    public function getBodyFull(): string
    {
        // TODO: refactor this full message "body" retrieval? Do we want body and subject separate or together?
        if ($this->gameMessage !== null) {
            return $this->gameMessage->getBodyFull($this->message);
        } else {
            // TODO: implement dynamic messages without templates (e.g. mass messages from admin to players)
            return '';
        }
    }

    /**
     * Get footer actions for the message.
     *
     * @return string
     */
    public function getFooterActions(): string
    {
        return '';

        /*
         <gradient-button sq30="">
                            <button class="custom_btn icon_not_favorited tooltip msgFavouriteBtn"
                                    title="mark as favourite"
                                    data-message-id="81049"><img
                                        src="/img/icons/not_favorited.png"
                                        style="width:20px;height:20px;"></button>
                        </gradient-button>
                        <gradient-button sq30="">
                            <button class="custom_btn icon_apikey tooltip msgApiKeyBtn"
                                    title="This data can be entered into a compatible combat simulator:<br/><input value='sr-en-255-f6796891d781ce5b9c10a401795b3e5acf4bcc50' readonly onclick='select()' style='width:360px'></input>"
                                    data-message-id="81049"><img
                                        src="/img/icons/apikey.png"
                                        style="width:20px;height:20px;"></button>
                        </gradient-button>
                        <gradient-button sq30="">
                            <button class="custom_btn tooltip msgCombatSimBtn"
                                    title="Open in Combat Simulator"
                                    onclick="window.open('#combatsim&amp;reportHash=sr-en-255-f6796891d781ce5b9c10a401795b3e5acf4bcc50');"
                                    data-message-id="81049"><img
                                        src="/img/icons/speed.png"
                                        style="width:20px;height:20px;"></button>
                        </gradient-button>
                        <gradient-button sq30="">
                            <button class="custom_btn overlay tooltip msgShareBtn"
                                    title="share message" data-message-id="81049"
                                    data-overlay-title="share message"
                                    data-target="#shareReportOverlay&amp;messageId=81049">
                                <img src="/img/icons/share.png"
                                     style="width:20px;height:20px;"></button>
                        </gradient-button>
                        <gradient-button sq30="">
                            <button class="custom_btn tooltip msgAttackBtn"
                                    title="Attack"
                                    onclick="window.location.href='#fleetdispatch&amp;galaxy=2&amp;system=8&amp;position=12&amp;type=1&amp;mission=1';"
                                    data-message-id="81049">
                                <div class="msgAttackIconContainer"><img
                                            src="/img/icons/attack.png"
                                            style="width:20px;height:20px;"></div>
                            </button>
                        </gradient-button>
                        <gradient-button sq30="">
                            <button class="custom_btn tooltip msgEspionageBtn"
                                    title="Espionage"
                                    onclick="sendShipsWithPopup(6,2,8,12,1,2); return false;"
                                    data-message-id="81049"><img
                                        src="/img/icons/espionage.png"
                                        style="width:20px;height:20px;"></button>
                        </gradient-button>
         */
    }

    public function getFooterDetails(): string
    {
        if ($this->gameMessage !== null) {
            return $this->gameMessage->getFooterDetails($this->message);
        } else {
            // TODO: implement dynamic messages without templates (e.g. mass messages from admin to players)
            return '';
        }
    }

    /**
     * Check if the message has not been viewed yet.
     *
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->viewed === 0;
    }

    /**
     * Get the message creation date.
     *
     * @return string
     */
    public function getDate(): string
    {
        // Return in this format: 18.03.2024 14:50:38
        return $this->created_at->format('d.m.Y H:i:s');
    }
}
