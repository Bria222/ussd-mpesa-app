<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pesa extends CI_Controller {


	public function __construct()
    {
        parent::__construct();
        $this->load->database();

    }

    public function index()
    {
    	$this->addUser();
    }
    public function ussdApp(){
    	$sessionId   = $this->input->post('sessionId');
        $serviceCode = $this->input->post('serviceCode');
        $phoneNumber = $this->input->post('phoneNumber');
        $text = $this->input->post('text');

        //Main menu screen option 1
        if ( $text == "" ) {
            // check if user is found on the users table
            $query = $this->db->get_where('users', array('phone_number' => $phoneNumber));
            if ($query->num_rows() > 0) {
                $response  = "CON Welcome to pesa app system\n";
                $response .= "1. Load airtime\n";
                $response .= "2. Check balance\n";
                $response .= "3. Okoa friend";
            }
            else{
                //user not found
                $response = "END Sorry! User not registered to this pesa app.";
            }

        }
        //option 2  load artime
        elseif ($text == "1" ) {
            $response  = "CON Enter airtime tocken";
           
        }
        //option 2  Check savings balance
        elseif ($text == "2" ) {
            // check if user is found on the users table
            $query = $this->db->get_where('users', array('phone_number' => $phoneNumber));
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $savings = $row->savings;
                $amount = $row->amount;
                $response = "END Dear customer! your airtime balance is Ksh. $amount and savings balance is Ksh. $savings.";
               
            }
            else{
                //user not found
                $response = "END Sorry! User not registered to this pesa app.";
            }
            
        }
        //option 2  Okoa friend
        elseif ($text == "3" ) {
            $response  = "CON Enter a friend number";
        }
        else{
            $finalOption =substr($text,0,2);

            if($finalOption == '1*'){
                // check if user is found on the users table
                $query = $this->db->get_where('users', array('phone_number' => $phoneNumber));
                if ($query->num_rows() > 0) {
                    $row = $query->row();
                    $mySavings = $row->savings;
                    $myAmount = $row->amount;
                    $textLength =strlen($text);
                    $airTimeTocken =substr($text,2,$textLength);

                    //Check if voucher number exist
                    $query = $this->db->get_where('airtime', array('airtime_tocken' => $airTimeTocken));
                    if ($query->num_rows() > 0) {
                        $row = $query->row();
                        $amount = $row->amount;
                        $finalAmount = 0.8 * $amount;
                        //If is a float value convert to int
                        $finalAmount = (int)$finalAmount;
                        $savings = $amount - $finalAmount;
                        //Calculate the final results
                        $combineAmount = $myAmount + $finalAmount;
                        $combineSavings = $mySavings + $savings; 
                        //Update final results into the database table
                        $data = array(
                                'amount' => $combineAmount,
                                'savings' => $combineSavings
                        );
                        $this->db->where('phone_number', $phoneNumber);
                        $this->db->update('users', $data);
                        //Delate voucher in airtime table
                        $this->db->delete('airtime', array('airtime_tocken' => $airTimeTocken));
                        $response = "END You recharged Ksh $amount. 20% saved into your savings. Current balance is Ksh $combineAmount. Thank you.";

                    }
                    else{
                        $response = "END Sorry! The voucher number provided does not exist. Try again with a valid voucher";
                    }
                    
                }
                else{
                    //user not found
                    $response = "END Sorry! User not registered to this pesa app.";
                }

            }
            elseif($finalOption == '3*'){

                // check if user is found on the users table
                $query = $this->db->get_where('users', array('phone_number' => $phoneNumber));
                if ($query->num_rows() > 0) {
                    $row = $query->row();
                    $mySavings = $row->savings;
                    $myAmount = $row->amount;
                    $textLength =strlen($text);
                    $friendsPhoneNumber =substr($text,2,$textLength);
                    //check if star exist

                    //process the wole text
                    $textLength =strlen($friendsPhoneNumber);
                    $amountPosStart = strpos($friendsPhoneNumber,"*");
                    if($amountPosStart > 0){
                        $amountPosStartTwo = $amountPosStart + 1;
                        $amountToBeSent = substr($friendsPhoneNumber,$amountPosStartTwo,$textLength);
                        //Friends phone number
                        $friendsPhoneNumber = substr($friendsPhoneNumber,0,$amountPosStart);
                        //echo $amountToBeSent;
                        //echo $friendsPhoneNumber. "  ". $amountToBeSent;

                        //Before update get savings and deduct amount to be sent if amount is more than the saved deny 

                        $deductedAmount = $mySavings - $amountToBeSent;
                        
                        if($deductedAmount < 0){
                            //End conection and start again insuficient funds
                            $response = "END Sorry! You have insuficient funds to complete this request.";
                        }
                        else{

                            // check if user is found on the users table
                            $query = $this->db->get_where('users', array('phone_number' => $friendsPhoneNumber));
                            if ($query->num_rows() > 0) {
                                //Update deducted amount on user table
                                $currentFriendAmount = $row->amount;
                                $data = array(
                                        'savings' => $deductedAmount
                                );
                                $this->db->where('phone_number', $phoneNumber);
                                $this->db->update('users', $data);
                                //Update added amount to be sent to a friend user
                                $amountToSendToFriend = $amountToBeSent + $currentFriendAmount;
                                $data = array(
                                        'amount' => $amountToSendToFriend
                                );
                                $this->db->where('phone_number', $friendsPhoneNumber);
                                $this->db->update('users', $data);
                                //Done End connection with a succsessfull message
                                $response = "END You sent Ksh. $amountToBeSent of your sevings to $friendsPhoneNumber. Your current savings is Ksh.$deductedAmount. Thank you";
                            }
                            else{
                                //user not found
                                $response = "END Sorry! The number you entered is not found.";

                            }

                        }
                        //Finaly update date into the table
                        
                    }
                    else{
                        // check if user is found on the users table
                        $query = $this->db->get_where('users', array('phone_number' => $friendsPhoneNumber));
                        if ($query->num_rows() > 0) {
                            $response = "CON Enter the amount of airtime you wish to send";
                        }
                        else{
                            //user not found
                            $response = "END Sorry! The number you entered is not found.";

                        }

                    }
                    
                }
                else{
                    //user not found
                    $response = "END Sorry! User not registered to this pesa app.";
                }

            }
            else{
                $response  = "END Sorry! Your not allowed to perfom this opperation.";

            }

        }
        // Print the response onto the page so that our gateway can read it
        header('Content-type: text/plain');
        echo $response;

    }

    public function addUser(){
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('name', 'Full name', 'required');
        $this->form_validation->set_rules('phone', 'Phone number', 'required');

        if ($this->form_validation->run() === FALSE)
        {
            $this->load->view('add_user');

        }
        else
        {
            $query = $this->db->get_where('users', array('phone_number' => $this->input->post('phone')));
            if (!$query->num_rows() > 0) {
                $data = array(
                    'user_name' => $this->input->post('name'),
                    'phone_number' => $this->input->post('phone'),
                    'amount' => 0,
                    'savings' => 0
                );
                $this->db->insert('users', $data);
                echo 'User added successfully';
            }
            else{
                echo 'User already exist';
            }
        }
        
    }

    public function getAllUsers(){ 
        $query = $this->db->get('users');

        foreach ($query->result() as $row)
        {
            $name = $row->user_name;
            $phone = $row->phone_number;
            echo 'Name:  '.$name. '  phone number: '. $phone. '</br>';
        }
        
    }
    
    public function getAllTockens(){ 
        
        $query = $this->db->get('airtime');

        foreach ($query->result() as $row)
        {
            $tocken = $row->airtime_tocken;
            $amount = $row->amount;
            echo 'Tocken:  '.$tocken. '  amount: '. $amount. '</br>';
        }

    }
    
    public function generateTockens(){
        
        for ($x = 0; $x <= 10; $x++) {
            
            $amount = array(20, 50,100, 200, 500, 1000);
            $amountIndex = array_rand($amount);
            $randTocken = rand(100000000000,999999999999);
            $currentAmount =$amount[$amountIndex];
            
            //add tocken to the database table
            $data = array(
                        'airtime_tocken' => $randTocken,
                        'amount' => $currentAmount
                    );
            if($this->db->insert('airtime', $data)){
                echo "Tockens generated is: $x <br>";
            }
            
        }
            
    }

}    