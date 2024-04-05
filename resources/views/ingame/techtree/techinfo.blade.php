
<ul class="subsection_tabs">
    <li>
        <a class="overlay reiter"
           data-overlay-same="true"
           href="{{ route('techtree.ajax', ['tab' => 1, 'object_id' => $object['id']]) }}">
            <span>
                Techtree            </span>
        </a>
    </li>
    <li>
        <a class="overlay reiter"
           data-overlay-same="true"
           href="{{ route('techtree.ajax', ['tab' => 4, 'object_id' => $object['id']]) }}">
            <span>
                Applications            </span>
        </a>
    </li>
    <li>
        <a class="overlay reiter active"
           data-overlay-same="true"
           href="{{ route('techtree.ajax', ['tab' => 2, 'object_id' => $object['id']]) }}">
            <span>
                Techinfo            </span>
        </a>
    </li>
    <li>
        <a class="overlay reiter"
           data-overlay-same="true"
           href="{{ route('techtree.ajax', ['tab' => 3, 'object_id' => $object['id']]) }}">
            <span>
                Technology            </span>
        </a>
    </li>
</ul>

<div class="techtree" data-id="c28d1c5551545f27be33f22c5643c45e" data-title="Techinfo - {{ $object['title'] }}">
    <div id="techinfo">
        <div class="techwrapper">
            <div class="leftcol {{ $object['type'] }} tech{{ $object_id }}">
                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="200" height="200" />
            </div>

            <div class="rightcol">
                <p>{!! nl2br($object['description_long']) !!}</p>

                <!--
                Different types of tables:
                - Resource production values
                - Energy production values
                - Storage values
                - Unit rapidfire values
                - Unit technical information
                -->
                {!! $production_table !!}
                {!! $rapidfire_table !!}
                {!! $properties_table !!}
            </div><!-- rightcol -->
            <br class="clearfloat"/>
        </div><!-- techwrapper -->
    </div><!-- techinfo -->

    <script type="text/javascript">
        $(document).ready(function(){
            $(".detailTableRow:not(.currentlevel):odd").addClass('alt');
        });
    </script>
</div>
<script type="text/javascript">
    $(document).ready(function(){initOverlayName();});</script>