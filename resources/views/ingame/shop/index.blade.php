@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

<div id="eventboxContent" style="display: none">
        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
    </div>


    <div id="inhalt">
        <div id="planet">
            <div id="header_text">
                <h2>
                    Shop            </h2>
            </div>

            <div id="detail" class="detail_screen small">
                <div id="techDetailLoading"></div>
            </div>

        </div>
        <div class="c-left"></div>
        <div class="c-right"></div>

        <div id="buttonz">
            <div class="header">
                <h2>Shop</h2>
            </div>
            <div class="content">
                <button class="to_shop active tooltip js_hideTipOnMobile" title="You can buy items here.">
                    <span class="to_shop_icon">Shop</span>
                </button>
                <button class="to_inventory tooltip js_hideTipOnMobile" title="You can get an overview of your purchased items here.">
        <span class="to_inventory_icon">
            Inventory            </span>
                </button>

                <div id="itemBox" class="border5px">
                    <div class="aside">
                        <ul class="listfilter border5px categoryFilter">
                            <li class="border5px inShop active">
                                <a href="javascript:void(0);" rel="c18170d3125b9941ef3a86bd28dded7bf2066a6a" class="active">
                            <span>
                                Special offers (<span class="amount">9</span>)
                            </span>
                                </a>
                            </li>
                            <li class="border5px inShop inInventory">
                                <a href="javascript:void(0);" rel="d8d49c315fa620d9c7f1f19963970dea59a0e3be">
                            <span>
                                all (<span class="amount">30</span>)
                            </span>
                                </a>
                            </li>
                            <li class="border5px inShop inInventory">
                                <a href="javascript:void(0);" rel="e71139e15ee5b6f472e2c68a97aa4bae9c80e9da">
                            <span>
                                Resources (<span class="amount">12</span>)
                            </span>
                                </a>
                            </li>
                            <li class="border5px inShop inInventory">
                                <a href="javascript:void(0);" rel="cccaafe693a53e8d1e791f06327974539da5978f">
                            <span>
                                Buddy Items (<span class="amount">3</span>)
                            </span>
                                </a>
                            </li>
                            <li class="border5px inShop inInventory">
                                <a href="javascript:void(0);" rel="dc9ec90e5a2163cc063b8bb3e9fe392782f565c8">
                            <span>
                                Construction (<span class="amount">18</span>)
                            </span>
                                </a>
                            </li>
                        </ul>
                        <div class="btn_wrap">
                            <a href="#" tabindex="1" class="btn btn_confirm buyResourcesLink">
                                Get more resources                    </a>
                        </div>
                        <div class="btn_wrap">
                            <a role="button" tabindex="2" class="btn btn_confirm detail_button slideIn" ref="ffffffffffffffffffffffffffffffffffffffff">
                                Purchase Dark Matter                    </a>
                        </div>
                    </div>


                    <div id="js_shopSliderBox" class="shop_slider"><div class="anythingSlider anythingSlider-default activeSlider" style="width: 335px; height: 332px;"><div class="anythingWindow" style="width: 335px; height: 332px;"><ul id="js_shopSlider" class="anythingBase horizontal" style="width: 335px; left: 0px;"><li class="slide_0 panel activePage" style="width: 335px; height: 332px;"><div class="item_img r_rare" style="background-image: url(/img/icons/40a1644e104985a3e72da28b76069197128f9fb5-100x.png);"><div class="item_img_box"><div class="activation disabled"></div><a href="javascript:void(0);" tabindex="1" title="KRAKEN Gold|Reduces the building time of buildings currently under construction by <b>6h</b>.<br /><br />
Duration: now<br /><br />
Price: 7.000 Dark Matter<br />
In Inventory: 0" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="929d5e15709cc51a4500de4499e19763c879f7f7"><div class="sale_badge disabled"></div><span class="ecke"><span class="level price">7K DM</span></span></a></div></div><div class="item_img r_rare" style="background-image: url(/img/icons/55d4b1750985e4843023d7d0acd2b9bafb15f0b7-100x.png);"><div class="item_img_box"><div class="activation disabled"></div><a href="javascript:void(0);" tabindex="1" title="DETROID Gold|Reduces the construction time of current shipyard-contracts by <b>6h</b>.<br /><br />
Duration: now<br /><br />
Price: 7.000 Dark Matter<br />
In Inventory: 0" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="0968999df2fe956aa4a07aea74921f860af7d97f"><div class="sale_badge disabled"></div><span class="ecke"><span class="level price">7K DM</span></span></a></div></div><div class="item_img r_rare" style="background-image: url(/img/icons/d949732b01a7f7f6d92e814f2de99479a324e1e3-100x.png);"><div class="item_img_box"><div class="activation disabled"></div><a href="javascript:void(0);" tabindex="1" title="NEWTRON Gold|Reduces research time for all research that is currently in progress by <b>6h</b>.<br /><br />
Duration: now<br /><br />
Price: 7.000 Dark Matter<br />
In Inventory: 0" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="8a4f9e8309e1078f7f5ced47d558d30ae15b4a1b"><div class="sale_badge disabled"></div><span class="ecke"><span class="level price">7K DM</span></span></a></div></div><div class="item_img r_uncommon" style="background-image: url(/img/icons/1ee55efe00bb03743ca031a9eaa1374bb936d863-100x.png);"><div class="item_img_box"><div class="activation disabled"></div><a href="javascript:void(0);" tabindex="1" title="KRAKEN Silver|Reduces the building time of buildings currently under construction by <b>2h</b>.<br /><br />
Duration: now<br /><br />
Price: 2.500 Dark Matter<br />
In Inventory: 0" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="4a58d4978bbe24e3efb3b0248e21b3b4b1bfbd8a"><div class="sale_badge disabled"></div><span class="ecke"><span class="level price">2.5K DM</span></span></a></div></div><div class="item_img r_uncommon" style="background-image: url(/img/icons/d0b8fb3d307b815b3182f3872e8eab654fe677df-100x.png);"><div class="item_img_box"><div class="activation disabled"></div><a href="javascript:void(0);" tabindex="1" title="DETROID Silver|Reduces the construction time of current shipyard-contracts by <b>2h</b>.<br /><br />
Duration: now<br /><br />
Price: 2.500 Dark Matter<br />
In Inventory: 0" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="27cbcd52f16693023cb966e5026d8a1efbbfc0f9"><div class="sale_badge disabled"></div><span class="ecke"><span class="level price">2.5K DM</span></span></a></div></div><div class="item_img r_uncommon" style="background-image: url(/img/icons/a92734028d1bf2e75c5c25ae134b4d298a5ca36e-100x.png);"><div class="item_img_box"><div class="activation disabled"></div><a href="javascript:void(0);" tabindex="1" title="NEWTRON Silver|Reduces research time for all research that is currently in progress by <b>2h</b>.<br /><br />
Duration: now<br /><br />
Price: 2.500 Dark Matter<br />
In Inventory: 0" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="d26f4dab76fdc5296e3ebec11a1e1d2558c713ea"><div class="sale_badge disabled"></div><span class="ecke"><span class="level price">2.5K DM</span></span></a></div></div><div class="item_img r_common" style="background-image: url(/img/icons/98629d11293c9f2703592ed0314d99f320f45845-100x.png);"><div class="item_img_box"><div class="activation disabled"></div><a href="javascript:void(0);" tabindex="1" title="KRAKEN Bronze|Reduces the building time of buildings currently under construction by <b>30m</b>.<br /><br />
Duration: now<br /><br />
Price: 700 Dark Matter<br />
In Inventory: 0" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="40f6c78e11be01ad3389b7dccd6ab8efa9347f3c"><div class="sale_badge disabled"></div><span class="ecke"><span class="level price">700 DM</span></span></a></div></div><div class="item_img r_common" style="background-image: url(/img/icons/56724c3a1dcae8036bb172f0be833a6f9a28bc27-100x.png);"><div class="item_img_box"><div class="activation disabled"></div><a href="javascript:void(0);" tabindex="1" title="DETROID Bronze|Reduces the construction time of current shipyard-contracts by <b>30m</b>.<br /><br />
Duration: now<br /><br />
Price: 700 Dark Matter<br />
In Inventory: 0" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="d3d541ecc23e4daa0c698e44c32f04afd2037d84"><div class="sale_badge disabled"></div><span class="ecke"><span class="level price">700 DM</span></span></a></div></div><div class="item_img r_common" style="background-image: url(/img/icons/4bc4327a3fd508b5da84267e2cfd58d47f9e4dcb-100x.png);"><div class="item_img_box"><div class="activation disabled"></div><a href="javascript:void(0);" tabindex="1" title="NEWTRON Bronze|Reduces research time for all research that is currently in progress by <b>30m</b>.<br /><br />
Duration: now<br /><br />
Price: 700 Dark Matter<br />
In Inventory: 0" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="da4a2a1bb9afd410be07bc9736d87f1c8059e66d"><div class="sale_badge disabled"></div><span class="ecke"><span class="level price">700 DM</span></span></a></div></div></li></ul></div><div class="anythingControls" style="display: none;"><ul class="thumbNav" style="display: none;"></ul></div><span class="arrow back disabled" style="display: none;"><a href="#"><span>«</span></a></span><span class="arrow forward disabled" style="display: none;"><a href="#"><span>»</span></a></span></div></div>

                    <div id="js_inventorySliderBox" class="inventory_slider" style="display:none;"></div>
                </div>        <div class="footer"></div>
            </div>
        </div>
    </div>

@endsection
