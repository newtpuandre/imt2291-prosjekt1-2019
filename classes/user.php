<?php
require_once 'db.php';

class User
{

    static private $target_dir = "uploads/";


    private $id;
    private $email;
    private $privileges;
    private $name;
    private $picture;

    private $db = null;

    public function __construct($m_email) {
        //Initalize a new database connection
        $this->db = new DB();

        //Find user in DB and store info in class variables
        $userArray = $this->db->findUser($m_email);
        $this->id = $userArray['id'];
        $this->email = $userArray['email'];
        $this->privileges = $userArray['privileges'];
        $this->name = $userArray['name'];
        $this->picture = $userArray['picture_path'];
    }

    function __destruct() {

    }

    public function updateUser($uid, $name, $username, $password, $profilepic){

        if ($password != null){
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $hashed_password = null;
        }
        /* Delete old picture if it is not the default picture and there is a new profile picture */

        $old_picture = $this->returnPicture();
        if($old_picture && ($profilepic != null)) {
            if ($old_picture != 'https://propertymarketersllc.com/wp-content/uploads/2018/05/profile-picture-placeholder.png') {
                unlink($old_picture);
            }
        }
    
        /* Upload new image if provided */
        $db = new DB();
        if ($profilepic != null) {

            $picture_file_type = strtolower(pathinfo(User::$target_dir . basename($profilepic["name"]), PATHINFO_EXTENSION));
            $picture_path = User::$target_dir . uniqid() . "." . $picture_file_type;
            

            /* TODO : Return meaningful error for all of these, for now, debug echos */
            if($picture_file_type != "jpg" && $picture_file_type != "png" && $picture_file_type != "jpeg" && $picture_file_type != "gif") {
                echo"picture format must be jpg, png, jpeg or gif";
                return false;
            }
           
            /* Resize profilepic  */
            $this->pictureResize($profilepic, 180, 180, $picture_path);

            if(move_uploaded_file($profilepic["tmp_name"], $picture_path)){
                echo "The files uploaded successfully!";
            }
            else{
                return false;
            }

            $db->updateUser($uid, $name, $username, $hashed_password, $picture_path);
            header("Location: profile.php");

        } else {

            $db->updateUser($uid, $name, $username, $hashed_password, $old_picture);
            header("Location: profile.php");

        }
    }

    public function returnEmail(){ //Returns users email
        return $this->email;
    }

    public function getPrivileges(){ //Get users privileges
        return $this->privileges;
    }

    public function returnId(){
        return $this->id;
    }

    public function returnName(){
        return $this->name;
    }

    public function returnPicture(){
        return $this->picture;
    }

    public function pictureResize($picture, $new_width, $new_height, $output_path){
        $content = file_get_contents($picture["tmp_name"]);
        
        list($old_width, $old_height, $type, $attr) = getimagesize($picture["tmp_name"]);
        
        $src_img = imagecreatefromstring(file_get_contents($picture["tmp_name"]));
        $dst_img = imagecreatetruecolor($new_width, $new_height);
        
        /* Copy and store */
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);
        imagepng($dst_img, $output_path);

        /* Clean up */
        imagedestroy($src_img);
        imagedestroy($dst_img);        
    }
}


?>