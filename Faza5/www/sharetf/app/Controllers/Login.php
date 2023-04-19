<?php

namespace App\Controllers;

use App\Models\Korisnik;
use App\Models\ZahtevZaRegistraciju;

class Login extends BaseController
{
    public function index()
    {
        //vraca login stranicu;
        $data = ["register" => false, "success" => false];
        echo view('pages/login', $data);
        return;
    }
    public function login()
    {
        //proverava korisnicko ime i lozinku
        //ako je ok prelazi na feed, u suprotnom prikazuje odgovarajucu poruku
        $data = ["register" => false, "success" => false];
        if (!$this->validate('login')) {
            $data['errors'] = $this->validator->getErrors();
            echo view('pages/login', $data);
            return;
        }
        else {
            //ovde treba u sesiji zapamtiti ulogovanog korisnika
            $k = new Korisnik();
            $user = $k->getUser($this->request->getVar('logemail'));
            $user2 = ['id' => $user['IdK'], 'img' => $user['Slika'], 'name' => $user['Ime'] . ' ' . $user['Prezime'], 'type' => $user['Tip'], 'text' => $user['Opis']];
            $this->session->set('user', $user2);
            return redirect()->to(site_url("User/feed"));
        }
    }
    public function register()
    {
        //proverava sva polja za registraciju
        //ako je ok pamti u bazi
        //prikazuje odgovarajucu poruku
        if (!$this->validate('register')) {
            $data = ['register' => true, 'success' => false, 'errors' => $this->validator->getErrors()];
            echo view('pages/login', $data);
            return;
        }
        else {
            //ovde treba evidentirati novog korisnika
            $z = new ZahtevZaRegistraciju();
            $id = $z->addRequest($this->request->getVar('name'), $this->request->getVar('lastname'), $this->request->getVar('email'), $this->request->getVar('password'), 'tmp');
            $file = $this->request->getFile('img');
            if ($file->isValid()) {
                $img = 'zahtev-' . $id . '.' . $file->getClientExtension();
                $file->move('/wamp64/www/uploads', $img);
                $img = "/wamp64/www/uploads/" . $img;
            } else $img = "/wamp64/www/uploads/default.jpg";
            $z->setImg($id, $img);
            $data = ['register' => false, 'success' => true];
            echo view('pages/login', $data);
            return;
        }
    }
}
