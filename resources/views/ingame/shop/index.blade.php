@extends('ingame.layouts.main')

@section('content')

@if(session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
<div id="eventlistcomponent" class="">
    <div id="eventboxContent" style="display: none;">
        <div id="eventListWrap">
            <div id="eventHeader">
                <a class="close_details eventToggle" href="javascript:toggleEvents();">
                </a>
                <h2>Events</h2>
            </div>
            <table id="eventContent">
                <tbody>
                </tbody>
            </table>
            <div id="eventFooter"></div>
        </div>
    </div>
</div>
<div id="shopcomponent" class="maincontent">
    <div id="inhalt">
        <div id="planet">
            <div id="header_text">
                <h2>Shop</h2>
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
            <div class="shopContent">
                <div class="tabSelection">
                    <div class="tabSelectionTab shopTab tooltip active" onclick="switchShopTab($(this), 'shop')" data-tooltip-title="You can buy items here.">
                        <span>Shop</span>
                        <span class="shopIcon"></span>
                    </div>
                    <div class="tabSelectionTab inventoryTab tooltip" onclick="switchShopTab($(this), 'inventory')" data-tooltip-title="You can get an overview of your purchased items here.">
                        <span>Inventory</span>
                        <span class="inventoryIcon"></span>
                    </div>
                </div>
                <div class="itemsHolder">
                    <div id="itemBox">
                        <div class="categoryHolder">
                            <div class="categoryItem">
                                <a href="javascript:void(0);" rel="c18170d3125b9941ef3a86bd28dded7bf2066a6a"
                                    onclick="inventoryObj.loadCategory('c18170d3125b9941ef3a86bd28dded7bf2066a6a')">
                                    <span>
                                        Special offers (<span class="amount">86</span>)
                                    </span>
                                </a>
                            </div>
                            <div class="categoryItem active">
                                <a href="javascript:void(0);" rel="d8d49c315fa620d9c7f1f19963970dea59a0e3be"
                                    onclick="inventoryObj.loadCategory('d8d49c315fa620d9c7f1f19963970dea59a0e3be')" class="active">
                                    <span>
                                        all (<span class="amount">86</span>)
                                    </span>
                                </a>
                            </div>
                            <div class="categoryItem">
                                <a href="javascript:void(0);" rel="fb3dc135f6a08a703cc412a21ac53c875eaf31f0"
                                    onclick="inventoryObj.loadCategory('fb3dc135f6a08a703cc412a21ac53c875eaf31f0')">
                                    <span>
                                        Booster (90 Days) (<span class="amount">12</span>)
                                    </span>
                                </a>
                            </div>
                            <div class="categoryItem">
                                <a href="javascript:void(0);" rel="43a659a3e77cdab39b03b4acc8b8d76ace5694a3"
                                    onclick="inventoryObj.loadCategory('43a659a3e77cdab39b03b4acc8b8d76ace5694a3')">
                                    <span>
                                        Booster (30 Days) (<span class="amount">12</span>)
                                    </span>
                                </a>
                            </div>
                            <div class="categoryItem">
                                <a href="javascript:void(0);" rel="e71139e15ee5b6f472e2c68a97aa4bae9c80e9da"
                                    onclick="inventoryObj.loadCategory('e71139e15ee5b6f472e2c68a97aa4bae9c80e9da')">
                                    <span>
                                        Resources (<span class="amount">16</span>)
                                    </span>
                                </a>
                            </div>
                            <div class="categoryItem">
                                <a href="javascript:void(0);" rel="8647110d430fc73ec28738d38769a71103941e69"
                                    onclick="inventoryObj.loadCategory('8647110d430fc73ec28738d38769a71103941e69')">
                                    <span>
                                        Class Selection (<span class="amount">6</span>)
                                    </span>
                                </a>
                            </div>
                            <div class="categoryItem">
                                <a href="javascript:void(0);" rel="dc9ec90e5a2163cc063b8bb3e9fe392782f565c8"
                                    onclick="inventoryObj.loadCategory('dc9ec90e5a2163cc063b8bb3e9fe392782f565c8')">
                                    <span>
                                        Construction (<span class="amount">29</span>)
                                    </span>
                                </a>
                            </div>
                            <div class="categoryItem">
                                <a href="javascript:void(0);" rel="631c69d4f43547882f510450d543cd29bc858c21"
                                    onclick="inventoryObj.loadCategory('631c69d4f43547882f510450d543cd29bc858c21')">
                                    <span>
                                        Profile (<span class="amount">11</span>)
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div class="btn_wrap">
                            <a href="https://s260-en.ogame.gameforge.com/game/index.php?page=ingame&amp;component=traderOverview#animation=false&amp;page=traderResources"
                                tabindex="1" class="btn btn_confirm buyResourcesLink">
                                Get more resources
                            </a>
                        </div>
                        <div class="btn_wrap">
                            <a role="button" tabindex="2" class="btn btn_confirm detail_button slideIn"
                                ref="ffffffffffffffffffffffffffffffffffffffff">
                                Purchase Dark Matter
                            </a>
                        </div>
                    </div>
                    <div id="js_shopSliderBox" class="shop_slider">
                        <div class="anythingSlider anythingSlider-default activeSlider" style="width: 335px; height: 332px;">
                            <div class="anythingWindow" style="width: 100%; height: 100%;">
                                <ul id="js_shopSlider" class="anythingBase horizontal" style="width: 3350px; left: 0px;">
                                    <li class="slide_0 panel activePage" style="width: 335px; height: 332px;">
                                        <div class="item_img r_common" style="background-image: url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png), url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png);">
                                            <div class="item_img_box">
                                                <div class="activation disabled"></div>
                                                <a href="javascript:void(0);" tabindex="1" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="c1d0232604872f899ea15a9772baf76880f55c5f" 
                                                    data-tooltip-title="Complete Resource Package|Upon activation you receive a daily production of all resources.<br /><br />
                                                    Duration: now<br /><br />
                                                    Price: 288,000 Dark Matter<br />
                                                    In Inventory: 0">
                                                    <div class="sale_badge disabled"></div>
                                                    <span class="ecke"><span class="level price">288K DM</span></span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="item_img r_common" style="background-image: url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png), url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png);">
                                            <div class="item_img_box">
                                                <div class="activation disabled"></div>
                                                <a href="javascript:void(0);" tabindex="1" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="c1d0232604872f899ea15a9772baf76880f55c5f" 
                                                    data-tooltip-title="Complete Resource Package|Upon activation you receive a daily production of all resources.<br /><br />
                                                    Duration: now<br /><br />
                                                    Price: 288,000 Dark Matter<br />
                                                    In Inventory: 0">
                                                    <div class="sale_badge disabled"></div>
                                                    <span class="ecke"><span class="level price">288K DM</span></span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="item_img r_common" style="background-image: url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png), url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png);">
                                            <div class="item_img_box">
                                                <div class="activation disabled"></div>
                                                <a href="javascript:void(0);" tabindex="1" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="c1d0232604872f899ea15a9772baf76880f55c5f" 
                                                    data-tooltip-title="Complete Resource Package|Upon activation you receive a daily production of all resources.<br /><br />
                                                    Duration: now<br /><br />
                                                    Price: 288,000 Dark Matter<br />
                                                    In Inventory: 0">
                                                    <div class="sale_badge disabled"></div>
                                                    <span class="ecke"><span class="level price">288K DM</span></span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="item_img r_common" style="background-image: url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png), url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png);">
                                            <div class="item_img_box">
                                                <div class="activation disabled"></div>
                                                <a href="javascript:void(0);" tabindex="1" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="c1d0232604872f899ea15a9772baf76880f55c5f" 
                                                    data-tooltip-title="Complete Resource Package|Upon activation you receive a daily production of all resources.<br /><br />
                                                    Duration: now<br /><br />
                                                    Price: 288,000 Dark Matter<br />
                                                    In Inventory: 0">
                                                    <div class="sale_badge disabled"></div>
                                                    <span class="ecke"><span class="level price">288K DM</span></span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="item_img r_common" style="background-image: url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png), url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png);">
                                            <div class="item_img_box">
                                                <div class="activation disabled"></div>
                                                <a href="javascript:void(0);" tabindex="1" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="c1d0232604872f899ea15a9772baf76880f55c5f" 
                                                    data-tooltip-title="Complete Resource Package|Upon activation you receive a daily production of all resources.<br /><br />
                                                    Duration: now<br /><br />
                                                    Price: 288,000 Dark Matter<br />
                                                    In Inventory: 0">
                                                    <div class="sale_badge disabled"></div>
                                                    <span class="ecke"><span class="level price">288K DM</span></span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="item_img r_common" style="background-image: url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png), url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png);">
                                            <div class="item_img_box">
                                                <div class="activation disabled"></div>
                                                <a href="javascript:void(0);" tabindex="1" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="c1d0232604872f899ea15a9772baf76880f55c5f" 
                                                    data-tooltip-title="Complete Resource Package|Upon activation you receive a daily production of all resources.<br /><br />
                                                    Duration: now<br /><br />
                                                    Price: 288,000 Dark Matter<br />
                                                    In Inventory: 0">
                                                    <div class="sale_badge disabled"></div>
                                                    <span class="ecke"><span class="level price">288K DM</span></span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="item_img r_common" style="background-image: url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png), url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png);">
                                            <div class="item_img_box">
                                                <div class="activation disabled"></div>
                                                <a href="javascript:void(0);" tabindex="1" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="c1d0232604872f899ea15a9772baf76880f55c5f" 
                                                    data-tooltip-title="Complete Resource Package|Upon activation you receive a daily production of all resources.<br /><br />
                                                    Duration: now<br /><br />
                                                    Price: 288,000 Dark Matter<br />
                                                    In Inventory: 0">
                                                    <div class="sale_badge disabled"></div>
                                                    <span class="ecke"><span class="level price">288K DM</span></span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="item_img r_common" style="background-image: url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png), url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png);">
                                            <div class="item_img_box">
                                                <div class="activation disabled"></div>
                                                <a href="javascript:void(0);" tabindex="1" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="c1d0232604872f899ea15a9772baf76880f55c5f" 
                                                    data-tooltip-title="Complete Resource Package|Upon activation you receive a daily production of all resources.<br /><br />
                                                    Duration: now<br /><br />
                                                    Price: 288,000 Dark Matter<br />
                                                    In Inventory: 0">
                                                    <div class="sale_badge disabled"></div>
                                                    <span class="ecke"><span class="level price">288K DM</span></span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="item_img r_common" style="background-image: url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png), url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png);">
                                            <div class="item_img_box">
                                                <div class="activation disabled"></div>
                                                <a href="javascript:void(0);" tabindex="1" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="c1d0232604872f899ea15a9772baf76880f55c5f" 
                                                    data-tooltip-title="Complete Resource Package|Upon activation you receive a daily production of all resources.<br /><br />
                                                    Duration: now<br /><br />
                                                    Price: 288,000 Dark Matter<br />
                                                    In Inventory: 0">
                                                    <div class="sale_badge disabled"></div>
                                                    <span class="ecke"><span class="level price">288K DM</span></span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="item_img r_common" style="background-image: url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png), url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png);">
                                            <div class="item_img_box">
                                                <div class="activation disabled"></div>
                                                <a href="javascript:void(0);" tabindex="1" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="c1d0232604872f899ea15a9772baf76880f55c5f" 
                                                    data-tooltip-title="Complete Resource Package|Upon activation you receive a daily production of all resources.<br /><br />
                                                    Duration: now<br /><br />
                                                    Price: 288,000 Dark Matter<br />
                                                    In Inventory: 0">
                                                    <div class="sale_badge disabled"></div>
                                                    <span class="ecke"><span class="level price">288K DM</span></span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="item_img r_common" style="background-image: url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png), url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png);">
                                            <div class="item_img_box">
                                                <div class="activation disabled"></div>
                                                <a href="javascript:void(0);" tabindex="1" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="c1d0232604872f899ea15a9772baf76880f55c5f" 
                                                    data-tooltip-title="Complete Resource Package|Upon activation you receive a daily production of all resources.<br /><br />
                                                    Duration: now<br /><br />
                                                    Price: 288,000 Dark Matter<br />
                                                    In Inventory: 0">
                                                    <div class="sale_badge disabled"></div>
                                                    <span class="ecke"><span class="level price">288K DM</span></span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="item_img r_common" style="background-image: url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png), url(https://s260-en.ogame.gameforge.com/cdn/img/item-images/18145145a76b71f151ba97b7c17891a5389ceeb5.png);">
                                            <div class="item_img_box">
                                                <div class="activation disabled"></div>
                                                <a href="javascript:void(0);" tabindex="1" class="detail_button tooltipHTML js_hideTipOnMobile slideIn" ref="c1d0232604872f899ea15a9772baf76880f55c5f" 
                                                    data-tooltip-title="Complete Resource Package|Upon activation you receive a daily production of all resources.<br /><br />
                                                    Duration: now<br /><br />
                                                    Price: 288,000 Dark Matter<br />
                                                    In Inventory: 0">
                                                    <div class="sale_badge disabled"></div>
                                                    <span class="ecke"><span class="level price">288K DM</span></span>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="anythingControls" style="display: block;">
                                <ul class="thumbNav">
                                    <li class="first"><a class="panel1 cur" href="#"><span>1</span></a></li>
                                    <li><a class="panel2" href="#"><span>2</span></a></li>
                                    <li><a class="panel3" href="#"><span>3</span></a></li>
                                    <li><a class="panel4" href="#"><span>4</span></a></li>
                                    <li><a class="panel5" href="#"><span>5</span></a></li>
                                    <li><a class="panel6" href="#"><span>6</span></a></li>
                                    <li><a class="panel7" href="#"><span>7</span></a></li>
                                    <li><a class="panel8" href="#"><span>8</span></a></li>
                                    <li><a class="panel9" href="#"><span>9</span></a></li>
                                    <li class="last"><a class="panel10" href="#"><span>10</span></a></li>
                                </ul>
                            </div>
                            <span class="arrow back disabled"><a href="#"><span>«</span></a></span>
                            <span class="arrow forward"><a href="#"><span>»</span></a></span>
                        </div>
                    </div>
                    <div id="js_inventorySliderBox" class="inventory_slider" style="display:none;"></div>
                </div>
                <div class="footer"></div>
            </div>
        </div>
    </div>
</div>
@endsection