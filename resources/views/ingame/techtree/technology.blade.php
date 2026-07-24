@php
    use OGame\GameObjects\Models\Abstracts\GameObject;
    use OGame\GameObjects\Models\Techtree\TechtreeRequirement;
    /** @var GameObject $object */
    /** @var array<string, array{label_key: string, objects: array<GameObject>}> $categories */
    /** @var array<int, array<TechtreeRequirement>> $requirements_by_object */
@endphp

<div id="technologytree" data-title="{{ __('t_ingame.techtree.page_title') }} - {{ $object->title }}">
    @include('ingame.techtree.partials.nav', ['currentAction' => 'technologies', 'objectId' => $object->id])

    <div class="content technologies">
        @foreach ($categories as $categoryKey => $category)
            @if (empty($category['objects']))
                @continue
            @endif
            <h1 data-category="{{ $categoryKey }}">{{ __($category['label_key']) }}</h1>
            <ul>
                @foreach ($category['objects'] as $categoryObject)
                    @php($reqs = $requirements_by_object[$categoryObject->id] ?? [])
                    <li class="{{ $categoryObject->class_name }}">
                        <a class="technology sprite_before sprite_tiny {{ $categoryObject->class_name }} overlay"
                           href="{{ route('techtree.ajax', ['tab' => 2, 'object_id' => $categoryObject->id]) }}"
                           data-overlay-same="true">
                            {{ $categoryObject->title }}
                        </a>
                        @if (!empty($reqs))
                            <a class="prerequisites overlay"
                               href="{{ route('techtree.ajax', ['tab' => 1, 'object_id' => $categoryObject->id]) }}"
                               data-overlay-same="true">
                                <ul>
                                    @foreach ($reqs as $requirement)
                                        <li class="{{ $requirement->levelCurrent >= $requirement->levelRequired ? 'fulfilled' : 'unfulfilled' }}">
                                            {{ $requirement->gameObject->title }}
                                            ({{ __('t_ingame.techtree.level') }}
                                            @if ($requirement->levelCurrent >= $requirement->levelRequired)
                                                {{ $requirement->levelRequired }}
                                            @else
                                                {{ $requirement->levelCurrent }}/{{ $requirement->levelRequired }}
                                            @endif
                                            )
                                        </li>
                                    @endforeach
                                </ul>
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endforeach
    </div>
</div>

<script type="text/javascript">
    $(function(){
        initOverlayName();
        $('#technologytree .content.technologies > h1').off('click.techtree').on('click.techtree', function(){
            $(this).next('ul').slideToggle(150);
        });
    });
</script>
