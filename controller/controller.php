<?php
session_start();

require_once '../model/model.php';

unQuoteMe();
if (isset($_POST['action'])) {  // check get and post
        $action = $_POST['action'];
    } else if (isset($_GET['action'])) {
        $action = $_GET['action'];
    } else {
        include('../view/index.php');  // default action
        exit();
    }
} else {
    switch ($action){
        case 'About':
            include '../view/about.php';
            break;
        case 'AddStory':
            addStory();
            break;
        default:
            getStoriesForPage();       
    } //END SWITCH
}
    
    function addStory(){
        $mode = "add";
        $storyID = 0;
        $headline = "";
        $section = "";
        $writer = "";
        $story = "";
        $storyImage = "GenericPic.jpg";
        $topStory = "Y";
        $datePublished = "yyyy:mm:dd";

        include '../view/newStory.php';    
    }
    
    function deleteStory(){
        $storyID = $_GET['StoryId'];
        if (!isset($storyID)) {
            $errorMessage = 'You must provide the ID of the Story you want to delete.';
            include '../view/errorMessage.php';
        } 
        else {
            $rowCount = deleteOneStory($storyID);
            if ($rowCount != 1) {
                    $errorMessage = "The delete affected $rowCount rows. Please contact the site Admin to correct this issue";
                    include '../view/errorMessage.php';
            } else {
                    header("Location:../controller/controller.php?action=Home&ListType=Home");
            }
        }
    }

    function displayRequestedStory(){
        $storyID = $_GET['StoryId'];
        $row = getSingleStory($storyID);
        include '../view/displayStory.php';
    }
    
    function editStory(){
        
            $storyID = $_GET['StoryId'];
        
            if (!isset($storyID)) {
                    $errorMessage = 'You must provide a StoryID to display.';
                    include '../view/errorMessage.php';
            } 
            else {
                $row = getStory($storyID);
                if ($row == FALSE) {
                    $errorMessage = 'That Story can no longer be found.';
                    include '../view/errorMessage.php';
                } 
                
                else {
                    $mode = "Edit";
                    $storyID = $row['StoryId'];
                    $headline = $row['Headline'];
                    $section = $row['Section'];
                    $writer = $row['Writer'];
                    $story = $row['Story'];
                    $storyImage = $row['StoryImage'];
                    $topStory = $row['TopStory'];
                    $datePublished = $row['DatePublished'];
                    
                    include '../view/newStory.php'; 
                    
                }
            }
    }

  
    function processRegistration(){
        $firstName = $_POST['FirstName'];
	$lastName = $_POST['LastName'];
	$email = $_POST['Email'];
        
        if(empty($firstName)){
            $errorMessage = "<h3> You Must provide a first name to register";
            include '../view/errorMessage.php';
        }
        else if(empty($email)|| !filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errorMessage = "<h3> You Must provide a valid email to register";
            include '../view/errorMessage.php';
        }
        else{
            $msg = "Thanks For Signing Up to Recieve our Emails";
            newUserInfo ($firstName, $lastName, $email);
            $userInfoARRAY = getUserInfo();
            
            include '../view/processRegisterMember.php';
        }
    }
    
    function processAddEdit() {

        //print_r($_POST);
        $storyID = $_POST['storyID'];
        $mode = $_POST['Mode'];
        $headline = $_POST['Headline'];
        $section = $_POST['Section'];        
        $writer = $_POST['Writer'];
        $story = $_POST['Story'];
        $storyImage = $_POST['StoryImage'];
        if (isset($_POST['TopStory'])) {
                $topStory = 'Y';
        } 
        else {
              $topStory = 'N';
        }
        $datePublished = $_POST['DatePublished'];

        // Validation
        $errors = "";
        if (empty($headline) || strlen($headline) > 100) {
                $errors .= "\\n* A Headline is required and can not be longer than 100 Characters.";
        }
        if (empty($section) || strlen($section) > 20) {
                $errors .= "\\n* A Section of the Paper is required and must be no more than 20 characters.";
        }
        if (empty($writer) || strlen($writer) > 100) {
                $errors .= "\\n* A Writers Name is reuqired";
        }
        if (empty($story) || strlen($story) > 65000) {
                $errors .= "\\n* The story must be copied in and can not be more than 65000 characters";
        }
        if (empty($storyImage) || strlen($storyImage) > 75) {
                $errors .= "\\n* Please Enter just the image name and extenstion ie. GenericPic.jpg";
        }
        if (!empty($datePublished) && !strtotime($datePublished)) {
                $errors .= "\\n* Please Enter a Real Date";
        }

        if ($errors != "") {
            include '../view/newStory.php';
        } 
        else {
            if($mode == "add"){
                $storyID = insertStory($headline, $section, $writer, $story, $storyImage, $topStory, $datePublished);
            }
            else{
                $rowsAffected = updateStory($storyID,$headline, $section, $writer, $story, $storyImage, $topStory, $datePublished);
            }
            header("Location:../controller/controller.php?action=DisplayStory&StoryId=$storyID");
        }

    }
    
    