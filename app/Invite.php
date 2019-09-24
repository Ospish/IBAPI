<?php
class Invite extends Model
{
    protected $fillable = [
        'email', 'invitation_token', 'registered_at',
    ];
}
