                <fieldset id="user-id{{ isset($user) ? '-'.$user->id : '' }}">
                    <legend>{{ trans('user.fieldset-legend') }}</legend>
@if( isset($before) )
@foreach($before as $templateName)
                        @include($templateName)
@endforeach
@endif
                        
                        <input name='user[name]' class="" type='text' placeholder="{{ trans('user.name') }}" value="{{ $user->name or old('user.name') }}" {{ (isset($user) && ($user->id !== auth()->user()->id)) ? 'disabled' : '' }} required />
                        <input  name="user[email]" class="" type="email" placeholder="{{ trans('form.email') }}" value="{{ $user->email or old('user.email') }}" required />
@if ( !isset($user) || ($user->id === auth()->user()->id))

                        <input  name="user[password]" class="" type="password" placeholder="{{ trans('auth.password') }}" value="">
                        <input  name="user[password_confirmation]" class="" type="password" placeholder="{{ trans('auth.confirm_password') }}" value="">
@endif
@if( isset($after) )
@foreach($after as $templateName)
                        @include($templateName)
@endforeach
@endif

                </fieldset>
