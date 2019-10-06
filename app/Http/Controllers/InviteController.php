<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InviteController extends Controller
{


    public function sendInviteLink($email , $token, $type)
        //public function sendInviteLink(Request $request, $id)
    {
        $user = $email;
        Mail::send('invite', ['user' => $user, 'token' => $token, 'type' => $type], function ($m) use ($user) {
            $m->from('hello@arthouseamur.ru', 'Inbloom');

            $m->to($user, 'name')->subject('Приглашение');
        });
    }
    public function generateInvitationToken($email) {

        return substr(md5(rand(0, 9) . $email . time()), 0, 6);
    }

    public function sendInviteLinkEmail(Request $request)
    {
        $token = $this->generateInvitationToken($request->email);
        DB::insert('insert into invites (created_at, invite, type, email) values (now(), "'.$token.'", '.$request->type.', "'.$request->email.'")', [1]);
        $this->validateEmail($request);
        $this->sendInviteLink($request->email, $token, $request->type);
    }
    /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    }

	public function store(StoreInvitationRequest $request)
	{
	    $invitation = new Invite($request->all());
	    $invitation->generateInvitationToken();
	    $invitation->save();
	}

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );
        // TODO: сделать нормальный ответ от сервера
        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }
}
?>
