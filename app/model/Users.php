<?php 
/**
* 
*/
class Users extends Model
{	
	protected static $auth=null;
	public static function auth(){
		if(!self::$auth){
			$access_token=Input::any('access_token');
			if (!$access_token) {
				return false;
			}
			$sql="SELECT a.* FROM users a LEFT JOIN access_token b ON a.id=b.user_id WHERE b.access_token=:access_token AND b.is_active=1";
			$artist=new self;
			self::$auth=$artist->first($sql,array('access_token'=>$access_token));
			if (self::$auth) {
				self::$auth->user_type='3';
			}
		}
		return self::$auth;
	}
	public static function getProfile(){
	    $sql="SELECT * FROM artists a WHERE a.id=".Artists::auth()->id;
		return (new self)->first($sql);
	}
	public static function profileAboutSet($details){
		$set=Helper::make_set($details);
		$id=Artists::auth()->id;
	    if((new self)->update("UPDATE artists SET $set WHERE id=$id",$details)){
	    	(new self)->update("UPDATE artists SET profile_stage=1 WHERE id=$id AND profile_stage<1");
	    	return true;
	    }
	    return false;
	}
	public static function profilePerformanceSet($single,$multiple){
		$artists=new self;
		$set=Helper::make_set($single);
		$id=Artists::auth()->id;
		$artist=new self;
		$artists->start_trans();
		$genre_id=self::multiple_insert($multiple['genre_id'],$id);
		$specialization_id=self::multiple_insert($multiple['specialization_id'],$id);
		$linguage_id=self::multiple_insert($multiple['linguage_id'],$id);
		try {
			$artists->sql("UPDATE artists SET $set WHERE id=$id",$single);
			$artists->sql("DELETE FROM artist_genres WHERE artist_id='$id'");
			$artists->sql("DELETE FROM artist_specializations WHERE artist_id='$id'");
			$artists->sql("DELETE FROM artist_linguage WHERE artist_id='$id'");
			$artists->sql("INSERT INTO artist_genres (artist_id,genre_id) VALUES $genre_id");
			$artists->sql("INSERT INTO artist_specializations (artist_id,specialization_id) VALUES $specialization_id");
			$artists->sql("INSERT INTO artist_linguage (artist_id,linguage_id) VALUES $linguage_id");	
			$artists->Update("UPDATE artists SET profile_stage=2 WHERE id=$id AND profile_stage<2");
		} catch (PDOException $e) {
			$artists->rollback();
			return 0;
		}
		$artists->commit();

		return true;
	}

	public static function profilePreferenceSet($single,$multiple){
		$artists=new self;
		$set=Helper::make_set($single);
		$id=Artists::auth()->id;
		$artist=new self;
		$artists->start_trans();
		$event_type_id=self::multiple_insert($multiple['event_type'],$id);
		$past_event_type_id=self::multiple_insert($multiple['past_event_type'],$id);
		
		try {
			$artists->sql("UPDATE artists SET $set WHERE id=$id",$single);
			$artists->sql("DELETE FROM artist_event_type WHERE artist_id='$id'");
			$artists->sql("DELETE FROM artist_past_event_type WHERE artist_id='$id'");
			$artists->sql("INSERT INTO artist_event_type (artist_id,event_type_id) VALUES $event_type_id");
			$artists->sql("INSERT INTO artist_past_event_type (artist_id,event_type_id) VALUES $past_event_type_id");
			if ($single['can_travel']) {
				$travel_city_id=self::multiple_insert($multiple['travel_city'],$id);
				$artists->sql("DELETE FROM artist_travel_city WHERE artist_id='$id'");
				$artists->sql("INSERT INTO artist_travel_city (artist_id,city_id) VALUES $travel_city_id");	
			}
		$artists->Update("UPDATE artists SET profile_stage=3 WHERE id=$id AND profile_stage<3");
		} catch (PDOException $e) {
			echo $e;
			$artists->rollback();
			return 0;
		}
		$artists->commit();

		return true;	
	}
	public static function verifyAccount($details){
		$id=Artists::auth()->id;
		$artists=new self;
		$artist=$artists->first("SELECT id FROM artists WHERE email_code=:email_code AND id=$id",$details);
		if ($artist) {
			return $artists->update("UPDATE artists SET email_code='',is_verified=1 WHERE id='$id'");
		}
		return false;
	}
	private static function multiple_insert(Array $details,$id){
		return "('$id',".implode("),('$id',",$details).")";
	}

	public static function getFullProfile(){
		$artist=Artists::auth();
		$artists=new self;
		$artist->event_type=$artists->select("SELECT a.*,b.event FROM artist_event_type a LEFT JOIN event_types b ON a.event_type_id=b.id WHERE a.artist_id=".$artist->id);
		$artist->past_event_type=$artists->select("SELECT a.*,b.event FROM artist_past_event_type a LEFT JOIN event_types b ON a.event_type_id=b.id WHERE a.artist_id=".$artist->id);
		$artist->travel_city=$artists->select("SELECT a.*,b.city FROM artist_travel_city a LEFT JOIN cities b ON a.city_id=b.id WHERE a.artist_id=".$artist->id);
		$artist->genres=$artists->select("SELECT a.*,b.name FROM artist_genres a LEFT JOIN genres b ON a.genre_id=b.id WHERE a.artist_id=".$artist->id);
		$artist->specializations=$artists->select("SELECT a.*,b.name,c.name AS genre FROM artist_specializations a LEFT JOIN specializations b  ON a.specialization_id=b.id LEFT JOIN genres c ON b.genre_id=c.id WHERE a.artist_id=".$artist->id);
		$artist->city=$artists->select("SELECT b.* FROM artists a LEFT JOIN cities b ON a.city_id=b.id WHERE a.id=".$artist->id);
		$artist->locality=$artists->select("SELECT b.* FROM artists a LEFT JOIN localities b ON a.locality_id=b.id WHERE a.id=".$artist->id);
		$artist->art_category=$artists->select("SELECT b.* FROM artists a LEFT JOIN art_categories b ON a.art_category_id=b.id WHERE a.id=".$artist->id);
		$artist->artist_category=$artists->select("SELECT b.* FROM artists a LEFT JOIN artist_categories b ON a.artist_category_id=b.id WHERE a.id=".$artist->id);
		$artist->linguage=$artists->select("SELECT a.*,b.linguage FROM artist_linguage a LEFT JOIN linguages b ON a.linguage_id=b.id WHERE a.artist_id=".$artist->id);
		$artist->fan_count=$artists->first("SELECT count(*) AS fan_count FROM artist_fans WHERE artist_id='$artist->id'")->fan_count;
		return $artist;
	}

	public static function getArtists($page=null){
		$user=Auth::user();
		$artists=new self;
		$in_id=self::getArtistId($page);
		@$sql="SELECT a.name,a.id,a.email,a.mobile, a.art_category_id,a.artist_category_id,a.city_id,a.locality_id,a.brief_intro,
					 
			a.gender,a.band_member,a.gig_duration,a.can_travel,a.training,a.facebook_link,a.twitter_link,
					 
			b.name AS artist_category,f.name AS art_category,
					 
			d.name AS genre,d.id AS genre_id,e.city,

			h.fan_count,j.is_fan

		    FROM artists  a LEFT JOIN artist_categories b ON a.artist_category_id=b.id

			LEFT JOIN artist_genres c ON a.id=c.artist_id 

   			LEFT JOIN genres d ON c.genre_id=d.id

			LEFT JOIN cities e ON a.city_id=e.id 

			LEFT JOIN art_categories f ON a.art_category_id=f.id

			LEFT JOIN (SELECT artist_id,count(id) AS fan_count FROM artist_fans g WHERE g.artist_id IN $in_id GROUP BY g.artist_id ) h ON h.artist_id=a.id

			LEFT JOIN (SELECT artist_id,count(id) is_fan FROM artist_fans i WHERE i.user_id='$user->id' AND i.user_type='$user->user_type' AND i.artist_id IN $in_id GROUP BY i.artist_id) j ON j.artist_id=a.id

			WHERE a.id IN $in_id AND a.id!='$user->id'";

		$artist=$artists->select($sql);
		$result=array();
		foreach ($artist as $key => $value) {
			$result[$value->id]['name']=$value->name;
			$result[$value->id]['id']=$value->id;
			$result[$value->id]['email']=$value->email;
			$result[$value->id]['mobile']=$value->mobile;
			$result[$value->id]['art_category_id']=$value->art_category_id;
			$result[$value->id]['artist_category_id']=$value->artist_category_id;
			$result[$value->id]['city_id']=$value->city_id;
			$result[$value->id]['locality_id']=$value->locality_id;
			$result[$value->id]['brief_intro']=$value->brief_intro;
			$result[$value->id]['gender']=$value->gender;
			$result[$value->id]['band_member']=$value->band_member;
			$result[$value->id]['gig_duration']=$value->gig_duration;
			$result[$value->id]['can_travel']=$value->can_travel;
			$result[$value->id]['training']=$value->training;
			$result[$value->id]['facebook_link']=$value->facebook_link;
			$result[$value->id]['twitter_link']=$value->twitter_link;
			$result[$value->id]['artist_category']=$value->artist_category;
			$result[$value->id]['art_category']=$value->art_category;
			$result[$value->id]['genre'][]=array('id'=>$value->genre_id,'genre'=>$value->genre);
			$result[$value->id]['city']=$value->city;
			$result[$value->id]['fan_count']=$value->fan_count;
			$result[$value->id]['is_fan']=$value->is_fan;
		}
		return array_values($result);
	}
	public function getArtist($id){
		$user=Auth::user();
		$artists=new self;
		$artist=$artists->first("SELECT * FROM artists WHERE id=?",array($id));
		$artist->event_type=$artists->select("SELECT a.*,b.event FROM artist_event_type a LEFT JOIN event_types b ON a.event_type_id=b.id WHERE a.artist_id=".$artist->id);
		$artist->past_event_type=$artists->select("SELECT a.*,b.event FROM artist_past_event_type a LEFT JOIN event_types b ON a.event_type_id=b.id WHERE a.artist_id=".$artist->id);
		$artist->travel_city=$artists->select("SELECT a.*,b.city FROM artist_travel_city a LEFT JOIN cities b ON a.city_id=b.id WHERE a.artist_id=".$artist->id);
		$artist->genres=$artists->select("SELECT a.*,b.name FROM artist_genres a LEFT JOIN genres b ON a.genre_id=b.id WHERE a.artist_id=".$artist->id);
		$artist->specializations=$artists->select("SELECT a.*,b.name,c.name AS genre FROM artist_specializations a LEFT JOIN specializations b  ON a.specialization_id=b.id LEFT JOIN genres c ON b.genre_id=c.id WHERE a.artist_id=".$artist->id);
		$artist->city=$artists->select("SELECT b.* FROM artists a LEFT JOIN cities b ON a.city_id=b.id WHERE a.id=".$artist->id);
		$artist->locality=$artists->select("SELECT b.* FROM artists a LEFT JOIN localities b ON a.locality_id=b.id WHERE a.id=".$artist->id);
		$artist->art_category=$artists->select("SELECT b.* FROM artists a LEFT JOIN art_categories b ON a.art_category_id=b.id WHERE a.id=".$artist->id);
		$artist->artist_category=$artists->select("SELECT b.* FROM artists a LEFT JOIN artist_categories b ON a.artist_category_id=b.id WHERE a.id=".$artist->id);
		$artist->linguage=$artists->select("SELECT a.*,b.linguage FROM artist_linguage a LEFT JOIN linguages b ON a.linguage_id=b.id WHERE a.artist_id=".$artist->id);
		@$artist->is_fan=$artists->first("SELECT count(*) AS is_fan FROM artist_fans WHERE artist_id='$id' AND user_id='$user->id' AND user_type='$user->user_type'")->is_fan;
		$artist->fan_count=$artists->first("SELECT count(*) AS fan_count FROM artist_fans WHERE artist_id='$id'")->fan_count;
		return $artist;
	}
	private static function getArtistId($page=null){
		$page=(int)$page?(int)$page:1;
		$artist=Artists::auth();
		$artists=new self;
		$per_page=10;
		$offset=$page*10-10;
		$filters=$artists->select("SELECT id FROM artists WHERE profile_stage=3 LIMIT $offset,$per_page");
		$in_id=array();
		foreach ($filters as $key => $value) {
			$in_id[]=$value->id;
		}
		$inid='('.implode(',',$in_id).')';
		return $inid;
	}
	public static function fanArtist($details){
		return (new self)->insert($details,'artist_fans');
	}
	public static function fanArtistRemove($details){
		return (new self)->delete("DELETE FROM artist_fans WHERE artist_id=:artist_id AND user_id=:user_id AND user_type=:user_type",$details);
	}

	public static function yahavi_team(){
		return (new self)->select("SELECT * FROM yahavi_teams ORDER BY RAND()");
	}
	public static function yahavi_archives(){
		return (new self)->select("SELECT * FROM yahavi_archives");
	}
	public static function yahavi_testimonial(){
		return (new self)->select("SELECT * FROM yahavi_testimonials");
	}
	public static function yahavi_news(){
		return (new self)->select("SELECT * FROM yahavi_news");
	}
	public static function yahavi_archive(){
		$sql="SELECT  a.id,a.name,a.venue_name,a.city_id,a.locality_id,a.t_from,
		a.t_to,a.art_category_id,a.artist_category_id,a.event_image,a.event_type_id,a.other_artists,a.brief_desc,
		b.id AS artist_id,b.name AS artist_name ,c.event,d.city,e.locality,f.name AS art_category,g.name AS artist_category FROM 
		admin_events a LEFT JOIN admin_event_artists h ON h.event_id=a.id
		LEFT JOIN artists b ON h.artist_id=b.id
		LEFT JOIN event_types c ON a.event_type_id=c.id
		LEFT JOIN cities d ON a.city_id=d.id
		LEFT JOIN localities e ON a.locality_id=e.id
		LEFT JOIN art_categories f ON a.art_category_id=f.id
		LEFT JOIN artist_categories g ON a.artist_category_id=g.id WHERE a.t_from<CURDATE() AND a.t_to<CURDATE() ORDER BY a.t_from DESC LIMIT 25 ";
		$model=new self;
		$events=$model->select($sql,$params);
		$result=array();
		foreach ($events as $key => $value) {
			foreach ($value as $k => $v) {
				if(!isset($result[$value->id]['artists'])){
					$result[$value->id]['artists']=array();
				}
				if ($k=='artist_id') {
					$v?$result[$value->id]['artists'][]=array('id'=>$value->artist_id,'name'=>$value->artist_name):NULL;
				}elseif($k!='artist_name'){
					$result[$value->id][$k]=$v;	
				}
			}
		}
		return array_values($result);
	}
	public static function subscribe($details){	
		if(!empty((new self)->select("SELECT * FROM subscribe WHERE email=:email",array('email'=>$details['email']))))
		{
			return 'subscribed';
		}
		return (new self)->sql("INSERT INTO `subscribe`(`email`) VALUES (:email)",
			array('email'=>$details['email']));	
	}
}
?>