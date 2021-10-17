<?php

namespace App\Http\Controllers\NFSN;

use Illuminate\Http\Request;

class Account extends Controller
{
    public function __construct($id) {
        parent::__construct();
        self::$object = 'account';
        $this->id = $id;
    }

    // NFSN PROPERTIES

    public function getBalance() {
        return $this->request('get', 'balance')->body();
    }

    public function getBalanceCash() {
        return $this->request('get', 'balanceCash')->body();
    }

    public function getBalanceCredit() {
        return $this->request('get', 'balanceCredit')->body();
    }

    public function getBalanceHigh() {
        return $this->request('get', 'balanceHigh')->body();
    }

    public function getFriendlyName() {
        return $this->request('get', 'friendlyName')->body();
    }

    public function setFriendlyName($name) {
        return $this->request('put', 'friendlyName', $name)->body();
    }

    public function getStatus() {
        return json_decode($this->request('get', 'status'), true);
    }

    public function getSites() {
        return json_decode($this->request('get', 'sites'), true);
    }

    // NFSN METHODS

    public function addSite($input) {
        return $this->request('post', 'addSite', ensureArray($input, 'site'), ['site'])->body();
    }

    public function addWarning($input) {
        return $this->request('post', 'addWarning', ensureArray($input, 'balance'), ['balance'])->body();
    }

    public function removeWarning($input) {
        return $this->request('post', 'removeWarning', ensureArray($input, 'balance'), ['balance'])->body();
    }
}
