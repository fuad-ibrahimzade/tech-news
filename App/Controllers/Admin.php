<?php

namespace App\Controllers;

use App\Config;
use App\Models\About;
use App\Models\Advertisements;
use App\Models\Comments;
use App\Models\Contacts;
use App\Models\CustomAnalytics;
use App\Models\Pages;
use App\Models\Posts;
use App\Models\SocialLinks;
use App\Models\User;
use Carbon\Carbon;
use Core\Router;
use \Core\View;
use \Firebase\JWT\JWT;
use PDO;

class Admin extends \Core\Controller
{
    public function __construct($route_params)
    {
        parent::__construct($route_params);
        \Cloudinary::config(Config::ClOUDINARY_config_array);
    }
    public function __invoke($method,$arguments) {
    }

    public function login($requestData=null)
    {
        if(!User::getAll()){
            User::createAdmin();
        }

        if (false && Admin::hasLoggedIn()) {
            return $this->json(['message' => 'Already logged in']);;
        }
        else {
            $user = User::getUserByEmail($requestData->email);
            $pass = (!empty($user)) && isset($user);
            if ($pass) {
                $pass = password_verify($requestData->password, $user->password);
            }
            if ($pass) {
                $_SESSION['user'] = [
                    "email" => $user->email,
                    'api_token' => $user->api_token
                ];

                $data = array(
                    "access_token" => $user->api_token,
                    "email" => $user->email,
                );
                $key= $user->api_token;
                $jwt = $this->getEncodedJWT($data,$key);
                return $this->json(['jwt' => $jwt]);
            }
            echo $pass ? "OK" : "ERR" ;
        }
		
        return $this->json(['message' => 'Something went wrong']);
    }

    public function logout($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>json_encode($this->isAuthorized()).' Already Logged out'
        );
        $jwt = $this->getEncodedJWT($data,$key);
        if($this->isAuthorized()){
            $data = array(
                'message'=>'Logged out after updating tokens'
            );
            $jwt = $this->getEncodedJWT($data,$key);
            unset($_SESSION['user']);
            User::update([
                'email'=>Config::ADMIN_EMAIL,
                'api_token'=>hash("sha256", rand())
            ]);
            return $this->json(['jwt'=>$jwt]);
        }
        return $this->json(['jwt'=>$jwt]);
    }

    public static function hasLoggedIn($requestData=null)
    {
        if (isset($_SESSION['user']) && (!empty($_SESSION['user'])) && is_array($_SESSION['user'])) {
            return true;
        }
        return false;
    }

    public function index($requestData=null)
    {
        $users=User::getAll()[0];
        if(isset($users) && !empty($users)){
            return $this->json($users);
        }
        else{
            return $this->json(array("message" => "No users found."));
        }
    }

    public function list_pages($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Pages listed'
        );
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $data['pages'] = Pages::getAll();
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }
    public function create_page($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem Page not created'
        );
        if($this->isAuthorized() && isset($requestData->page)){
            $data['message'] = 'Page created';
            Pages::create($requestData->page);
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }
    public function read_page($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem Page not read'
        );
        if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($requestData['id'])){
            $data['message'] = 'Page read';
            $data['page'] = Pages::getById($requestData['id']);
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }
    public function update_page($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem Page not updated'
        );
        if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($requestData->id)){
            $data['message'] = 'Page Retrieved';
            $data['page'] = (Pages::getById($requestData->id));
        }
        else if($this->isAuthorized() && isset($requestData->page)){
            $data['message'] = 'Page updated';
            Pages::update([
                'id'=>$requestData->page->id,
                'name'=>$requestData->page->name,
                'show_at_home'=>$requestData->page->show_at_home,
                'showing_order'=>$requestData->page->showing_order,
                'date_upd'=>Carbon::now()
            ]);
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }
    public function delete_page($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem Page not deleted'
        );
        if($this->isAuthorized() && isset($requestData->id)){
            $data['message'] = 'Page deleted';
            $deleteMessage=Pages::delete($requestData->id);
            if($deleteMessage!==true){
                $data['message']=$deleteMessage;
            }
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }

    public function upload_image($requestData=null){

        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>json_encode($requestData).' Problem File not uploaded to cloudinary'
        );
        $fileseISSET=isset($_FILES['file'])&&isset($_FILES['file']['name']);
        if($fileseISSET!==false && $this->isAuthorized()){
            $data['message'] = 'File uploaded to cloudinary';
            $cloudnaryFile=\Cloudinary\Uploader::upload($_FILES["file"]["tmp_name"]);
            $imgSRC=cloudinary_url($cloudnaryFile['public_id']);
            $data['src']=$imgSRC;
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }
    public function delete_image($requestData=null){

        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem File not deleted from cloudinary'
        );
        if($this->isAuthorized() && isset($requestData->src)){
            $data['message'] = 'File deleted from cloudinary';
            
            $public_idOLD=preg_replace('/http:\/\/res\.cloudinary\.com\/.*\/image\/upload\//','',$requestData->src);
            $deleteMessage=\Cloudinary\Uploader::destroy($public_idOLD);
            $data['message']=$deleteMessage;
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }

    public function list_featured_posts($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Featured Posts listed'
        );
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            if(isset($requestData['post_category'])){
                $data['posts'] = Posts::getByWhereLimit('post_category',$requestData['post_category']);
            }
            else{
                $data['posts'] = Posts::getRandomLimit(2);
            }
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }
    public function list_posts($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Posts listed'
        );
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            if(isset($requestData['post_category'])){
                $data['posts'] = Posts::getByWhere('post_category',$requestData['post_category']);
            }
            else{
                $data['posts'] = Posts::getAll();
            }
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }
    public function create_post($requestData=null)
    {
        try {
            $requestData=json_decode(json_encode($requestData));
        } catch (\Throwable $th) {
            throw $th;
        }
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem Post not created'
        );
        if($this->isAuthorized() && isset($requestData)){
            $data['message'] = 'Post created';
            $post = $requestData;
            $cloudnaryFile=\Cloudinary\Uploader::upload($_FILES['author_picture']['tmp_name']);
            $imgSRC=cloudinary_url($cloudnaryFile['public_id']);
            $post->author_picture=$imgSRC;

            $cloudnaryFile2=\Cloudinary\Uploader::upload($_FILES['post_picture']['tmp_name']);
            $imgSRC2=cloudinary_url($cloudnaryFile2['public_id']);
            $post->post_picture=$imgSRC2;

            Posts::create($post);
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }
    public function read_post($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem Post not read'
        );
        if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($requestData['id'])){
            $data['message'] = 'Post read';
            $data['post'] = Posts::getById($requestData['id']);
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }
    public function update_post($requestData=null)
    {
        try {
            $requestData=json_decode(json_encode($requestData));
        } catch (\Throwable $th) {
            throw $th;
        }
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem Post not updated'
        );
        if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($requestData->id)){
            $data['message'] = 'Post Retrieved';
            $data['post'] = (Posts::getById($requestData->id));
        }
        else if($this->isAuthorized() && isset($requestData)){
            $data['message'] = 'Post updated';
            $post = $requestData;
            if(isset($_FILES['author_picture']) && isset($_FILES['author_picture']['tmp_name'])){
                $postOLD=Posts::getById($requestData->id);
                $public_idOLD=preg_replace('/http:\/\/res\.cloudinary\.com\/.*\/image\/upload\//','',$postOLD->author_picture);
                $deleteMessageC=\Cloudinary\Uploader::destroy($public_idOLD);

                $cloudnaryFile=\Cloudinary\Uploader::upload($_FILES['author_picture']['tmp_name']);
                $imgSRC=cloudinary_url($cloudnaryFile['public_id']);
                $post->author_picture=$imgSRC;
            }
            if(isset($_FILES['post_picture']) && isset($_FILES['post_picture']['tmp_name'])){
                $postOLD=Posts::getById($requestData->id);
                $public_idOLD=preg_replace('/http:\/\/res\.cloudinary\.com\/.*\/image\/upload\//','',$postOLD->post_picture);
                $deleteMessageC=\Cloudinary\Uploader::destroy($public_idOLD);

                $cloudnaryFile=\Cloudinary\Uploader::upload($_FILES['post_picture']['tmp_name']);
                $imgSRC=cloudinary_url($cloudnaryFile['public_id']);
                $post->post_picture=$imgSRC;
            }

            $post->date_upd=Carbon::now();
            Posts::update($post);
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);

    }
    public function delete_post($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem Post not deleted'
        );
        if($this->isAuthorized() && isset($requestData->id)){
            $data['message'] = 'Post deleted';

            $post=Posts::getById($requestData->id);
            $public_idOLD=preg_replace('/http:\/\/res\.cloudinary\.com\/.*\/image\/upload\//','',$post->author_picture);
            $deleteMessageC=\Cloudinary\Uploader::destroy($public_idOLD);

            $public_idOLD2=preg_replace('/http:\/\/res\.cloudinary\.com\/.*\/image\/upload\//','',$post->post_picture);
            $deleteMessageC2=\Cloudinary\Uploader::destroy($public_idOLD2);

            $deleteMessage=Posts::delete($requestData->id);
            if($deleteMessage!==true){
                $data['message']=$deleteMessage.' , cloudinary delete1: '.$deleteMessageC.' , cloudinary delete2: '.$deleteMessageC2;
            }


        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }

    public function read_distinct_tags($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Tags read'
        );
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $data['post_tags'] = Posts::getDistinctColumn('post_tags');
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }

    public function list_comments($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Comments listed'
        );
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            if(isset($requestData['post_id'])){
                $data['comments'] = Comments::getByWhere('post_id',$requestData['post_id']);
            }
            else{
                $data['comments'] = Comments::getAll();
            }
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }
    public function create_comment($requestData=null)
    {
        try {
            $requestData=json_decode(json_encode($requestData));
        } catch (\Throwable $th) {
            throw $th;
        }
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem Comment not created'
        );
        if(isset($requestData)){
            $data['message'] = 'Comment created';
            $comment = $requestData;
            $data['comment_received']=$comment;
            
            if(isset($comment->parent_comment_id) && $comment->parent_comment_id!=='-1'){
                $comment_created=Comments::create($comment);
                $data['comment_created']=$comment_created;

                $parentComment=Comments::getById($comment->parent_comment_id);
                $child_comment_ids=(array)json_decode($parentComment->child_comment_ids,true);
                $child_comment_ids[]=$comment_created->id;
                $parentComment->child_comment_ids=json_encode($child_comment_ids);
                Comments::update($parentComment);
            }
        }
        return $this->json(['jwt'=>$data]);
    }
    public function read_comment($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem Comment not read'
        );
        if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($requestData['id'])){
            $data['message'] = 'Comment read';
            $data['comment'] = Comments::getById($requestData['id']);
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }
    public function update_comment($requestData=null)
    {
        try {
            $requestData=json_decode(json_encode($requestData));
        } catch (\Throwable $th) {
            throw $th;
        }
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem Comment not updated'
        );
        if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($requestData->id)){
            $data['message'] = 'Comment Retrieved';
            $data['comment'] = (Comments::getById($requestData->id));
        }
        else if($this->isAuthorized() && isset($requestData)){
            $data['message'] = 'Comment updated';
            $comment = $requestData;

            $comment->date_upd=Carbon::now();
            Comments::update($comment);
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);

    }
    public function delete_comment($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem Comment not deleted'
        );
        if($this->isAuthorized() && isset($requestData->id)){
            $data['message'] = 'Comment deleted';

            $comment=Comments::getById($requestData->id);;
            if(isset($comment->parent_comment_id) && $comment->parent_comment_id!=='-1'){
                $parentComment=Comments::getById($comment->parent_comment_id);
                $child_comment_ids=(array)json_decode($parentComment->child_comment_ids,true);
                unset($child_comment_ids[array_search($comment->id,$child_comment_ids)]);
                $parentComment->child_comment_ids=json_encode($child_comment_ids);
                Comments::update($parentComment);
            }
            $child_comment_ids=(array)json_decode($comment->child_comment_ids,true);
            if(count($child_comment_ids)>0){
                $comment->message='deleted message';
                Comments::update($comment);
                $data['message']='parent comment replaced';
                $jwt = $this->getEncodedJWT($data,$key);
                return $this->json(['jwt'=>$jwt]);
            }
            $deleteMessageC='';

            $deleteMessageC2='';

            $deleteMessage=Comments::delete($requestData->id);
            if($deleteMessage!==true){
                $data['message']=$deleteMessage.' , cloudinary delete1: '.$deleteMessageC.' , cloudinary delete2: '.$deleteMessageC2;
            }

        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }
    
    public function update_about($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem About not updated'
        );
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $data['message'] = 'About Retrieved';
            $data['editordata'] = (About::getAll()[0]->content);
        }
        else if($this->isAuthorized() && isset($requestData->editordata)){
            $data['message'] = 'About updated';
            About::update([
                'id'=>1,
                'content'=>($requestData->editordata)
            ]);
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }
    public function update_contacts($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem Contacts not updated'
        );
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $data['message'] = 'Contacts Retrieved';
            $data['editordata'] = (Contacts::getAll()[0]->content);
        }
        else if($this->isAuthorized() && isset($requestData->editordata)){
            $data['message'] = 'Contacts updated';
            Contacts::update([
                'id'=>1,
                'content'=>($requestData->editordata)
            ]);
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }
    public function update_social_links($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem Social Links not updated'
        );
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $data['message'] = 'Social Links Retrieved';
            $data['social_links'] = array(
                'facebook'=>SocialLinks::getByWhere('link_type','facebook')[0]->content,
                'twitter'=>SocialLinks::getByWhere('link_type','twitter')[0]->content,
                'telegram'=>SocialLinks::getByWhere('link_type','telegram')[0]->content
            );
        }
        else if($this->isAuthorized() && isset($requestData->social_links)){
            $data['message'] = 'Social Links updated';
            $social_links = array(
                'facebook'=>$requestData->social_links->facebook,
                'twitter'=>$requestData->social_links->twitter,
                'telegram'=>$requestData->social_links->telegram
            );
            foreach ($social_links as $key => $value) {
                SocialLinks::update([
                    'id'=>SocialLinks::getByWhere('link_type',$key)[0]->id,
                    'content'=>($social_links[$key])
                ]);
            }
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }

    public function create_analytics_user_visit($requestData=null)
    {
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem Custom Analytics not created'
        );
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($requestData->user_ip) && isset($requestData->request_uri)){

            $user_ip=$requestData->user_ip;
            $locationInfoJson=CustomAnalytics::get_location($user_ip);
            if(isset($locationInfoJson) && isset($locationInfoJson->country_name)){
                $analytics_data=[
                    'visited_page_link' => $requestData->request_uri,
                    'user_ip' => $locationInfoJson->ip,
                    'country' => $locationInfoJson->country_name,
                    'http_referer' => $_SERVER['HTTP_REFERER'],
                    'ip_data' => $locationInfoJson
                ];
                $custom_analitics_user_visit = CustomAnalytics::create((object)[
                    'analytics_type' => 'link_click',
                    'analytics_data' => json_encode($analytics_data)
                ]);
                $data['message'] = 'Custom Analytics created';
                $data['ca_user_visit'] = ($custom_analitics_user_visit);
            }
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }

    public function list_analytics_user_visit($requestData=null){
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'User Visits listed'
        );
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            if(isset($requestData['analytics_type'])){
                $data['analytics_data'] = CustomAnalytics::getByWhere('analytics_type',$requestData['analytics_type']);
            }
            else{
                $data['analytics_data'] = CustomAnalytics::getAll();
            }
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);
    }

    public function update_advertisements($requestData=null)
    {
        try {
            $requestData=json_decode(json_encode($requestData));
        } catch (\Throwable $th) {
            throw $th;
        }
        $key= User::getUserByEmail(Config::ADMIN_EMAIL)->api_token;
        $data = array(
            'message'=>'Problem Advertisements not updated'
        );
        if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($requestData->id)){
            $data['message'] = 'Advertisements Retrieved';
            $data['ads'] = (Advertisements::getById($requestData->id));
        }
        else if($this->isAuthorized() && isset($requestData)){
            $baseUrl='http://'.$_SERVER['SERVER_NAME'].'/';
            $data['message'] = 'Advertisements updated';
            $ads = $requestData;
            if(isset($_FILES['ads_picture_1']) && isset($_FILES['ads_picture_1']['tmp_name'])){
                $adsOLD=Advertisements::getById($requestData->id);
                if(strpos($adsOLD->ads_picture_1, $baseUrl) === false){
                    // yeni localhost ad-1 ve ad-2 jpgler deyil
                    $public_idOLD=preg_replace('/http:\/\/res\.cloudinary\.com\/.*\/image\/upload\//','',$adsOLD->ads_picture_1);
                    $deleteMessageC=\Cloudinary\Uploader::destroy($public_idOLD);
                }

                $cloudnaryFile=\Cloudinary\Uploader::upload($_FILES['ads_picture_1']['tmp_name']);
                $imgSRC=cloudinary_url($cloudnaryFile['public_id']);
                $ads->ads_picture_1=$imgSRC;
            }
            if(isset($_FILES['ads_picture_2']) && isset($_FILES['ads_picture_2']['tmp_name'])){
                $adsOLD=Advertisements::getById($requestData->id);
                if(strpos($adsOLD->ads_picture_2, $baseUrl) === false){
                    $public_idOLD=preg_replace('/http:\/\/res\.cloudinary\.com\/.*\/image\/upload\//','',$adsOLD->ads_picture_2);
                    $deleteMessageC=\Cloudinary\Uploader::destroy($public_idOLD);
                }

                $cloudnaryFile=\Cloudinary\Uploader::upload($_FILES['ads_picture_2']['tmp_name']);
                $imgSRC=cloudinary_url($cloudnaryFile['public_id']);
                $ads->ads_picture_2=$imgSRC;
            }

            $ads->date_upd=Carbon::now();
            Advertisements::update($ads);
        }
        $jwt = $this->getEncodedJWT($data,$key);
        return $this->json(['jwt'=>$jwt]);

    }

    public function create_defaults($requestData=null)
    {
        $defaultContent=('
        <h3 _ngcontent-sia-c6="" style="font-family: &quot;Nunito Sans&quot;, sans-serif; font-weight: 700; color: rgb(33, 38, 49); margin: 0px 0px 15px; font-size: 23px;">Lorem Ipsum: when, and when not to use it</h3><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">Do you like Cheese Whiz? Spray tan? Fake eyelashes? That\'s what is Lorem Ipsum to many—it rubs them the wrong way, all the way. It\'s unreal, uncanny, makes you wonder if something is wrong, it seems to seek your attention for all the wrong reasons. Usually, we prefer the real thing, wine without sulfur based preservatives, real butter, not margarine, and so we\'d like our layouts and designs to be filled with real words, with thoughts that count, information that has value.</p><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">The toppings you may chose for that TV dinner pizza slice when you forgot to shop for foods, the paint you may slap on your face to impress the new boss is your business. But what about your daily bread? Design comps, layouts, wireframes—will your clients accept that you go about things the facile way? Authorities in our business will tell in no uncertain terms that Lorem Ipsum is that huge, huge no no to forswear forever. Not so fast, I\'d say, there are some redeeming factors in favor of greeking text, as its use is merely the symptom of a worse problem to take into consideration.</p><figure _ngcontent-sia-c6="" class="figure-img" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;"><img _ngcontent-sia-c6="" alt="" class="img-responsive" src="http://localhost:4200/assets/img/post-4.jpg"><figcaption _ngcontent-sia-c6="" style="padding-top: 5px; font-size: 13px; font-weight: 600;">So Lorem Ipsum is bad (not necessarily)</figcaption></figure><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">You begin with a text, you sculpt information, you chisel away what\'s not needed, you come to the point, make things clear, add value, you\'re a content person, you like words. Design is no afterthought, far from it, but it comes in a deserved second. Anyway, you still use Lorem Ipsum and rightly so, as it will always have a place in the web workers toolbox, as things happen, not always the way you like it, not always in the preferred order. Even if your less into design and more into content strategy you may find some redeeming value with, wait for it, dummy copy, no less.</p><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">There\'s lot of hate out there for a text that amounts to little more than garbled words in an old language. The villagers are out there with a vengeance to get that Frankenstein, wielding torches and pitchforks, wanting to tar and feather it at the least, running it out of town in shame.</p><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">One of the villagers, Kristina Halvorson from Adaptive Path, holds steadfastly to the notion that design can’t be tested without real content:</p><blockquote _ngcontent-sia-c6="" class="blockquote" style="padding-top: 20px; padding-bottom: 20px; margin-bottom: 10px; border-left: 0px; position: relative; font-weight: 600; color: rgb(61, 69, 92); font-family: Nunito, sans-serif;">I’ve heard the argument that “lorem ipsum” is effective in wireframing or design because it helps people focus on the actual layout, or color scheme, or whatever. What kills me here is that we’re talking about creating a user experience that will (whether we like it or not) be DRIVEN by words. The entire structure of the page or app flow is FOR THE WORDS.</blockquote><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">If that\'s what you think how bout the other way around? How can you evaluate content without design? No typography, no colors, no layout, no styles, all those things that convey the important signals that go beyond the mere textual, hierarchies of information, weight, emphasis, oblique stresses, priorities, all those subtle cues that also have visual and emotional appeal to the reader. Rigid proponents of content strategy may shun the use of dummy copy but then designers might want to ask them to provide style sheets with the copy decks they supply that are in tune with the design direction they require.</p><h3 _ngcontent-sia-c6="" style="font-family: &quot;Nunito Sans&quot;, sans-serif; font-weight: 700; color: rgb(33, 38, 49); margin: 0px 0px 15px; font-size: 23px;">Summing up, if the copy is diverting attention from the design it’s because it’s not up to task.</h3><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">Typographers of yore didn\'t come up with the concept of dummy copy because people thought that content is inconsequential window dressing, only there to be used by designers who can’t be bothered to read. Lorem Ipsum is needed because words matter, a lot. Just fill up a page with draft copy about the client’s business and they will actually read it and comment on it. They will be drawn to it, fiercely. Do it the wrong way and draft copy can derail your design review.</p>                
        ');
        $data = array(
            // Problem some Defaults not created
            'message'=>'error, already exists'
        );
        if(!User::getAll()){
            User::createAdmin();
            // Defaults created
            $data['message'] = 'ok';
            $jwt=['data'=>$data];
            return $this->json(['jwt'=>$jwt]);
        }
        $jwt=['data'=>$data];
        return $this->json(['jwt'=>$jwt]);
    }
    
    public function drop_all_tables($requestData=null)
    {
        $db = null;
        try{
            $dsn = 'pgsql:host=' . Config::DB_HOST . ';port=' . Config::DB_POSTGRESQL_PORT . ';dbname=' . Config::DB_NAME . 
            ';user=' . Config::DB_USER . ';password=' . Config::DB_PASSWORD;
            $db = new PDO($dsn);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e){
            echo 'Connection failed: ' . $e->getMessage();
        }
        $statement=null;
        $sql="
            SELECT
            *
            FROM
            pg_catalog.pg_tables
            WHERE
            schemaname != 'pg_catalog'
            AND schemaname != 'information_schema';
        ";
        $sql="
            SELECT
            table_schema || '.' || table_name
            FROM
                information_schema.tables
            WHERE
                table_type = 'BASE TABLE'
            AND
                table_schema NOT IN ('pg_catalog', 'information_schema');
        ";
        $statement = $db->prepare($sql);
        $statement->execute();
        $tables = $statement->fetchAll(PDO::FETCH_NUM);
        
        foreach($tables as $table){
            echo $table[0], '<br>';
            $sql="
            DROP TABLE IF EXISTS ".$table[0].";
            ";
            $statement = $db->prepare($sql);
            $statement->execute();
        }
    }

}