<?php
// Our custom post type function

function create_posttype_rasadin() {
 
    register_post_type( 'cp007',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'CustomPost&Other' ),
                'singular_name' => __( 'cp007' ),
                // 'add_new' =>  __('ADD NEW CUSTOM POST')
            ),
            'public'             => true,
            'has_archive'        => true,
            'taxonomies'         => array('category','post_tag'),
            'rewrite'            => array('slug' => 'Name Custom Post Types'),
            'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
            'menu_icon'          => 'dashicons-editor-kitchensink',
       
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype_rasadin' );





// WP query for frontend view(customs posts)
add_shortcode('yyyccc', 'custom_post_shortcode_and_WPquery'); //yyyccc is shortcode for this function
function custom_post_shortcode_and_WPquery()
{
    $args = array(
        'post_type'=> 'cp007'
    );
    
    $query = new WP_Query($args);
    $html = '<div>';
    // var_dump($query->posts);
    foreach( $query->posts as $post ) {
        $like = get_post_meta($post->ID, 'cpt_post_like', true);
        $html .= '
        
            <h2>'.$post->post_title.'</h2>
            <p>'.$post->post_content.'</p>

            <p> Author ID: </p>
            <?php 
            <p>'.$post->post_author.'</p>


            <p> Date and Time: </p>
            <?php 
            <p>'.$post->post_date.'</p>

            <p>'.$post->post_excerpt.'</p>
            <button type="button" id="js-add-like" data-id="'.$post->ID.'">Like <span class="like-count">'.$like.'</span></button>
            <br>
            <?php 
        ';
    }
    $html .= '</div>';
    return $html;
}





// add_shortcode( 'test', 'test_shortcode' );
// function test_shortcode() {

//     $html = '<h1>Test Shortcode</h1>';

//     $html .= '<h2>This is 2nd paragraph</h2>';

//     return $html;

// }


//code for frontpage show (custom posts)
add_action('pre_get_posts', 'frontpageShow');
function frontpageShow($query)
{
    if (is_home() && $query->is_main_query()) {

    $query-> set('post_type',array('post','cp007') ); //'post' for word press main posts and 'cp007' for custom posts
   

return $query;
   }
}
//code for frontpage show (custom posts)





///this code is for custom registration
add_shortcode('form_reg', 'custom_form_reg');
function custom_form_reg() { 
    global $wpdb;

    if ($_POST) {

        $username = $wpdb->escape($_POST['txtUsername']);
        $email = $wpdb->escape($_POST['txtEmail']);
        $password = $wpdb->escape($_POST['txtPassword']);
        $ConfPassword = $wpdb->escape($_POST['txtConfirmPassword']);

        $error = array();
        if (strpos($username, ' ') !== FALSE) {
            $error['username_space'] = "Username has Space";
        }

        if (empty($username)) {
            $error['username_empty'] = "Needed Username must";
        }

        if (username_exists($username)) {
            $error['username_exists'] = "Username already exists";
        }

        if (!is_email($email)) {
            $error['email_valid'] = "Email has no valid value";
        }

        if (email_exists($email)) {
            $error['email_existence'] = "Email already exists";
        }

        if (strcmp($password, $ConfPassword) !== 0) {
            $error['password'] = "Password didn't match";
        }

        if (count($error) == 0) {

            wp_create_user($username, $password, $email);
            echo "User Created Successfully";
            exit();
        }else{
            
            print_r($error);
            
        }
    }
    $html = '
    <form method="post" >  

        <p>
        <label>Username</label>
        <div>
        <input type="text" name="txtUsername" id="txtUsername" placeholder= "Enter username"/>
        </div>
        </p>

        <p>
        <label>Email</label>
        <div>
        <input type="email" name="txtEmail" id="txtEmail" placeholder= "Enter email"/>
        </div>
        </p>

        <p>
        <label>Password</label>
        <input type="password" id= "txtPassword" name="txtPassword" placeholder= "Enter password"/>
        </p>

        <p>
        <label>Password Confirm</label>
        <input type="password" id= "txtConfirmPassword" name="txtConfirmPassword" placeholder= "Enter password again"/>
        </p>


        <p>
        <button type= "submit" name="btn_submit"> Sign Up </button>
        </p>

    </form>
    ';
    return $html;
}
//this code is for user custom registration



///this code is for custom login
add_shortcode('form_login', 'custom_form_login');
function custom_form_login() { 
    global $user_ID;
    global $wpdb;
    if(!$user_ID){
       if ($_POST) {
           $username = $wpdb->escape($_POST['username']);
           $password = $wpdb->escape($_POST['password']);


           $login_array = array();
           $login_array['user_login'] = $username;
           $login_array['user_password'] = $password;

           $verify_user= wp_signon($login_array,true);

           if(!is_wp_error($verify_user)){
               echo "<script>window.location=' ".site_url()."'</script>"; 

           }
           else {
            echo "<p>Invalid</p>";
           }
        }
        else{ 

         $html = '
         <form method="post" >  
    
         <p>
         <label>Username</label>
         <div>
         <input type="text" name="username" id="username" placeholder= "Enter username/email"/>
         </div>
         </p>
    
         <p>
         <label>Password</label>
         <input type="password" id= "password" name="password" placeholder= "Enter password"/>
         </p>

         <p>
         <button type= "submit" name="btn_submit"> Log In </button>
         </p>
    
         </form>
         ';
    return $html;
    }

}
}
//this code is for user custom login

//this code is for post auto reload without page reload
add_action('wp_ajax_load_posts_by_ajax', 'load_posts_by_ajax_callback');
add_action('wp_ajax_nopriv_load_posts_by_ajax', 'load_posts_by_ajax_callback');

function load_posts_by_ajax_callback() {
    check_ajax_referer('load_more_posts', 'security');
    $paged = $_POST['page'];
    $args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => '2',
        'paged' => $paged,
    );
    $my_posts = new WP_Query( $args );
    if ( $my_posts->have_posts() ) :
        ?>
        <?php while ( $my_posts->have_posts() ) : $my_posts->the_post(); ?>
            <h2><?php the_title(); ?></h2>
            <?php the_excerpt(); ?>
        <?php endwhile; ?>
        <?php
    endif;
 
    wp_die();
}
//this code is for post auto reload without page reload

