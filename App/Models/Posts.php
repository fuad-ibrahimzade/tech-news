<?php

namespace App\Models;

use Carbon\Carbon;
use Core\Model;
use PDO;

class Posts extends Model
{
    public static function createDefault(Type $var = null)
    {
        $db = static::getDB();
        // `id` INT(11) NOT NULL AUTO_INCREMENT,

        $stmt = $db->prepare("CREATE TABLE IF NOT EXISTS posts
            (
                id SERIAL,
                post_category VARCHAR(255) NOT NULL,
                post_title VARCHAR(255) NOT NULL,
                post_tags VARCHAR(255) NOT NULL,
                post_picture VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                post_desc TEXT NOT NULL,
                author_name VARCHAR(255) NOT NULL,
                author_desc TEXT NOT NULL,
                author_picture VARCHAR(255) NOT NULL,
                author_social_links VARCHAR(255),
                date_upd TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                date_add DATE NOT NULL,
                PRIMARY KEY (id)
            );
            ");
        $stmt->execute();

        $pages=['News','Popular','Web Design','JavaScript','Css','Jquery'];
        $postTitle="Ask HN: Does Anybody Still Use JQuery?";
        $postTitles=[
            'Tell-A-Tool: Guide To Web Design And Development Tools',
            'Why Node.js Is The Coolest Kid On The Backend Development Block!',
            'Pagedraw UI Builder Turns Your Website Design Mockup Into Code Automatically',
            'Chrome Extension Protects Against JavaScript-Based CPU Side-Channel Attacks',
            'CSS Float: A Tutorial',
            'Ask HN: Does Anybody Still Use JQuery?'
        ];
        $postDescription='
            Do you like Cheese Whiz? Spray tan? Fake eyelashes? That\'s what is Lorem Ipsum to many—it rubs them the wrong way, all the way. It\'s unreal, uncanny
        ';
        $postTags=[['Chrome','CSS','Tutorial'],['Backend','JQuery','Design'],['Development','JavaScript','Website'],
                    ['CSS','Tutorial','Backend'],['Design','Development','JavaScript'],['Chrome','JQuery','Website']];

        $baseUrl='https://'.$_SERVER['SERVER_NAME'].'/';
        $postPictures=[];
        for ($i=0; $i < 6 ; $i++) {
            // frontend/dist/frontend/
            $postPictures[]=$baseUrl.'assets/img/post-'.($i+1).'.jpg';
        }
        $defaultContent=`
            <h3 _ngcontent-sia-c6="" style="font-family: &quot;Nunito Sans&quot;, sans-serif; font-weight: 700; color: rgb(33, 38, 49); margin: 0px 0px 15px; font-size: 23px;">Lorem Ipsum: when, and when not to use it</h3><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">Do you like Cheese Whiz? Spray tan? Fake eyelashes? That's what is Lorem Ipsum to many—it rubs them the wrong way, all the way. It's unreal, uncanny, makes you wonder if something is wrong, it seems to seek your attention for all the wrong reasons. Usually, we prefer the real thing, wine without sulfur based preservatives, real butter, not margarine, and so we'd like our layouts and designs to be filled with real words, with thoughts that count, information that has value.</p><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">The toppings you may chose for that TV dinner pizza slice when you forgot to shop for foods, the paint you may slap on your face to impress the new boss is your business. But what about your daily bread? Design comps, layouts, wireframes—will your clients accept that you go about things the facile way? Authorities in our business will tell in no uncertain terms that Lorem Ipsum is that huge, huge no no to forswear forever. Not so fast, I'd say, there are some redeeming factors in favor of greeking text, as its use is merely the symptom of a worse problem to take into consideration.</p><figure _ngcontent-sia-c6="" class="figure-img" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;"><img _ngcontent-sia-c6="" alt="" class="img-responsive" src="https://localhost:4200/assets/img/post-4.jpg"><figcaption _ngcontent-sia-c6="" style="padding-top: 5px; font-size: 13px; font-weight: 600;">So Lorem Ipsum is bad (not necessarily)</figcaption></figure><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">You begin with a text, you sculpt information, you chisel away what's not needed, you come to the point, make things clear, add value, you're a content person, you like words. Design is no afterthought, far from it, but it comes in a deserved second. Anyway, you still use Lorem Ipsum and rightly so, as it will always have a place in the web workers toolbox, as things happen, not always the way you like it, not always in the preferred order. Even if your less into design and more into content strategy you may find some redeeming value with, wait for it, dummy copy, no less.</p><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">There's lot of hate out there for a text that amounts to little more than garbled words in an old language. The villagers are out there with a vengeance to get that Frankenstein, wielding torches and pitchforks, wanting to tar and feather it at the least, running it out of town in shame.</p><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">One of the villagers, Kristina Halvorson from Adaptive Path, holds steadfastly to the notion that design can’t be tested without real content:</p><blockquote _ngcontent-sia-c6="" class="blockquote" style="padding-top: 20px; padding-bottom: 20px; margin-bottom: 10px; border-left: 0px; position: relative; font-weight: 600; color: rgb(61, 69, 92); font-family: Nunito, sans-serif;">I’ve heard the argument that “lorem ipsum” is effective in wireframing or design because it helps people focus on the actual layout, or color scheme, or whatever. What kills me here is that we’re talking about creating a user experience that will (whether we like it or not) be DRIVEN by words. The entire structure of the page or app flow is FOR THE WORDS.</blockquote><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">If that's what you think how bout the other way around? How can you evaluate content without design? No typography, no colors, no layout, no styles, all those things that convey the important signals that go beyond the mere textual, hierarchies of information, weight, emphasis, oblique stresses, priorities, all those subtle cues that also have visual and emotional appeal to the reader. Rigid proponents of content strategy may shun the use of dummy copy but then designers might want to ask them to provide style sheets with the copy decks they supply that are in tune with the design direction they require.</p><h3 _ngcontent-sia-c6="" style="font-family: &quot;Nunito Sans&quot;, sans-serif; font-weight: 700; color: rgb(33, 38, 49); margin: 0px 0px 15px; font-size: 23px;">Summing up, if the copy is diverting attention from the design it’s because it’s not up to task.</h3><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">Typographers of yore didn't come up with the concept of dummy copy because people thought that content is inconsequential window dressing, only there to be used by designers who can’t be bothered to read. Lorem Ipsum is needed because words matter, a lot. Just fill up a page with draft copy about the client’s business and they will actually read it and comment on it. They will be drawn to it, fiercely. Do it the wrong way and draft copy can derail your design review.</p>                
        `;
        $defaultContent=('
        <h3 _ngcontent-sia-c6="" style="font-family: &quot;Nunito Sans&quot;, sans-serif; font-weight: 700; color: rgb(33, 38, 49); margin: 0px 0px 15px; font-size: 23px;">Lorem Ipsum: when, and when not to use it</h3><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">Do you like Cheese Whiz? Spray tan? Fake eyelashes? That\'s what is Lorem Ipsum to many—it rubs them the wrong way, all the way. It\'s unreal, uncanny, makes you wonder if something is wrong, it seems to seek your attention for all the wrong reasons. Usually, we prefer the real thing, wine without sulfur based preservatives, real butter, not margarine, and so we\'d like our layouts and designs to be filled with real words, with thoughts that count, information that has value.</p><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">The toppings you may chose for that TV dinner pizza slice when you forgot to shop for foods, the paint you may slap on your face to impress the new boss is your business. But what about your daily bread? Design comps, layouts, wireframes—will your clients accept that you go about things the facile way? Authorities in our business will tell in no uncertain terms that Lorem Ipsum is that huge, huge no no to forswear forever. Not so fast, I\'d say, there are some redeeming factors in favor of greeking text, as its use is merely the symptom of a worse problem to take into consideration.</p><figure _ngcontent-sia-c6="" class="figure-img" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;"><img _ngcontent-sia-c6="" alt="" class="img-responsive" src="'.$baseUrl.'assets/img/post-4.jpg"><figcaption _ngcontent-sia-c6="" style="padding-top: 5px; font-size: 13px; font-weight: 600;">So Lorem Ipsum is bad (not necessarily)</figcaption></figure><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">You begin with a text, you sculpt information, you chisel away what\'s not needed, you come to the point, make things clear, add value, you\'re a content person, you like words. Design is no afterthought, far from it, but it comes in a deserved second. Anyway, you still use Lorem Ipsum and rightly so, as it will always have a place in the web workers toolbox, as things happen, not always the way you like it, not always in the preferred order. Even if your less into design and more into content strategy you may find some redeeming value with, wait for it, dummy copy, no less.</p><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">There\'s lot of hate out there for a text that amounts to little more than garbled words in an old language. The villagers are out there with a vengeance to get that Frankenstein, wielding torches and pitchforks, wanting to tar and feather it at the least, running it out of town in shame.</p><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">One of the villagers, Kristina Halvorson from Adaptive Path, holds steadfastly to the notion that design can’t be tested without real content:</p><blockquote _ngcontent-sia-c6="" class="blockquote" style="padding-top: 20px; padding-bottom: 20px; margin-bottom: 10px; border-left: 0px; position: relative; font-weight: 600; color: rgb(61, 69, 92); font-family: Nunito, sans-serif;">I’ve heard the argument that “lorem ipsum” is effective in wireframing or design because it helps people focus on the actual layout, or color scheme, or whatever. What kills me here is that we’re talking about creating a user experience that will (whether we like it or not) be DRIVEN by words. The entire structure of the page or app flow is FOR THE WORDS.</blockquote><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">If that\'s what you think how bout the other way around? How can you evaluate content without design? No typography, no colors, no layout, no styles, all those things that convey the important signals that go beyond the mere textual, hierarchies of information, weight, emphasis, oblique stresses, priorities, all those subtle cues that also have visual and emotional appeal to the reader. Rigid proponents of content strategy may shun the use of dummy copy but then designers might want to ask them to provide style sheets with the copy decks they supply that are in tune with the design direction they require.</p><h3 _ngcontent-sia-c6="" style="font-family: &quot;Nunito Sans&quot;, sans-serif; font-weight: 700; color: rgb(33, 38, 49); margin: 0px 0px 15px; font-size: 23px;">Summing up, if the copy is diverting attention from the design it’s because it’s not up to task.</h3><p _ngcontent-sia-c6="" style="margin-bottom: 20px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;">Typographers of yore didn\'t come up with the concept of dummy copy because people thought that content is inconsequential window dressing, only there to be used by designers who can’t be bothered to read. Lorem Ipsum is needed because words matter, a lot. Just fill up a page with draft copy about the client’s business and they will actually read it and comment on it. They will be drawn to it, fiercely. Do it the wrong way and draft copy can derail your design review.</p>                
        ');
        // frontend/dist/frontend/
        $author=['author_name'=>'John Doe','author_picture'=>$baseUrl.'assets/img/author.png','author_desc'=>'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'];
        $authorSocialLinks=['facebook'=>'facebook','twitter'=>'twitter','instagram'=>'instagram'];
        for ($index=0; $index <8 ; $index++) {
            for ($i=0; $i < count($pages); $i++) {
                $page=Pages::getByWhere('name',$pages[$i])[0];
                $page->posts_count+=1;
                $fields=[];
                foreach ($page as $key => $value) {
                    $fields[$key]=$value;
                }
                Pages::update($fields);
                
                $stmt = $db->prepare("INSERT INTO posts (post_category,post_title,post_tags,post_picture,content,
                post_desc,author_name,author_desc,author_picture,author_social_links,date_upd,date_add) 
                VALUES ( ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? );");
                $stmt->execute(array(
                    $pages[$i],
                    $postTitles[$i],
                    json_encode($postTags),
                    $postPictures[$i],
                    $defaultContent,
                    $postDescription,
                    $author['author_name'],
                    $author['author_desc'],
                    $author['author_picture'],
                    json_encode($authorSocialLinks),
                    Carbon::now(),
                    Carbon::now()
                )); 
            }
        }


        
    }
    
    public static function getAll()
    {
        $db = static::getDB();
        $stmt=null;
        try{
            $stmt = $db->query('SELECT * FROM posts');
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        catch (\Exception $e){
        //    echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return false;
    }

    public static function getRandomLimit($limit)
    {
        $db = static::getDB();
        $stmt=null;
        try{
            // $stmt = $db->query('SELECT * FROM posts ORDER BY rand() LIMIT '.$limit);//mysql
            $stmt = $db->query('SELECT * FROM posts ORDER BY random() LIMIT '.$limit);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        catch (\Exception $e){
        //    echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return false;
    }

    public static function getById($id) {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM posts WHERE id = ?');
        $stmt->execute(array($id));
        $allIds = $stmt->fetchAll(PDO::FETCH_OBJ)[0];
        return $allIds;
    }

    public static function getByWhere($ckey,$cvalue) {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM posts WHERE '.$ckey.' = ?');
        $stmt->execute(array($cvalue));
        $allIds = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $allIds;
    }

    public static function update($fields){
        $db = static::getDB();
        foreach ($fields as $key => $value){
            if(!Posts::column_exists($key))continue;
            if($key=='id')continue;
            $sql = "UPDATE posts SET ".$key."=? WHERE id=?";
            $stmt= $db->prepare($sql);
            $stmt->execute([
                $value,
                $fields->id
            ]);
        }
    }

    public static function create($fields){
        $page=Pages::getByWhere('name',$fields->post_category)[0];
        $page->posts_count+=1;
        $fields=[];
        foreach ($page as $key => $value) {
            $fields[$key]=$value;
        }
        Pages::update($fields);
        
        $db = static::getDB();
        $stmt = $db->prepare("INSERT INTO posts (post_category,post_title,post_tags,post_picture,content,
            post_desc,author_name,author_desc,author_picture,author_social_links,date_upd,date_add) 
            VALUES ( ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? );");
        $stmt->execute(array(
            $fields->post_category,
            $fields->post_title,
            $fields->post_tags,
            $fields->post_picture,
            $fields->content,
            $fields->post_desc,
            $fields->author_name,
            $fields->author_desc,
            $fields->author_picture,
            (isset($fields->author_social_links)?$fields->author_social_links:''),
            Carbon::now(),
            Carbon::now()
        ));
    }

    public static function delete($id)
    {
        $page=Pages::getByWhere('name',Posts::getById($id)->post_category)[0];
        $page->posts_count-=1;
        $fields=[];
        foreach ($page as $key => $value) {
            $fields[$key]=$value;
        }
        Pages::update($fields);

        $db = static::getDB();
        $stmt = $db->prepare( "DELETE FROM posts WHERE id = ?" );
        $stmt->execute(array($id));
        if( ! $stmt->rowCount() ) {return "Deletion failed";}
        else { return true; }
    }

    public static function column_exists($column)
    {
        $db = static::getDB();
        // $stmt = $db->prepare( "SHOW COLUMNS FROM `posts` LIKE ?" );
        $stmt=$db->prepare("
        SELECT column_name 
        FROM information_schema.columns 
        WHERE table_name='posts' and column_name=?;
        ");
        $stmt->execute(array($column));
        // $stmt_value=count($db->query("SHOW COLUMNS FROM `posts` LIKE '".$column."'")->fetchAll());
        if( ! $stmt->rowCount() ) { return false; }
        else { return true; }
    }
    
    public static function getDistinctColumn($column)
    {
        $db = static::getDB();
        // $stmt = $db->prepare( "SELECT DISTINCT ".$column." FROM posts" );
        // $stmt->execute(array($column));
        // $stmt_value=count($db->query("SHOW COLUMNS FROM `posts` LIKE '".$column."'")->fetchAll());
        $stmt = $db->prepare( "SELECT DISTINCT ".$column." FROM posts" );
        $stmt->execute();
        if( ! $stmt->rowCount() ) { return false; }
        else { return $stmt->fetchAll(PDO::FETCH_OBJ); }
    }
}
