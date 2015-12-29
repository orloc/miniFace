<?php

namespace MiniFace\Controller;

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

    public function postStatusAction(Request $request){
        // I AM USER 1
        $content = $request->request->get('status', false);
        $db = $this->app['db'];

        if ($content === false){
            // err
        } else {
            $content = $db->quote($content);
        }

        $now = $db->quote(Date('Y-m-d H:i:s'));

        $sql = "INSERT INTO posts (status, user_id, created_at) VALUES ($content, 1, $now)";
        $db->query($sql);

        return new JsonResponse(['id' => $db->lastInsertId(), 'status' => $content, 'created_at' => $now], 200);
    }

    public function getPostsAction(){
        $db = $this->app['db'];
        $myUser = $this->getMyUser();

        $friends = $db->query("
          select friended_user_id as friend_id
          from user_friends
          where friending_user_id = {$myUser['id']}")->fetchAll();

        if (!empty($friends)){
            $tmp = array_map(function($f){
                return $f['friend_id'];
            }, $friends);

            $friends = $tmp;
        }

        $userIds = array_merge($friends, [$myUser['id']]);

        $q = "select * from posts where user_id IN";
        $ids = implode(",", $userIds);
        $q .= "($ids)";

        $posts = $db->query($q)->fetchAll();

        return new JsonResponse($posts);

    }

    public function countFriendsAction(){

    }

    public function getConnectionDegreesAction(){

    }

    public function addFriendAction(Request $request){


    }

    public function getFriendsAction(){
        // i am user 1
        $db = $this->app['db'];
        $sql = 'SELECT * from (
          SELECT friend
        )';

    }

    protected function getMyUser(){
        $q = 'select * from users order by created_at DESC limit 1';

        return $this->app['db']->query($q)->fetch();
    }
}
