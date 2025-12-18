@extends('ingame.layouts.main')

@section('content')

    <div id="characterclassselectioncomponent" class="maincontent">
        <div id="characterclassselection">
            <div id="inhalt">
                <div class="header small" id="planet">
                    <h2>Class Selection</h2>
                </div>
                <div class="c-left shortCorner"></div>
                <div class="c-right shortCorner"></div>
                <div class="boxWrapper">
                    <div class="header">
                    </div>
                    <div class="content">
                        <h2>Choose Your Class</h2>
                        <p>Select a class to receive additional benefits. You can change your class in the class selection section in the top-right.</p>
                        <div class="characterclass boxes">
                            @foreach($classes as $class)
                                <div class="characterclass box {{ $currentClass && $currentClass->value === $class->value ? 'selected' : '' }}"
                                     data-character-class-id="{{ $class->value }}"
                                     data-character-class-name="{{ $class->getName() }}"
                                     data-character-class-price="{{ $changeCost }}">
                                    <div class="buttons">
                                        @if($currentClass && $currentClass->value === $class->value)
                                            <a class="deactivate-it deactivate" href="javascript:void(0);" onclick="deselectCharacterClass()">
                                                <span>Deactivate</span>
                                            </a>
                                        @else
                                            @if($isFreeSelection)
                                                <a class="build-it" href="javascript:void(0);" onclick="selectCharacterClass({{ $class->value }}, '{{ $class->getName() }}', {{ $changeCost }})">
                                                    <span>Select for Free</span>
                                                </a>
                                            @elseif($darkMatter >= $changeCost)
                                                <a class="build-it" href="javascript:void(0);" onclick="selectCharacterClass({{ $class->value }}, '{{ $class->getName() }}', {{ $changeCost }})">
                                                    <span>Buy for<br>{{ number_format($changeCost, 0, ',', '.') }} DM</span>
                                                </a>
                                            @else
                                                <a class="build-it_disabled nodarkmatter" href="/premium">
                                                    <span>Buy for<br>{{ number_format($changeCost, 0, ',', '.') }} DM</span>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="sprite characterclass large {{ $class->getMachineName() }}"></div>
                                    <div class="boxClassBoni">
                                        <h2>{{ $class->getName() }}</h2>
                                        <ul>
                                            @foreach($class->getBonuses() as $bonus)
                                                <li class="characterclass bonus">{{ $bonus }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="shipinfo">
                                        <span>{{ $class->getClassShipName() }}</span>
                                        <div class="shipdescription">{{ $class->getShipDescription() }}</div>
                                        <div class="sprite ship small ship{{ $class->getClassShipId() }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <br>
                    </div>
                    <div class="footer"></div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function selectCharacterClass(classId, className, price) {
            let message = '';
            @if($isFreeSelection)
                message = 'Do you want to activate the ' + className + ' class for free?';
            @else
                message = 'Do you want to activate the ' + className + ' class for ' + price.toLocaleString() + ' Dark Matter? In doing so, you will lose your current class.';
            @endif

            errorBoxDecision(
                'Select Character Class',
                message,
                'Confirm',
                'Cancel',
                function() {
                    fetch('{{ route('characterclass.select') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            characterClassId: classId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            fadeBox('Character class selected successfully!', true);
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else if (data.lackingDM) {
                            errorBoxDecision(
                                'Not enough Dark Matter',
                                'Not enough Dark Matter available! Do you want to buy some now?',
                                'Buy Dark Matter',
                                'Cancel',
                                function() {
                                    window.location.href = '/premium';
                                }
                            );
                        } else {
                            fadeBox(data.message, false);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        fadeBox('An error occurred. Please try again.', false);
                    });
                }
            );
        }

        function deselectCharacterClass() {
            errorBoxDecision(
                'Deactivate Character Class',
                'Do you really want to deactivate your character class? Reactivation requires {{ number_format($changeCost, 0, ',', '.') }} Dark Matter.',
                'Deactivate',
                'Cancel',
                function() {
                    fetch('{{ route('characterclass.deselect') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            fadeBox('Character class deactivated successfully!', true);
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            fadeBox(data.message, false);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        fadeBox('An error occurred. Please try again.', false);
                    });
                }
            );
        }
    </script>

@endsection
