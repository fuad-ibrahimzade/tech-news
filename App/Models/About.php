<?php

namespace App\Models;

use Carbon\Carbon;
use Core\Model;
use PDO;

class About extends Model
{
    public static function createDefault(Type $var = null)
    {
        $baseUrl='https://'.$_SERVER['SERVER_NAME'].'/';
        $db = static::getDB();

        $stmt = $db->prepare("CREATE TABLE IF NOT EXISTS about
            (
                id SERIAL,
                content TEXT NOT NULL,
                date_upd TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                date_add DATE NOT NULL,
                PRIMARY KEY (id)
            );
            ");
        $stmt->execute();
        $stmt = $db->prepare("INSERT INTO about (content,date_upd,date_add) VALUES ( ? , ? , ? );");
        $stmt->execute(array(
            '
                <div _ngcontent-yis-c6="" class="section-row" style="margin-bottom: 40px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;"><p _ngcontent-yis-c6="" style="margin-bottom: 20px;">Lorem ipsum dolor sit amet, ea eos tibique expetendis, tollit viderer ne nam. No ponderum accommodare eam, purto nominavi cum ea, sit no dolores tractatos. Scripta principes quaerendum ex has, ea mei omnes eruditi. Nec ex nulla mandamus, quot omnesque mel et. Amet habemus ancillae id eum, justo dignissim mei ea, vix ei tantas aliquid. Cu laudem impetus conclusionemque nec, velit erant persius te mel. Ut eum verterem perpetua scribentur.</p><figure _ngcontent-yis-c6="" class="figure-img" style="margin-bottom: 20px;"><img _ngcontent-yis-c6="" alt="" class="img-responsive" src="'.$baseUrl.'assets/img/about-1.jpg"></figure><p _ngcontent-yis-c6="" style="margin-bottom: 20px;">Vix mollis admodum ei, vis legimus voluptatum ut, vis reprimique efficiendi sadipscing ut. Eam ex animal assueverit consectetuer, et nominati maluisset repudiare nec. Rebum aperiam vis ne, ex summo aliquando dissentiunt vim. Quo ut cibo docendi. Suscipit indoctum ne quo, ne solet offendit hendrerit nec. Case malorum evertitur ei vel.</p></div><div _ngcontent-yis-c6="" class="row section-row" style="margin-bottom: 40px; color: rgb(61, 69, 92); font-family: Nunito, sans-serif; font-size: 16px;"><div _ngcontent-yis-c6="" class="col-md-6" style="float: left; width: 390px;"><figure _ngcontent-yis-c6="" class="figure-img" style="margin-bottom: 20px;"><img _ngcontent-yis-c6="" alt="" class="img-responsive" src="'.$baseUrl.'assets/img/about-2.jpg"></figure></div><div _ngcontent-yis-c6="" class="col-md-6" style="float: left; width: 390px;"><h3 _ngcontent-yis-c6="" style="font-family: &quot;Nunito Sans&quot;, sans-serif; font-weight: 700; color: rgb(33, 38, 49); margin: 0px 0px 15px; font-size: 23px;">Our Mission</h3><p _ngcontent-yis-c6="" style="margin-bottom: 20px;">Id usu mutat debet tempor, fugit omnesque posidonium nec ei. An assum labitur ocurreret qui, eam aliquid ornatus tibique ut.</p><ul _ngcontent-yis-c6="" class="list-style" style="margin-right: 0px; margin-left: 0px; padding: 0px 0px 0px 15px; list-style-position: initial; list-style-image: initial;"><li _ngcontent-yis-c6=""><p _ngcontent-yis-c6="" style="margin-bottom: 20px;">Vix mollis admodum ei, vis legimus voluptatum ut.</p></li><li _ngcontent-yis-c6=""><p _ngcontent-yis-c6="" style="margin-bottom: 20px;">Cu cum alia vide malis. Vel aliquid facilis adolescens.</p></li><li _ngcontent-yis-c6=""><p _ngcontent-yis-c6="" style="margin-bottom: 20px;">Laudem rationibus vim id. Te per illum ornatus.</p></li></ul></div></div>
            ',
            Carbon::now(),
            Carbon::now()
        ));
    }
    public static function getAll()
    {
        $db = static::getDB();
        $stmt=null;
        try{
            $stmt = $db->query('SELECT * FROM about');
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        catch (\Exception $e){
        //    echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return false;
    }
    public static function getById($id) {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM about WHERE id = ?');
        $stmt->execute(array($id));
        $allIds = $stmt->fetchAll(PDO::FETCH_OBJ)[0];
        return $allIds;
    }

    public static function getByWhere($ckey,$cvalue) {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM about WHERE '.$ckey.' = ?');
        $stmt->execute(array($cvalue));
        $allIds = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $allIds;
    }

    public static function update($fields){
        $db = static::getDB();
        foreach ($fields as $key => $value){
            if($key=='id')continue;
            $sql = "UPDATE about SET ".$key."=? WHERE id=?";
            $stmt= $db->prepare($sql);
            $stmt->execute([
                $value,
                $fields['id']
            ]);
        }
    }
}
