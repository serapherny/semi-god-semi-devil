<div class="<?php if (isset($class)) echo $class;?>">

<?php if (is_array($content)){
        foreach($content as $sub_content) { ?>
          <div class="<?php if (isset($sub_class)) echo $sub_class;?>">
          <?php echo $sub_content;?>
          </div>
<?php   }
      } else {
        echo $content;
      }?>

</div>