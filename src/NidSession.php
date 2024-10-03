<?php

namespace Ekeng\Nid;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NidSession extends Model
{
    protected $table = 'nid_sessions';

    protected $fillable = [
        'state',
        'verifier',
    ];

    private function generateState()
    {
        $nidState = uniqid();
        if(static::where('state',$nidState)->first()){
            return $this->generateState();
        }
        return $nidState;
    }

    private function generateVerifier()
    {
        $random = bin2hex(openssl_random_pseudo_bytes(32));
        $verifier = $this->base64UrlEncode(pack('H*', $random));
        return $verifier;
    }

    private function base64UrlEncode($plainText)
    {
        $base64 = base64_encode($plainText);
        $base64 = trim($base64, "=");
        $base64url = strtr($base64, '+/', '-_');
        return ($base64url);
    }

    public function getCodeChallenge()
    {
        return $this->base64UrlEncode(pack('H*', hash('sha256', $this->verifier)));
    }

    public static function generate($databaseName)
    {
        try {
        $db = Schema::hasTable($databaseName);
        if (!empty($db)) {
            $session = new self();
            $session->state = $session->generateState();
            $session->verifier = $session->generateVerifier();
            $session->save();
            return $session;
        } else {
            return ['status' => 'FAIL','message' => "$databaseName table not found"];
        }
        } catch (Exception $e) {
            throw new Exception('Database connection error');
        }

    }
}
