<?php
namespace App\Components\HaveIBeenPwnedApi;

use DateTime;
use \GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class SearchPasswordByRange {
    protected const BASE_URL = 'https://api.pwnedpasswords.com/range';

    protected static $responses = [];

    protected $password = '';

    protected $hashed_password = '';

    protected $hash_prefix = '';

    protected $response = [];

    public function __construct(string $password) {
        $this->password = $password;

        $this->hashed_password = sha1($password);

        $this->hash_prefix = substr($this->hashed_password, 0, 5);
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getHashedPassword(): string {
        return $this->hashed_password;
    }

    public function search(): void {
        if(isset(static::$responses[$this->hash_prefix])) {
            $this->response = static::$responses[$this->hash_prefix];

            return;
        }

        $this->response = Cache::store('opcache')->remember(
            "pwned_passwords:{$this->hash_prefix}",
            new DateTime('next week'),
            function() {
                $client = new Client();

                $url = static::BASE_URL . "/{$this->hash_prefix}";

                $response = $client->get($url);

                return explode("\r\n", ((string)$response->getBody()));
            }
        );

        static::$responses[$this->hash_prefix] = $this->response;
    }

    public function getResponse() : array {
        return $this->response;
    }

    public function getMatches(int $matches_threshold): array {
        $hash_suffix = strtoupper(substr($this->hashed_password, 5));

        $matches_within_threshold = [];

        foreach($this->response as $hash) {
            list($suffix, $matches) = explode(':', $hash);

            if ($hash_suffix == $suffix && $matches >= $matches_threshold) {
                $matches_within_threshold[] = [
                    'suffix' => $suffix,
                    'matches' => $matches
                ];
            }
        }

        return $matches_within_threshold;
    }
}
