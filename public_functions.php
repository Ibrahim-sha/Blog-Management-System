<?php 
 include('config.php');
/* * * * * * * * * * * * * * *
* Returns all published posts
* * * * * * * * * * * * * * */
/* * * * * * * * * * * * * * * *
* Returns all published posts
* * * * * * * * * * * * * * */
function getPublishedPosts() {
        // use global $conn object in function
        global $conn;
        $sql = "SELECT * FROM posts WHERE published=true";
        $result = mysqli_query($conn, $sql);
    
        if ($result) {
            // Check if the query was successful
            $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
            $final_posts = array();
            foreach ($posts as $post) {
                $post['topic'] = getPostTopic($post['id']); 
                array_push($final_posts, $post);
            }
            return $final_posts;
        } else {
            // Handle the query failure here, e.g., log an error or return an empty array
            return array();
        }
    }
    
    // Modify other functions in a similar way to handle query results.
    
/* * * * * * * * * * * * * * *
* Receives a post id and
* Returns topic of the post
* * * * * * * * * * * * * * */
function getPostTopic($post_id){
        global $conn;
        $sql = "SELECT * FROM topic WHERE id=
                        (SELECT topic_id FROM post_topic WHERE post_id=$post_id) LIMIT 1";
        $result = mysqli_query($conn, $sql);
        $topic = mysqli_fetch_assoc($result);
        return $topic;
}
/* * * * * * * * * * * * * * * *
* Returns all posts under a topic
* * * * * * * * * * * * * * * * */
/* * * * * * * * * * * * * * * *
* Returns all posts under a topic
* * * * * * * * * * * * * * * * */
function getPublishedPostsByTopic($topic_id) {
    global $conn;
    $sql = "SELECT * FROM posts ps 
            WHERE ps.id IN 
            (SELECT pt.post_id FROM post_topic pt 
                    WHERE pt.topic_id=$topic_id GROUP BY pt.post_id 
                    HAVING COUNT(1) = 1)";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Check if the query was successful
        $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $final_posts = array();
        foreach ($posts as $post) {
            $post['topic'] = getPostTopic($post['id']);
            array_push($final_posts, $post);
        }
        return $final_posts;
    } else {
        // Handle the query failure here, e.g., log an error or return an empty array
        return array();
    }
}

/* * * * * * * * * * * * * * * *
* Returns topic name by topic id
* * * * * * * * * * * * * * * * */
function getTopicNameById($id)
{
        global $conn;
        $sql = "SELECT name FROM topics WHERE id=$id";
        $result = mysqli_query($conn, $sql);
        $topic = mysqli_fetch_assoc($result);
        return $topic['name'];
}
/* * * * * * * * * * * * * * *
* Returns a single post
* * * * * * * * * * * * * * */
function getPost($slug){
        global $conn;
        // Get single post slug
        $post_slug = $_GET['post-slug'];
        $sql = "SELECT * FROM posts WHERE slug='$post_slug' AND published=true";
        $result = mysqli_query($conn, $sql);

        // fetch query results as associative array.
        $post = mysqli_fetch_assoc($result);
        if ($post) {
                // get the topic to which this post belongs
                $post['topic'] = getPostTopic($post['id']);
        }
        return $post;
}
/* * * * * * * * * * * *
*  Returns all topics
* * * * * * * * * * * * */
function getAllTopics()
{
        global $conn;
        $sql = "SELECT * FROM topics";
        $result = mysqli_query($conn, $sql);
        $topics = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $topics;
}
?>