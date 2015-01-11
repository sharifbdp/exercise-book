<?php

class My_model extends CI_Model {

    function count_book_bundle() {

        $sql = "SELECT
                COUNT(*) AS total_bundle FROM
                (
                SELECT 
                  COUNT(books.id) AS bundle_group_list
                FROM
                  books 
                  JOIN categories 
                    ON categories.id = books.`category_id` 
                  JOIN publishers 
                    ON publishers.`id` = books.`publisher_id` 
                  JOIN writers 
                    ON writers.`id` = books.`writer_id` 
                WHERE books.is_free > 1 AND books.active=1 
                GROUP BY is_free 
                ) AS a";

        $query = $this->db->query($sql);
        return $query->result();
    }

    function get_book_bundle_lists() {

        $sql = "SELECT 

                    b.group_id AS group_id,
                    b.total_book AS total_book,
                    b.bundle_id AS bundle_id,
                    a.image AS image,
                    a.in_app_purchase_id AS in_app_purchase_id,
                    a.usd_price AS usd_price,
                    a.bd_price  AS bd_price  		

                FROM 

                ( SELECT 
                    books.`is_free` AS group_id, 
                    COUNT(books.id) AS total_book,
                    books.`is_free` AS bundle_id 
                FROM
                    books 
                JOIN categories 
                    ON categories.id = books.`category_id` 
                JOIN publishers 
                    ON publishers.`id` = books.`publisher_id` 
                JOIN writers 
                    ON writers.`id` = books.`writer_id` 
                WHERE books.is_free > 1 AND books.active=1
                GROUP BY is_free
                ORDER BY is_free ) AS b

                LEFT OUTER JOIN
                    `book_bundle` AS a ON a.`id`=b.group_id";

        $query = $this->db->query($sql);
        return $query->result();
    }

    function get_book_id_list_by_bundle_id($id) {
        $this->db->from('books_book_bundle');
        $this->db->where("bundle_id", $id);

        $query = $this->db->get()->result();
        return $query;
    }

    function get_book_details_by_id($id) {
        $this->db->from('books');
        $this->db->where("id", $id);

        $query = $this->db->get()->result();
        return $query;
    }

}
