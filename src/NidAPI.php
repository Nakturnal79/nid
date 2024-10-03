<?php
namespace Ekeng\Nid;

use Ekeng\Nid\NidSession;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Validator;

class NidAPI
{
    const HTTP_OK = 200;

    private $clientId;
    private $clientSecret;

    private $baseUri;
    private $authUri;
    private $tokenUri;

    private $client;

    public function __construct($clientId, $clientSecret, $baseUri, $authUri, $tokenUri)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        $this->baseUri = $baseUri;
        $this->authUri = $authUri;
        $this->tokenUri = $tokenUri;
        $this->client = new Client([
            'base_uri' => $this->baseUri,
            RequestOptions::TIMEOUT => 10
        ]);
    }

    private function getRedirectUrl()
    {
        return route('nid.callback');
    }

    public function generateAuthRedirectUrl($lang,$database)
    {
        $lngCode = is_null($lang) ? 'hy' : $lang;
        $nidSession = NidSession::generate($database);
        if($nidSession['status'] == 'FAIL'){
//            return env('APP_URL');
            throw new Exception($nidSession['message']);
        }
        $data = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->getRedirectUrl(),
            'response_type' => 'code',
            'state' => $nidSession->state,
            'scope' => 'openid',
            'code_challenge' => $nidSession->getCodeChallenge(),
            'code_challenge_method' => 'S256',
        ];
        return $this->baseUri."/$lngCode/".$this->authUri."?".http_build_query($data);
    }

    public function getUserDetails($data)
    {
        $errorMessage = '';
        $validator = Validator::make($data, [
            'code' => 'required|string',
            'state' => 'required|string'
        ]);
        if( $validator->fails() ){
            $errorMessage = 'Nid get user details invalid session credentials';
        }else{

            $session = NidSession::where('state',$data['state'])->first();
            if(!$session){
                $errorMessage = 'nid.exception.get_user_details.invalid_state';
            }
        }

        if($errorMessage){
            throw new Exception($errorMessage);
        }

        $resultData = $this->getToken($data['code'], $session->verifier);
        try {
            $decoded = JWT::decode($resultData['id_token'], new Key($this->clientSecret, 'HS512'));
        }catch (\Exception $e){
            throw new Exception('Nid get user details jsw parse error');
        }

        return $decoded;
    }

    private function getToken($code, $codeVerifier)
    {
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->getRedirectUrl(),
            'grant_type' => 'authorization_code',
            'code' => $code,
            'code_verifier' => $codeVerifier,
        ];

        $resultData = $this->sendRequest($this->tokenUri, $data, 'POST');

        if ( is_null($resultData) || !empty($resultData['error']) || empty($resultData['id_token'])) {
            throw new Exception('Nid get token ErrorMessage: '.($resultData['error'] ?? '') );
        }

        return $resultData;
    }

    private function sendRequest($url, array $data, $method = 'POST', $type = 'form', array $headers = [])
    {
        try {
            $res = $this->client->request($method, $url, [
                RequestOptions::HEADERS => array_merge([
                    'accept' => "application/json",
                ], $headers),
                RequestOptions::FORM_PARAMS => ($data != null ? $data : []),
            ]);
            $code = $res->getStatusCode();
            if ($code == self::HTTP_OK) {
                $response = json_decode($res->getBody(), true);
                return $response;
            } else {
                throw new Exception('Connection error Status code:' . $code);
            }
        } catch (\Exception $e) {
            throw new Exception('Connection error ');
        }

    }
}
