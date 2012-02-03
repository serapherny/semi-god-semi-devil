<div class="single-fall" style="width: <?php echo $fall_width;?>px;">
  <?php foreach($content as $sub_content) { 
          $this->load->view('base/div', array('content'=> $sub_content));  
        }?>
</div>