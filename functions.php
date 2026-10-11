<?php
function getUsers()
{
    $folder = dirname(USERS_FILE);

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    if (!file_exists(USERS_FILE)) {
        file_put_contents(USERS_FILE, '[]');
    }

    $isiFile = file_get_contents(USERS_FILE);
    $users = json_decode($isiFile, true);

    if (!is_array($users)) {
        return array();
    }

    return $users;
}

function saveUsers($users)
{
    $json = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    $hasil = file_put_contents(USERS_FILE, $json, LOCK_EX);

    if ($hasil === false) {
        return false;
    }

    return true;
}

function findUserByEmail($email, $users)
{
    foreach ($users as $user) {
        if (strtolower($user['email']) == strtolower($email)) {
            return $user;
        }
    }
    return null;
}

function generateNextId($users)
{
    if (count($users) == 0) {
        return 1;
    }

    $semuaId = array_column($users, 'id');

    return max($semuaId) + 1;
}

function updateUser($id, $dataBaru)
{
    $users = getUsers();

    foreach ($users as $index => $user) {
        if ($user['id'] == $id) {
            $users[$index] = array_merge($user, $dataBaru);

            return saveUsers($users);
        }
    }

    return false;
}

function clean($teks)
{
    $teks = trim($teks);
    $teks = htmlspecialchars($teks, ENT_QUOTES, 'UTF-8');

    return $teks;
}
