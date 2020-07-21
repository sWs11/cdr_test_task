<?php

namespace App\Controllers;

class MainController {
    public function index() {
        include VIEWS_DIR . '/Main/index.php';
    }

    public function loadFile() {

        var_dump($_SERVER);
        var_dump($_FILES);
        var_dump($_REQUEST);

    }
}