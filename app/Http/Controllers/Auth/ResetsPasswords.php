<?php

namespace Microffice\Http\Controllers\Auth;

use Microffice\User;
use Illuminate\Foundation\Auth\ResetsPasswords as OriginalResetPasswords;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

/**
 * This traits :
 *      - adds the use of User model validation rules
 *      - translates the reset password email subject
 *
 */

trait ResetsPasswords
{
    use OriginalResetPasswords;

    /**
     * Send a reset link to the given user.
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Here we use the User model validation rules instead of ['email' => 'required|email']
        $this->validate($request, removeUniqueRules(getStandaloneValidationRules(User::$rules, 'email')));

        $broker = $this->getBroker();

        $response = Password::broker($broker)->sendResetLink($request->only('email'), function (Message $message) {
            $message->subject($this->getEmailSubject());
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return $this->getSendResetLinkEmailSuccessResponse($response);

            case Password::INVALID_USER:
            default:
                return $this->getSendResetLinkEmailFailureResponse($response);
        }
    }

    /**
     * Get the e-mail subject line to be used for the reset link email.
     *
     * @return string
     */
    protected function getEmailSubject()
    {
        // Here we translate the reset password email subject
        return property_exists($this, 'subject') ? $this->subject : trans('passwords.email-subject');
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function getResetValidationRules()
    {
        // Here we use the User model validation rules
        $rules = User::$rules;
        $rules = [
            // Add token validation to prevent spoofing
            'token' => 'required',
            // Strip any dependency and then any "unique" rule
            'email' => removeUniqueRules(getStandaloneValidationRules($rules['email'])),
            // Get password rules as they are (shouldn't depend on any thing nor be unique...)
            'password' => $rules['password']
        ];

        return $rules;
    }
}
