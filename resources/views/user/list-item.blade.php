                <li class='user-list'>
                    <a class="user-list" href='{{ url("user/$user->id/edit") }}'>
@if( isset($before) )
@foreach($before as $templateName)
                        @include($templateName)
@endforeach
@endif
                        <ul class='user-data'>
                            <li class='user-data name'>{{ $user->name }}</li>
                            <li class='user-data email'>{{ $user->email }}</li>
                        </ul>
@if( isset($after) )
@foreach($after as $templateName)
                        @include($templateName)
@endforeach
@endif
                    </a>
                </li>