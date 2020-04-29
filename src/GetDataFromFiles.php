<?php

namespace GetData;

use function DI\get;

function getData()
{
    $users = [];
    $string = implode('}\n{', explode('}{', file_get_contents(getNormalisedPath("users.json"))));
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
    return file_put_contents(getNormalisedPath("users.json"), json_encode($resultUser), FILE_APPEND);
}


function getNormalisedPath($fileName)
{
    $absolutePath = realpath(__DIR__ . "/../datebase");
    $arrayPath = explode('/', $absolutePath);
    $arrayPath[] = $fileName;
    $normalizedPath = implode(DIRECTORY_SEPARATOR, $arrayPath);
    return $normalizedPath;
}
