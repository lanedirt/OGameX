{{-- Alliance Classes Tab --}}
<div id="allianceclassselection">
    <div class="content">
        <h2>{{ __('Select alliance class') }}</h2>
        <p>{{ __('Select an alliance class to receive special bonuses. You can change the alliance class in the alliance menu, provided you have the requisite permissions.') }}</p>

        <div class="allianceclass boxes">
            {{-- Warriors Class --}}
            <div class="allianceclass box" data-alliance-class-id="1" data-alliance-class-name="{{ __('Warriors (Alliance)') }}" data-alliance-class-price="400000">
                <div class="buttons">
                    <a class="build-it_disabled tooltip js_hideTipOnMobile nodarkmatter" rel="{{ route('premium.index') }}" data-tooltip-title="{{ __('There is not enough dark matter available') }}">
                        <span>{{ __('Buy for') }}<br>400,000 DM</span>
                    </a>
                </div>
                <div class="sprite allianceclass large warrior"></div>
                <div class="boxClassBoni">
                    <h2>{{ __('Warriors (Alliance)') }}</h2>
                    <ul>
                        <li class="allianceclass bonus">{{ __('+10% speed for ships flying between alliance members') }}</li>
                        <li class="allianceclass bonus">{{ __('+1 combat research levels') }}</li>
                        <li class="allianceclass bonus">{{ __('+1 espionage research levels') }}</li>
                        <li class="allianceclass bonus">{{ __('The espionage system can be used to scan whole systems.') }}</li>
                    </ul>
                </div>
            </div>

            {{-- Traders Class --}}
            <div class="allianceclass box" data-alliance-class-id="2" data-alliance-class-name="{{ __('Traders (Alliance)') }}" data-alliance-class-price="400000">
                <div class="buttons">
                    <a class="build-it_disabled tooltip js_hideTipOnMobile nodarkmatter" rel="{{ route('premium.index') }}" data-tooltip-title="{{ __('There is not enough dark matter available') }}">
                        <span>{{ __('Buy for') }}<br>400,000 DM</span>
                    </a>
                </div>
                <div class="sprite allianceclass large trader"></div>
                <div class="boxClassBoni">
                    <h2>{{ __('Traders (Alliance)') }}</h2>
                    <ul>
                        <li class="allianceclass bonus">{{ __('+10% speed for transporters') }}</li>
                        <li class="allianceclass bonus">{{ __('+5% mine production') }}</li>
                        <li class="allianceclass bonus">{{ __('+5% energy production') }}</li>
                        <li class="allianceclass bonus">{{ __('+10% planet storage capacity') }}</li>
                        <li class="allianceclass bonus">{{ __('+10% moon storage capacity') }}</li>
                    </ul>
                </div>
            </div>

            {{-- Researchers Class --}}
            <div class="allianceclass box" data-alliance-class-id="3" data-alliance-class-name="{{ __('Researchers (Alliance)') }}" data-alliance-class-price="400000">
                <div class="buttons">
                    <a class="build-it_disabled tooltip js_hideTipOnMobile nodarkmatter" rel="{{ route('premium.index') }}" data-tooltip-title="{{ __('There is not enough dark matter available') }}">
                        <span>{{ __('Buy for') }}<br>400,000 DM</span>
                    </a>
                </div>
                <div class="sprite allianceclass large explorer"></div>
                <div class="boxClassBoni">
                    <h2>{{ __('Researchers (Alliance)') }}</h2>
                    <ul>
                        <li class="allianceclass bonus">{{ __('+5% larger planets on colonisation') }}</li>
                        <li class="allianceclass bonus">{{ __('+10% speed to expedition destination') }}</li>
                        <li class="allianceclass bonus">{{ __('The system phalanx can be used to scan fleet movements in whole systems.') }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <br>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Alliance class selection will be implemented in the future
        // For now, this is a placeholder view

        $('.allianceclass.box').on('click', function() {
            // TODO: Implement alliance class selection/activation
            fadeBox('{{ __("Alliance class system not yet implemented") }}', false);
        });
    });
</script>
