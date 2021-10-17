<?php

namespace App\Http\Controllers\NFSN;

use Illuminate\Http\Request;

/*
 * $dns = new DNS('my.domain.com');
 * 
 * PROPERTIES
 *
 * $dns->expire();          // Gets the "expire" value for the domain.
 * $dns->expire($expire);   // Sets the "expire" value.
 * $dns->minTTL();          // Gets the "minTTL" value for the domain.
 * $dns->minTTL($minTTL);   // Sets the "minTTL" value.
 * $dns->refresh();         // Gets the "refresh" value for the domain.
 * $dns->refresh($refresh); // Sets the "refresh" value.
 * $dns->retry();           // Gets the "retry" value for the domain.
 * $dns->retry($retry);     // Sets the "retry" value.
 * $dns->serial();          // Gets the "serial" value for the domain. It can't be set.
 *
 * METHODS
 *
 * $dns->updateSerial();       // Updates the serial of the domain.
 *
 * $dns->listRRs($parameters); // Returns an array of RRs that match the given parameters.
 *     $parameters is an array of the form:
 *     ['name' => '', 'type' => '', 'data' => '']
 *     where each key is optional.
 *
 * $dns->addRRs($input);       // Adds new RRs. e.g.:
 * 
 *     $dns->addRRs([
 *         ['name' => 'a', 'type' => 'CNAME', 'data' => 'example.com'],
 *         ['name' => 'b', 'type' => 'A', 'data' => '10.20.30.40'],
 *         ['name' => 'c', 'type' => 'A', 'data' => '11.21.31.41', 'ttl' => 3600],
 *     ]); // Adds three RRs.
 * 
 *     $dns->addRRs(['name' => 'a', 'type' => 'CNAME', 'data' => 'example.com']);
 *         // Adds one RR.
 *
 * $dns->removeRRs($input);      // Removes RRs. e.g.:
 * 
 *     $dns->removeRRs([
 *         ['name' => 'a', 'type' => 'CNAME', 'data' => 'example.com'],
 *         ['name' => 'b', 'type' => 'A', 'data' => '10.20.30.40'],
 *         ['name' => 'c', 'type' => 'A', 'data' => '11.21.31.41'],
 *     ]); // Removes three RRs.
 * 
 *     $dns->removeRRs(['name' => 'a', 'type' => 'CNAME', 'data' => 'example.com']);
 *         // Remoevs one RR.
 * 
 * $dns->replaceRRs($input);     // Replaces RRs. e.g.:
 * 
 *     $dns->replaceRRs([
 *         ['name' => 'a', 'type' => 'CNAME', 'data' => 'example.com'],
 *         ['name' => 'b', 'type' => 'A', 'data' => '10.20.30.40'],
 *         ['name' => 'c', 'type' => 'A', 'data' => '11.21.31.41', 'ttl' => 3600],
 *     ]); // Replaces three RRs
 */

class DNS extends Controller
{
    public function __construct($id) {
        parent::__construct();
        self::$object = 'dns';
        $this->id = $id;
    }

    // HIGHER LEVEL PROPERTIES

    public function expire($expire = null) {
        if(is_null($expire)) {
            return $this->getExpire();
        }

        return $this->setExpire($expire);
    }

    public function minTTL($minTTL = null) {
        if(is_null($minTTL)) {
            return $this->getMinTTL();
        }

        return $this->setMinTTL($minTTL);
    }

    public function refresh($refresh = null) {
        if(is_null($refresh)) {
            return $this->getRefresh();
        }

        return $this->setRefresh($refresh);
    }

    public function retry($retry = null) {
        if(is_null($retry)) {
            return $this->getRetry();
        }

        return $this->setRetry($retry);
    }

    public function serial() {
        return $this->getSerial();
    }

    // NFSN PROPERTIES (RAW)

    private function getExpire() {
        $response = $this->request('get', 'expire');

        if($response->ok()) {
            return $response->json();
        }

        return null;
    }

    private function setExpire($expire) {
        $response = $this->request('put', 'expire', $expire);

        if($response->ok()) {
            return true;
        }

        return null;
    }

    private function getMinTTL() {
        $response = $this->request('get', 'minTTL');

        if($response->ok()) {
            return $response->json();
        }

        return null;
    }

    private function setMinTTL($minTTL) {
        $response = $this->request('put', 'minTTL', $minTTL);

        if($response->ok()) {
            return true;
        }

        return null;
    }

    private function getRefresh() {
        $response = $this->request('get', 'refresh');

        if($response->ok()) {
            return $response->json();
        }

        return null;
    }

    private function setRefresh($refresh) {
        $response = $this->request('put', 'refresh', $refresh);

        if($response->ok()) {
            return true;
        }

        return null;
    }

    private function getRetry() {
        $response = $this->request('get', 'retry');

        if($response->ok()) {
            return $response->json();
        }

        return null;
    }

    private function setRetry($retry) {
        $response = $this->request('put', 'retry', $refresh);

        if($response->ok()) {
            return true;
        }

        return null;
    }

    private function getSerial() {
        $response = $this->request('get', 'serial');

        if($response->ok()) {
            return $response->json();
        }

        return null;
    }

    // HIGHER LEVEL METHODS

    public function addRRs($input) {
        $return = [];

        foreach((is_scalar($input[array_key_first($input)]) ? [$input] : $input) as $i) {
            $return[] = $this->addRRRaw($i);
        }

        return $return;
    }

    public function listRRs($input = []) {
        return $this->listRRsRaw($input);
    }

    public function removeRRs($input) {
        $return = [];

        foreach((is_scalar($input[array_key_first($input)]) ? [$input] : $input) as $i) {
            $return[] = $this->removeRRRaw($i);
        }

        return $return;
    }

    public function updateSerial() {
        return $this->updateSerialRaw();
    }

    // NFSN METHODS (RAW)

    private function addRRRaw($input) {
        // name, type, data: mandatory
        // ttl: optional

        $response = $this->request('post', 'addRR', $input, ['name', 'type', 'data', 'ttl']);

        if($response->ok()) {
            return true;
        }

        return null;
    }

    private function listRRsRaw($input = []) {
        // name, type, data: optional
        $response = $this->request('post', 'listRRs', $input, ['name', 'type', 'data']);

        if($response->ok()) {
            return $response->json();
        }

        return null;
    }

    private function removeRRRaw($input) {
        $response = $this->request('post', 'removeRR', $input, ['name', 'type', 'data']);

        if($response->ok()) {
            return true;
        }

        return null;
    }

    private function replaceRRRaw($input) {
        // name, type, data: mandatory
        // ttl: optional

        $response = $this->request('post', 'replaceRR', $input, ['name', 'type', 'data', 'ttl']);

        if($response->ok()) {
            return true;
        }

        return null;
    }

    private function updateSerialRaw() {
        return $this->request('post', 'updateSerial');
    }
}
