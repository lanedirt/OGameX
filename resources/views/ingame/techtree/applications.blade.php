
<ul class="subsection_tabs">
    <li>
        <a class="overlay reiter"
           data-overlay-same="true"
           href="{{ route('techtree.ajax', ['tab' => 1, 'object_id' => $object->object->id]) }}">
            <span>
                Techtree            </span>
        </a>
    </li>
    <li>
        <a class="overlay reiter active"
           data-overlay-same="true"
           href="{{ route('techtree.ajax', ['tab' => 4, 'object_id' => $object->object->id]) }}">
            <span>
                Applications            </span>
        </a>
    </li>
    <li>
        <a class="overlay reiter"
           data-overlay-same="true"
           href="{{ route('techtree.ajax', ['tab' => 2, 'object_id' => $object->object->id]) }}">
            <span>
                Techinfo            </span>
        </a>
    </li>
    <li>
        <a class="overlay reiter"
           data-overlay-same="true"
           href="{{ route('techtree.ajax', ['tab' => 3, 'object_id' => $object->object->id]) }}">
            <span>
                Technology            </span>
        </a>
    </li>
</ul>

<div class="techtree" data-id="5752c611e29741257f3cf67e060afb27" data-title="Applications - {{ $object->object->title }}">
    <div class="advice">{{ $object->object->title }} is a prerequisite of:</div>
    There are no such technologies.        </div>
<script type="text/javascript">
    $(document).ready(function(){initOverlayName();});</script>