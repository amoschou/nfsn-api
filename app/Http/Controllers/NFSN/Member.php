<?php

namespace App\Http\Controllers\NFSN;

use Illuminate\Http\Request;

class Member extends Controller
{
    public function __construct($id) {
        parent::__construct();
        self::$object = 'member';
        $this->id = $id;
    }

    // NFSN PROPERTIES

    public function getAccounts() {
        return json_decode($this->request('get', 'accounts'), true);
    }

    public function sites() {
        return json_decode($this->request('get', 'sites'), true);
    }
}
