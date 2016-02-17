@if( isset($before) )
@foreach($before as $templateName)
@include($templateName)
@endforeach
@endif
<div class='chat-link' href='chat/{{ $user->id }}' >chat</div>
@if( isset($after) )
@foreach($after as $templateName)
@include($templateName)
@endforeach
@endif
