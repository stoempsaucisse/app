<img class='avatar' src='{{ secure_asset("assets/img/avatar.png") }}' />
<input  name="user[avatar]" class="" type="file" placeholder="Email" value="{{ $user->avatar or 'no avatar' }}" required />
