{{-- Alliance Classes Tab --}}
<div id="allianceclassselection">
    <div class="content">
        <h2>{{ __('t_ingame.alliance.select_class_title') }}</h2>
        <p>{{ __('t_ingame.alliance.select_class_note') }}</p>

        <div class="allianceclass boxes">
            {{-- Warriors Class --}}
            <div class="allianceclass box" data-alliance-class-id="1" data-alliance-class-name="{{ __('t_ingame.alliance.class_warriors') }}" data-alliance-class-price="400000">
                <div class="buttons">
                    <a class="build-it_disabled tooltip js_hideTipOnMobile nodarkmatter" rel="{{ route('premium.index') }}" data-tooltip-title="{{ __('t_ingame.alliance.no_dark_matter') }}">
                        <span>{{ __('t_ingame.alliance.buy_for') }}<br>400,000 DM</span>
                    </a>
                </div>
                <div class="sprite allianceclass large warrior"></div>
                <div class="boxClassBoni">
                    <h2>{{ __('t_ingame.alliance.class_warriors') }}</h2>
                    <ul>
                        <li class="allianceclass bonus">{{ __('t_ingame.alliance.warrior_bonus_1') }}</li>
                        <li class="allianceclass bonus">{{ __('t_ingame.alliance.warrior_bonus_2') }}</li>
                        <li class="allianceclass bonus">{{ __('t_ingame.alliance.warrior_bonus_3') }}</li>
                        <li class="allianceclass bonus">{{ __('t_ingame.alliance.warrior_bonus_4') }}</li>
                    </ul>
                </div>
            </div>

            {{-- Traders Class --}}
            <div class="allianceclass box" data-alliance-class-id="2" data-alliance-class-name="{{ __('t_ingame.alliance.class_traders') }}" data-alliance-class-price="400000">
                <div class="buttons">
                    <a class="build-it_disabled tooltip js_hideTipOnMobile nodarkmatter" rel="{{ route('premium.index') }}" data-tooltip-title="{{ __('t_ingame.alliance.no_dark_matter') }}">
                        <span>{{ __('t_ingame.alliance.buy_for') }}<br>400,000 DM</span>
                    </a>
                </div>
                <div class="sprite allianceclass large trader"></div>
                <div class="boxClassBoni">
                    <h2>{{ __('t_ingame.alliance.class_traders') }}</h2>
                    <ul>
                        <li class="allianceclass bonus">{{ __('t_ingame.alliance.trader_bonus_1') }}</li>
                        <li class="allianceclass bonus">{{ __('t_ingame.alliance.trader_bonus_2') }}</li>
                        <li class="allianceclass bonus">{{ __('t_ingame.alliance.trader_bonus_3') }}</li>
                        <li class="allianceclass bonus">{{ __('t_ingame.alliance.trader_bonus_4') }}</li>
                        <li class="allianceclass bonus">{{ __('t_ingame.alliance.trader_bonus_5') }}</li>
                    </ul>
                </div>
            </div>

            {{-- Researchers Class --}}
            <div class="allianceclass box" data-alliance-class-id="3" data-alliance-class-name="{{ __('t_ingame.alliance.class_researchers') }}" data-alliance-class-price="400000">
                <div class="buttons">
                    <a class="build-it_disabled tooltip js_hideTipOnMobile nodarkmatter" rel="{{ route('premium.index') }}" data-tooltip-title="{{ __('t_ingame.alliance.no_dark_matter') }}">
                        <span>{{ __('t_ingame.alliance.buy_for') }}<br>400,000 DM</span>
                    </a>
                </div>
                <div class="sprite allianceclass large explorer"></div>
                <div class="boxClassBoni">
                    <h2>{{ __('t_ingame.alliance.class_researchers') }}</h2>
                    <ul>
                        <li class="allianceclass bonus">{{ __('t_ingame.alliance.researcher_bonus_1') }}</li>
                        <li class="allianceclass bonus">{{ __('t_ingame.alliance.researcher_bonus_2') }}</li>
                        <li class="allianceclass bonus">{{ __('t_ingame.alliance.researcher_bonus_3') }}</li>
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
            fadeBox(@json(__('t_ingame.alliance.class_not_implemented')), false);
        });
    });
</script>
