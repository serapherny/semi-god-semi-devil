<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/photo.php';
require_once LIB.'ent/user.php';
require_once LIB.'ent/post.php';
require_once LIB.'ent/comment.php';
require_once LIB.'ent/item.php';
require_once CONTROLLER.'base/abstract_page.php';

class Test_init extends AbstractPage {
  private
    $username_to_id = array(),
    $photoname_to_id = array(),
    $postname_to_id = array(),
    $itemname_to_id = array();

  protected function create_test_user($name, $profile_photo_id) {
    $user = new User();
    $user->new_sid()->set('nickname', $name)
         ->set('password', '1234')
         ->set('email_addr', $name);
    $this->user_model->create_user($user);
    $this->username_to_id[$name] = $user->get('sid');
    return <p>Created new User {$name}</p>;
  }

  protected function create_test_photo($photo_name, $user_name) {
    $photo = new Photo();
    $binary = base64_encode(
        read_file(CONTROLLER.'internal/test_photos/'.$photo_name)
    );
    $photo->new_sid()->set('file_ext', '.jpg')
          ->set('binary', $binary)
          ->set('author', $this->username_to_id[$user_name]);
    $this->photo_model->upload_photo($photo);
    $this->photoname_to_id[$photo_name] = $photo->get('sid');
    return <p>Created new Photo {$photo_name} for {$user_name}</p>;
  }

  protected function create_test_item($item_name, $photo_name, $user_name) {
    $item = new Item();
    $item->new_sid()->set('author', $this->username_to_id[$user_name])
         ->set('photolist', $this->photoname_to_id[$photo_name]);
    $this->item_model->insert($item);
    $this->itemname_to_id[$item_name] = $item->get('sid');
    return <p>Created new Item {$item_name} using photo {$photo_name} for {$user_name}</p>;
  }

  protected function create_test_item_post($post_name, $item_name, $user_name) {
    $post = new Post();
    $post->new_sid()->set('author', $this->username_to_id[$user_name])
         ->set('content_type', 'item')
         ->set('content', $this->itemname_to_id[$item_name]);
    $this->post_model->insert($post);
    $this->postname_to_id[$post_name] = $post->get('sid');
    return <p>Created new Post {$post_name} ({$post->get('sid')}) for Item {$item_name} as {$user_name}</p>;
  }

  protected function create_test_comment($comment_content,
                                         $post_name, $user_name) {

  }

  public function test_set_1() {
    $output = <div/>;
    $this->load->model('ent/user_model', 'user_model');

    $output->appendChild($this->create_test_user('test-Zhen', 0));
    $output->appendChild($this->create_test_user('test-Cici', 0));

    $this->load->model('ent/photo_model', 'photo_model');

    $output->appendChild($this->create_test_photo('plant.jpg', 'test-Zhen'));
    $output->appendChild($this->create_test_photo('chrome.jpg', 'test-Zhen'));
    $output->appendChild($this->create_test_photo('evernote.jpg', 'test-Cici'));

    $this->load->model('ent/item_model', 'item_model');
    $output->appendChild($this->create_test_item('Plant', 'plant.jpg', 'test-Zhen'));
    $output->appendChild($this->create_test_item('Chrome', 'chrome.jpg', 'test-Cici'));
    $output->appendChild($this->create_test_item('Evernote', 'evernote.jpg', 'test-Cici'));

    $this->load->model('ent/post_model', 'post_model');
    $output->appendChild($this->create_test_item_post('show plant', 'Plant', 'test-Zhen'));
    $output->appendChild($this->create_test_item_post('show chrome', 'Chrome', 'test-Cici'));
    $output->appendChild($this->create_test_item_post('show evernote', 'Evernote', 'test-Cici'));
    $output->appendChild($this->create_test_item_post('show plant2', 'Plant', 'test-Cici'));
    $output->appendChild($this->create_test_item_post('show chrome2', 'Chrome', 'test-Zhen'));
    $output->appendChild($this->create_test_item_post('show evernote2', 'Evernote', 'test-Zhen'));

    return $output;
  }

  public function index() {
    echo <ui:page_skeleton>
           {$this->getHeader()}
           {$this->test_set_1()}
         </ui:page_skeleton>;
  }
}

/* End of file photo_monitor.php */
/* Location: ./application/controllers/internal/photo_monitor.php */