@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="rewardscomponent" class="maincontent">
        <div id="content">
            <div id="inhalt">
                <div id="planet" style="background-image:url(/img/headers/rewards/rewards.jpg);height:250px;">
                    <div id="header_text">
                        <h2>Rewards</h2>
                    </div>
                </div>
                <div id="buttonz">
                    <div class="header">
                        <h2>Rewards</h2>
                    </div>
                    <div class="content">
                        <div class="rewardhint rewardnotifyhidden">
                            <img class="rewardwarningicon" src="/img/icons/04be50e8afc747846a55a646381a16.png">
                            <span class="rewardwarningtext">

                </span>
                        </div>

                        <div class="rewardlist">
                            <a class="tooltipLeft fright questionIcons" style="display: inline-block" title="Rewards will be dispatched every day and can be collected manually. From the 7th day on, no further rewards will be sent out. The first reward will be given on the 2nd day of registration.">
                                <span class="rewardDetail"></span>
                            </a>
                            <br>
                            <h3>New awards</h3>

                            <h3>Awards not yet reached</h3>
                            <div class="rewardlist-item">
                                <div class="rewardlistimg rewardlistimg_1 rewardnotclaim">
                                    <div class="rewardlist-item-icon">
                                        <img src="/img/icons/2251eaefdfdf075833e5247781a4ac.png">
                                    </div>
                                    <div class="rewardlist-item-text">
                                        <h3>Let`s go Emperor</h3>
                                        <div class="rewardlist-item-wrapper">
                                            <p>Greetings, emperor Lieutenant Cupid!

                                                The supplies from your colony ship have been unloaded and are now available to help develop your world. The perfect time to drive forward the improvements to your empire!

                                                Good luck!
                                                The OGame Starter Aid</p>
                                            <a class="reward-button disabled" href="javascript:void(0)">Not fulfilled</a>
                                        </div>
                                        <div class="rewardlist-item-bottom"></div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="rewardlist-item">
                                <div class="rewardlistimg rewardlistimg_2 rewardnotclaim">
                                    <div class="rewardlist-item-icon">
                                        <img src="/img/icons/2251eaefdfdf075833e5247781a4ac.png">
                                    </div>
                                    <div class="rewardlist-item-text">
                                        <h3>The colony is growing!</h3>
                                        <div class="rewardlist-item-wrapper">
                                            <p>Greetings, emperor Lieutenant Cupid!

                                                Your subordinates want to make themselves useful. We have provided you with a KRAKEN robot to help accelerate the improvements to your colony. Increase your production and soon your empire will bloom into untold strength!

                                                Good luck!
                                                The OGame Starter Aid</p>
                                            <a class="reward-button disabled" href="javascript:void(0)">Not fulfilled</a>
                                        </div>
                                        <div class="rewardlist-item-bottom"></div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="rewardlist-item">
                                <div class="rewardlistimg rewardlistimg_4 rewardnotclaim">
                                    <div class="rewardlist-item-icon">
                                        <img src="/img/icons/2251eaefdfdf075833e5247781a4ac.png">
                                    </div>
                                    <div class="rewardlist-item-text">
                                        <h3>Supply and demand</h3>
                                        <div class="rewardlist-item-wrapper">
                                            <p>Greetings, emperor Lieutenant Cupid!

                                                When the metal storage is overflowing and the assembly line stands still because there are no more crystals, it is a good time to pay a visit to the resource merchant. Best check out his offers now.

                                                Good luck!
                                                The OGame Starter Aid</p>
                                            <a class="reward-button disabled" href="javascript:void(0)">Not fulfilled</a>
                                        </div>
                                        <div class="rewardlist-item-bottom"></div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="rewardlist-item">
                                <div class="rewardlistimg rewardlistimg_8 rewardnotclaim">
                                    <div class="rewardlist-item-icon">
                                        <img src="/img/icons/2251eaefdfdf075833e5247781a4ac.png">
                                    </div>
                                    <div class="rewardlist-item-text">
                                        <h3>Progress through technology</h3>
                                        <div class="rewardlist-item-wrapper">
                                            <p>Greetings, emperor Lieutenant Cupid!

                                                Your colony has to be protected from enemy emperors. Rocket Launchers are an effective means of fighting back against attacking space ships. Protect your new home!

                                                Good luck!
                                                The OGame Starter Aid</p>
                                            <a class="reward-button disabled" href="javascript:void(0)">Not fulfilled</a>
                                        </div>
                                        <div class="rewardlist-item-bottom"></div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="rewardlist-item">
                                <div class="rewardlistimg rewardlistimg_16 rewardnotclaim">
                                    <div class="rewardlist-item-icon">
                                        <img src="/img/icons/2251eaefdfdf075833e5247781a4ac.png">
                                    </div>
                                    <div class="rewardlist-item-text">
                                        <h3>Progress through technology</h3>
                                        <div class="rewardlist-item-wrapper">
                                            <p>Greetings, emperor Lieutenant Cupid!

                                                Our scientists are racking their brains. How about we support them a little in their hard work? A NEWTRON robot should be of good use to them.

                                                Good luck!
                                                The OGame Starter Aid</p>
                                            <a class="reward-button disabled" href="javascript:void(0)">Not fulfilled</a>
                                        </div>
                                        <div class="rewardlist-item-bottom"></div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="rewardlist-item">
                                <div class="rewardlistimg rewardlistimg_32 rewardnotclaim">
                                    <div class="rewardlist-item-icon">
                                        <img src="/img/icons/2251eaefdfdf075833e5247781a4ac.png">
                                    </div>
                                    <div class="rewardlist-item-text">
                                        <h3>Conquer outer space</h3>
                                        <div class="rewardlist-item-wrapper">
                                            <p>Greetings, emperor Lieutenant Cupid!

                                                We need to strengthen our forces if we don`t want to end up being our enemy`s play toy. The DETROID robot can accelerate production in the shipyard. That way the fleet will be ready to go at all times!

                                                Good luck!
                                                The OGame Starter Aid</p>
                                            <a class="reward-button disabled" href="javascript:void(0)">Not fulfilled</a>
                                        </div>
                                        <div class="rewardlist-item-bottom"></div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="rewardlist-item">
                                <div class="rewardlistimg rewardlistimg_64 rewardnotclaim">
                                    <div class="rewardlist-item-icon">
                                        <img src="/img/icons/2251eaefdfdf075833e5247781a4ac.png">
                                    </div>
                                    <div class="rewardlist-item-text">
                                        <h3>Expansion of the empire</h3>
                                        <div class="rewardlist-item-wrapper">
                                            <p>Greetings, emperor Lieutenant Cupid!

                                                The foundations for a powerful empire are set. The Commanding Staff are now available to you for 3 days to support you in the consolidation of your empire. Drive research forward and soon new worlds will be open to you and available for your settlers!

                                                Good luck!
                                                The OGame Starter Aid</p>
                                            <a class="reward-button disabled" href="javascript:void(0)">Not fulfilled</a>
                                        </div>
                                        <div class="rewardlist-item-bottom"></div>
                                    </div>
                                </div>
                            </div>
                            <br>

                            <h3>Collected awards</h3>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
