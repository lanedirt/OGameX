{{-- Techtree Navigation Component --}}
<nav data-current-action="{{ $currentAction }}">
    <ul>
        <li>
            <a class="overlay" data-action="technologytree" data-overlay-same="true" href="{{ route('techtree.ajax', ['tab' => 1, 'object_id' => $objectId]) }}">
                @lang('Techtree')
            </a>
        </li>
        <li>
            <a class="overlay" data-action="applications" data-overlay-same="true" href="{{ route('techtree.ajax', ['tab' => 4, 'object_id' => $objectId]) }}">
                @lang('Applications')
            </a>
        </li>
        <li>
            <a class="overlay" data-action="technologyinformation" data-overlay-same="true" href="{{ route('techtree.ajax', ['tab' => 2, 'object_id' => $objectId]) }}">
                @lang('Techinfo')
            </a>
        </li>
        <li>
            <a class="overlay" data-action="technologies" data-overlay-same="true" href="{{ route('techtree.ajax', ['tab' => 3, 'object_id' => $objectId]) }}">
                @lang('Technology')
            </a>
        </li>
    </ul>
</nav>