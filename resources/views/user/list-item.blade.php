                <li class='user-list'>
@can('update', [Microffice\User::class, $user->id])
                    <a class="user-list" href='{{ url("user/$user->id") }}'>
@else
                    <a class="user-list disabled" href='#' title="{{ trans('error.edit', ['resource' => trans_choice('user.user', 1)]) }}">
@endcan
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