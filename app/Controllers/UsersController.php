<?php
namespace App\Controllers;

use App\Database;
use App\Redirect;
use App\View;

class UsersController extends Database
{

    public function index(): View
    {
        return new View('Users/index');
    }

    public function show(): View
    {
        return new View('Users/show');
    }

}