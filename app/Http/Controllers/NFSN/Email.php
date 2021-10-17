<?php

namespace App\Http\Controllers\NFSN;

use Illuminate\Http\Request;

/*
 * $email = new Email('my.domain.com');
 * 
 * $email->listForwards(); // Returns an array of all fowards defined on the domain, or null on failure.
 *
 * $email->setForwards($list); // Sets forwards for each element of $list. e.g.:
 *   $email->setForwards(['a' => 'newa@example.com', 'b' => 'newb@example.com']);
 *   Return value currently not documented and subject to change.
 *
 * $email->removeForwards($list); // Removes the forwards for each element of $list. e.g.:
 *   $email->removeForwards(['a', 'b']); // Removes 'a@my.domain.com' and 'b@my.domain.com'.
 *   Return value currently not documented and subject to change.
 */

class Email extends Controller
{
    private static $bounce;
    private static $discard;

    public function __construct($id) {
        parent::__construct();
        self::$object = 'email';
        self::$bounce = 'bounce@email.nearlyfreespeech.net';
        self::$discard = 'discard@email.nearlyfreespeech.net';
        $this->id = $id;
    }

    // HIGHER LEVEL METHODS

    public function listForwards() {
        return $this->listForwardsRaw();
    }

    public function removeForwards($input) {
        $return = [];

        foreach((is_string($input) ? [$input] : $input) as $forward) {
            $return[] = $this->removeForwardRaw($forward);
        }

        return $return;
    }

    public function setForwards($input) {
        $return = [];

        foreach($input as $forward => $destEmail) {
            if(in_array($destEmail, ['bounce', 'discard'])) {
                $destEmail = self::${$destEmail};
            }

            $return[$forward] = $this->setForwardRaw([
                'forward' => $forward,
                'dest_email' => $destEmail
            ]);
        }

        return $return;
    }

    // NFSN METHODS (RAW)

    private function listForwardsRaw() {
        $response = $this->request('post', 'listForwards');

        if($response->ok()) {
            return $response->json();
        }

        return null;
    }

    private function removeForwardRaw($input) {
        return $this->request('post', 'removeForward', $input, 'forward');
    }

    private function setForwardRaw($input) {
        return $this->request('post', 'setForward', $input, ['forward', 'dest_email']);
    }

}
