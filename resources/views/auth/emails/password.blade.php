{{ trans('auth.email-password') }}: {{ url('password/reset', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}
