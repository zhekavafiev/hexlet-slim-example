<?php

namespace GetData;

use function DI\get;

function getData()
{
    $users = [];
    $string = implode('}\n{', explode('}{', file_get_contents("/home/evg/hexlet-slim-example/datebase/users.json")));
    $arrayFromDataBase = explode('\n', $string);
    foreach ($arrayFromDataBase as $user) {
        $dataUser = json_decode($user, true);
        foreach ($dataUser as $id => $userInfo) {
            $users[$id] = $userInfo;
        }
    }

    return $users;
}

function putData($user)
{
    $resultUser[$user['id']] = $user;
    return file_put_contents("/home/evg/hexlet-slim-example/datebase/users.json", json_encode($resultUser), FILE_APPEND);
}
