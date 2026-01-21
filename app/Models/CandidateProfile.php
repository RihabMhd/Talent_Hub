<?php 

namespace App\Models ;

class CandidateProfile {

    private $id;
    private $user_id;
    private $offre_id;
    private $message_motivation;
    private $cv_path;
    private $status;
    private $date_postulation;


    public function __construct($data = []){
          $this->id = $data['id'] ;
          $this->user_id = $data['user_id'] ;
          $this->offre_id = $data['offre_id'] ;
          $this->message_motivation = $data['message_motivation'] ;
          $this->cv_path = $data['cv_path'] ;
          $this->status = $data['status'] ;
          $this->date_postulation = $data['date_postulation'] ;
    }

    public function getId(){
        return $this->id ;
    }
    public function getuser_id(){
        return $this->user_id ;
    }
    public function getoffer_id(){
        return $this->offer_id ;
    }
    public function getmessagemotivation(){
        return $this->messagemotivation ;
    }
    public function getcv_path(){
        return $this->cv_path ;
    }
    public function getstatus(){
        return $this->status ;
    }
    public function getdate_postulation(){
        return $this->date_postulation ;
    }




    public function setId($id){
        $this->id = $id ;
    }
    public function setuser_id($user_id){
        $this->user_id = $user_id ;
    }
    public function setoffer_id($offer_id){
        $this->offer_id = $offer_id ;
    }
    public function setmeasage_motivation($measage_motivation){
        $this->measage_motivation = $measage_motivation ;
    }
    public function setcv_path($cv_path){
        $this->cv_path = $cv_path ;
    }
    public function setstatus($status){
        $this->status = $status ;
    }
    public function setdate_postulation($date_postulation){
        $this->date_postulation = $date_postulation ;
    }



}