                <fieldset id="user-id{{ isset($user) ? '-'.$user->id : '' }}">
                    <legend>{!! trans('user.fieldset-legend') !!}</legend>
@if( isset($before) )
@foreach($before as $templateName)
                        @include($templateName)
@endforeach
@endif
                        
                        <input name="user[name]" class="" type="text" placeholder="{!! trans('user.name') !!}" value="{{ $user->name or old('user.name') }}" {{ (isset($rules['name']) && strpos( $rules['name'], 'required') !== false) ? 'required ' : '' }} {{ (! isset($user) || auth()->user()->can('updateName', [Microffice\User::class, $user->id])) ? '' : 'readonly ' }}/>
                        <input  name="user[email]" class="" type="email" placeholder="{!! trans('form.email') !!}" value="{{ $user->email or old('user.email') }}"  {{ (isset($rules['email']) && strpos( $rules['email'], 'required') !== false) ? 'required ' : '' }}/>
@if ( !isset($user) || auth()->user()->can('updatePassword', [Microffice\User::class, $user->id]))

                        <input  name="user[password]" class="" type="password" placeholder="{!! trans('auth.password') !!}" value="" {{ (isset($rules['password']) && strpos( $rules['password'], 'required') !== false) ? 'required ' : '' }}/>
                        <input  name="user[password_confirmation]" class="" type="password" placeholder="{!! trans('auth.password_confirmation') !!}" value="" {{ (isset($rules['password']) && strpos( $rules['password'], 'required') !== false) ? 'required ' : '' }}/>
@endif
@if( isset($after) )
@foreach($after as $templateName)
                        @include($templateName)
@endforeach
@endif

                </fieldset>
