<?php

$app = require_once __DIR__.'/app.php';

$db = $app['db'];
$faker = \Faker\Factory::create();

// Add some users
$db->query(getUserQuery($db, $faker));

// pseudo-randomly get friends for each user
$users = generateFriends(getUsers($db));

generatePosts($db, $users, $faker);

$q = getUpdateFriendsQuery($users);

$db->query($q);

function generatePosts($db, array $users, $faker){
    $sql = "INSERT INTO posts (status, user_id, created_at) VALUES ";
    $values = [];
    foreach ($users as $u){
        for ($i = 0; $i < 4; $i++) {
            $p = $db->quote($faker->realText(45));
            $now = $db->quote(Date('Y-m-d H:i:s'));
            $values[] = "($p, {$u['id']}, $now)";
        }
    }

    $sql .= implode(', ', $values);

    $db->query($sql);

}

function getUpdateFriendsQuery(array $users){
    $sql = "INSERT INTO user_friends (friending_user_id, friended_user_id) VALUES ";
    $valStr = [];
    foreach ($users as $u ){
        foreach ($u['friends'] as $fId){
            if ($u['id'] === $fId){ // skip myself
                continue;
            } else {
                $valStr[] = "({$u['id']}, {$users[$fId]['id']})";
            }
        }
    }

    $sql .= implode(",", $valStr);

    return $sql;
}

function generateFriends(array $users){
    $tmp = $users;
    foreach ($tmp as $k => $u){
        // assign a random  to friends
        $friend_keys = array_rand(
            $users,
            count($users) / mt_rand(1,count($users)/3)
        );

        $tmp[$k]['friends'] = $friend_keys;
    }
    return $tmp;
}

function getUsers($db){
    $sql = "SELECT id  as id FROM users";
    $stmt =  $db->query($sql);
    return $stmt->fetchAll();
}

function getUserQuery($db, $faker, $target = 100){
    $sql = "INSERT INTO users (name, created_at) VALUES ";
    $count = 0;
    $values = [];
    while ($count <= $target){
        $now = $db->quote(Date('Y-m-d H:i:s'));
        $name = $db->quote($faker->name);

        $values[] = "($name, $now)";
        $count++;
    }

    $sql .= implode(",", $values);

    return $sql;
}
