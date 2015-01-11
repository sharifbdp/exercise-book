<?php

session_start();
ob_start();
//header('Content-Type: text/html; charset=utf-8');
header('Content-Type: application/json; charset=utf-8');
$con = mysqli_connect("localhost", "root", "root", "bbh_website");
//$con = mysqli_connect("localhost", "mobioapp_appuser", "Mobi@13#", "mobioapp_bbh_website_beta");
$con->set_charset("utf8");
$con->query('SET SQL_BIG_SELECTS=1'); 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
if (!$con) {
    die('Could not connect: ' . mysql_error());
}

if (!empty($_POST['pemail'])) {
    $pemail = $_POST['pemail'];
} else {
    $pemail = "0";
}
?>

<?php

// tag_type = 0 new book
// tag_type = 1 not a new book
//get bundle list..
$get_bundle_count = "SELECT

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


$bundle_count = mysqli_query($con, $get_bundle_count) or die(mysql_error());
$result = array();


while ($row = mysqli_fetch_object($bundle_count)) {
    //echo "<pre>";print_r($row);
    if ($row->total_bundle > 0) {
        $result['bundle'] = "Yes";
    } else {

        $result['bundle'] = "No";
    }
    //$i++;
}

$i = 0;

$get_bundle_lists_detail = "
SELECT 

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

$bundle_list = mysqli_query($con, $get_bundle_lists_detail) or die(mysql_error());
while ($row = mysqli_fetch_object($bundle_list)) {
    $result['Bundle'][$i]['total_book'] = $row->total_book;
    $result['Bundle'][$i]['bundle_id'] = $row->bundle_id;
    $result['Bundle'][$i]['image'] = $row->image;
    $result['Bundle'][$i]['in_app_purchase_id'] = $row->in_app_purchase_id;
    $result['Bundle'][$i]['usd_price'] = $row->usd_price;
    $result['Bundle'][$i]['bd_price'] = $row->bd_price;
    $result['Bundle'][$i]['is_purchases'] = 0;
    $i++;
}


$get_books_lists = "SELECT

  c.id AS id,
  c.category_id AS category_id,
  c.publisher_id AS publisher_id,
  c.writer_id AS writer_id,
  c.title AS title,
  c.ISBN AS ISBN,
  c.front_image AS front_image,
  c.purchase_image AS purchase_image,
  c.`is_free` AS is_free,
  c.`is_downloadable` AS is_downloadable,
  c.`file_name` AS file_name,
  c.`is_archive` AS is_archive,
  c.`in_app_purchased_id` AS in_app_purchased_id,
  c.`last_read_page_no` AS last_read_page_no,

  c.`created_date` AS created_date,
  c.`update_date` AS update_date,
  c.`e_publish_date` as e_publish_date, 
  c.`price` AS price,
  c.`bd_price` AS bd_price,
  c.`total_pages` AS total_pages,
  c.`tag_image` AS tag_image,
  c.`store_product_id` AS store_product_id,
  c.`inactive_image` AS inactive_image,
  c.`preview_image_1` AS preview_image_1,
  c.`preview_image_2` AS preview_image_2,
  c.`preview_image_3` AS preview_image_3,
  c.category_name AS category_name,
  c.writer_name AS writer_name,
  c.publisher_name AS publisher_name,
  c.rating AS rating,
  
  c.google_purchase,
  c.activation_purchase,
  c.bkash_purchase,
  
  d.image AS parent_image,
  d.in_app_purchase_id AS parent_in_app_purchase_id

FROM 
    (SELECT 
      books.id AS id,
      books.`category_id` AS  category_id,
      books.`publisher_id` AS  publisher_id,
      books.`writer_id` AS writer_id,
      books.`title` AS title,
      books.`ISBN` AS ISBN,
      books.`front_image` AS front_image,
      books.`purchase_image` AS purchase_image,
      books.`is_free` AS is_free,
      books.`is_downloadable` AS is_downloadable,
      books.`file_name` AS file_name,
      books.`is_archive` AS is_archive,
      books.`in_app_purchased_id` AS in_app_purchased_id,
      books.`last_read_page_no` AS last_read_page_no,
      IF(books.`created_date` IS NULL,DATE_FORMAT(NOW(),'%Y-%m-%d %h:%i:%S'),books.`created_date`) AS created_date,
      books.`update_date` AS update_date,
      books.`e_publish_date` as e_publish_date, 
      books.`price` AS price,
      books.`bd_price` AS bd_price,
      books.`total_pages` AS total_pages,
      books.`tag_image` AS tag_image,
      books.`store_product_id` AS store_product_id,
      books.`inactive_image` AS inactive_image,
      books.`preview_image_1` AS preview_image_1,
      books.`preview_image_2` AS preview_image_2,
      books.`preview_image_3` AS preview_image_3,
      categories.`title` AS category_name,
      writers.`name` AS writer_name,
      publishers.`name` AS publisher_name,
      
      g_sale.id as google_purchase,
      activation_sale.id as activation_purchase,
      bkash_sale.id as bkash_purchase,
      
      IF(b.`rating` IS NULL,0,b.`rating`) AS rating

        FROM
            books 
        JOIN categories 
          ON categories.id = books.`category_id` 
        JOIN publishers 
          ON publishers.`id` = books.`publisher_id` 
        JOIN writers 
          ON writers.`id` = books.`writer_id` 
        LEFT OUTER JOIN (SELECT book_id AS book_id,MAX(rating) AS rating  FROM `book_rating` GROUP BY book_id) AS b ON b.`book_id`=`books`.`id`
        
        LEFT JOIN `sales_detail` AS g_sale ON g_sale.`pbook_id`= books.id AND g_sale.device_user_email = '{$pemail}'
        LEFT JOIN `book_bundle_sales` AS activation_sale ON activation_sale.`book_id`= books.id AND activation_sale.user_mail = '{$pemail}'
        LEFT JOIN `payment_history` AS bkash_sale ON bkash_sale.`book_id`= books.id AND bkash_sale.user_email = '{$pemail}'
            
        WHERE books.active=1  
    ) AS c
       
       LEFT OUTER JOIN `book_bundle` AS d ON d.`id`=c.is_free 
       
       ORDER BY c.id ASC
       ";

$books_list = mysqli_query($con, $get_books_lists) or die(mysql_error());
$i = 0;
$result['items'] = mysqli_affected_rows($con);
while ($row = mysqli_fetch_object($books_list)) {
    $result['Books'][$i]['book'] = $row;
    $week_3 = date('Y-m-d', strtotime("-3 week"));

    if (( strtotime($week_3) <= strtotime($row->e_publish_date)) && ( strtotime(date("Y-m-d")) >= strtotime($row->e_publish_date))) {
        if (!empty($row->e_publish_date)) {
            $result['Books'][$i]['book']->tag_type = 0;
        } else {
            $result['Books'][$i]['book']->tag_type = 1;
        }
    } else {
        $result['Books'][$i]['book']->tag_type = 1;
    }
    
    if(!empty($row->activation_purchase) || !empty($row->google_purchase) || !empty($row->bkash_purchase)){
       $result['Books'][$i]['book']->is_purchases = 1; 
    }else{
       $result['Books'][$i]['book']->is_purchases = 0; 
    }
    
    $categories = mysqli_query($con, "SELECT * FROM categories WHERE categories.id = " . $row->category_id) or die(mysql_error());
    $result['Books'][$i]['category'] = mysqli_fetch_object($categories);
    $publishers = mysqli_query($con, "SELECT * FROM publishers WHERE publishers.id = " . $row->publisher_id) or die(mysql_error());
    $result['Books'][$i]['publisher'] = mysqli_fetch_object($publishers);
    $writers = mysqli_query($con, "SELECT * FROM writers WHERE writers.id = " . $row->writer_id) or die(mysql_error());
    $result['Books'][$i]['writer'] = mysqli_fetch_object($writers);
    $i++;
}

mysqli_close($con);

echo json_encode($result);
?>
