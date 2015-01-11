<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Welcome extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model(array('My_model', ''), '', TRUE);
    }

    public function index() {

        $bundle_count = $this->My_model->count_book_bundle();

        // echo '<pre>', print_r($bundle_count[0]);

        $result = array();

        if ($bundle_count[0]->total_bundle > 0) {
            $result['bundle'] = "Yes";
        } else {
            $result['bundle'] = "No";
        }

        $bundle_list = $this->My_model->get_book_bundle_lists();

        $i = 0;
        foreach ($bundle_list as $b) {
            $result['Bundle'][$i]['total_book'] = $b->total_book;
            $result['Bundle'][$i]['bundle_id'] = $b->bundle_id;
            $result['Bundle'][$i]['image'] = $b->image;
            $result['Bundle'][$i]['in_app_purchase_id'] = $b->in_app_purchase_id;
            $result['Bundle'][$i]['usd_price'] = $b->usd_price;
            $result['Bundle'][$i]['bd_price'] = $b->bd_price;
            $result['Bundle'][$i]['is_purchases'] = 0;
            
            $book_ids = $this->My_model->get_book_id_list_by_bundle_id($b->bundle_id);
            $j = 0;
            foreach ($book_ids as $id){
                $book_details = $this->My_model->get_book_details_by_id($id->book_id);
                $result['Bundle'][$i]['Book'][$j] = $book_details;
                
                $j++;
            }
            
            $i++;
        }


        echo '<pre>', print_r($result);
    }

}
