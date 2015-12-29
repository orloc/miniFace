<?php

namespace MiniFace\Controller;

use Doctrine\DBAL\Connection;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class MainController
 * @package MiniFace\Controller
 */
class MainController implements ControllerProviderInterface {

    protected $app;

    public function __construct(Application $app){
        $this->app = $app;
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app){
        $controllers = $app['controllers_factory'];

        $controllers->get('/', [$this, 'defaultAction']);

        $controllers->get('/api/myUser', [$this, 'getMyUserAction']);

        $controllers->get('/api/posts', [$this, 'getPostsAction']);
        $controllers->post('/api/status', [$this, 'postStatusAction']);

        $controllers->post('/api/friends', [$this, 'addFriendAction']);
        $controllers->get('/api/friends', [$this, 'getFriendsAction']);

        $controllers->get('/api/countFriends', [$this, 'countFriendsAction']);
        $controllers->get('/api/degrees', [$this, 'getConnectionDegreesAction']);


        return $controllers;
    }

    /**
     * Default page action
     * @return mixed
     */
    public function defaultAction(){
        return $this->app['twig']->render('index.html.twig');
    }

    public function getMyUserAction(){
        $myUser = $this->getMyUser();

        return new JsonResponse($myUser, 200);
    }

    public function postStatusAction(Request $request){
        // I AM USER 1
        $content = $request->request->get('status', false);
        $db = $this->app['db'];

        $now = Date('Y-m-d H:i:s');

        $stmt = $db->prepare("INSERT INTO posts (status, user_id, created_at) VALUES (:content, :user_id, :created_at)");
        $stmt->execute(['content' => $content, 'user_id' => $this->getMyUser()['id'], 'created_at' => $now]);

        return new JsonResponse(['id' => $db->lastInsertId(), 'status' => $content, 'created_at' => $now], 200);
    }

    public function getPostsAction(){
        $db = $this->app['db'];
        $myUser = $this->getMyUser();

        $stmt = $db->prepare("select * from user_friends where friending_user_id = :userId");
        $stmt->bindValue("userId", $myUser['id']);
        $stmt->execute();

        $friends = $stmt->fetchAll();

        $newFriends = [];
        if (!empty($friends)){
            $tmp = array_map(function($f){
                return $f['friended_user_id'];
            }, $friends);

            $newFriends = $tmp;
        }

        $stmt = $db->executeQuery("select * from posts where user_id IN (?)",
            [array_merge($newFriends, [$myUser['id']])],
            [Connection::PARAM_INT_ARRAY]
        );

        $posts = $stmt->fetchAll();


        $friendReference = $db->executeQuery("select * from users where id IN (?)",
            [array_merge($newFriends, [$myUser['id']])],
            [Connection::PARAM_INT_ARRAY]
        )->fetchAll();

        foreach ($posts as $k => $p){
            foreach ($friendReference as $f){
                if ($p['user_id'] === $f['id']){
                    $posts[$k]['user'] = $f;
                }
            }
        }

        return new JsonResponse($posts);

    }

    public function countFriendsAction(){
        $stmt = $this->app['db']->prepare("SELECT count(*) as friend_count from user_friends where friending_user_id = :userId");
        $stmt->bindValue("userId", $this->getMyUser()['id']);
        $stmt->execute();

        $count = $stmt->fetch();

        return new JsonResponse(['friend_count' => $count['friend_count']]);
    }

    public function getConnectionDegreesAction(){

    }

    public function addFriendAction(Request $request){
        $name = $request->request->get('name', false);

        $db = $this->app['db'];
        $user = $this->getMyUser();
        $now = Date('Y-m-d H:i:s');

        $stmt = $db->prepare("INSERT INTO users (name, created_at) VALUES(:name, :created_at)");
        $stmt->execute(['name' => $name, 'created_at' => $now]);

        $uId = $db->lastInsertId();

        $friendStmt = $db->prepare("INSERT INTO user_friends (friending_user_id, friended_user_id) VALUES(:myUser, :friendUser)");
        $friendStmt->execute(['myUser' => $user['id'], 'friendUser' => $uId]);

        return new JsonResponse(['id' => $uId, 'name' => $name, 'created_at' => $now], 200);

    }

    public function getFriendsAction(){
        $user = $this->getMyUser();

        $stmt = $this->app['db']->prepare("
          SELECT u.*
          FROM users AS u
          INNER JOIN user_friends AS uf ON u.id=uf.friended_user_id
          WHERE uf.friending_user_id = :user_id
          GROUP BY u.id
          "
        );

        $stmt->bindValue("user_id", $user['id']);
        $stmt->execute();

        $res = $stmt->fetchAll();

        return new JsonResponse($res);
    }

    protected function getMyUser(){
        $stmt = $this->app['db']->prepare('select * from users order by created_at ASC limit 1');
        $stmt->execute();

        return $stmt->fetch();
    }
}
